<?php

namespace App\Http\Controllers;

use App\aaa\g;
use App\aaa\Transformers\VoucherParametersTransformer;
use App\Http\Controllers\ApiController;
use App\Http\Models\Business;
use App\Http\Models\VoucherParameter;
use App\Http\Requests\Vouchers\VoucherParameters\CreateVoucherParametersRequest;
use App\Http\Requests\Vouchers\VoucherParameters\UpdateVoucherParametersRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use \Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VoucherParametersController extends ApiController
{
    /**
     *  instance of VoucherParametersTransformer class
     * @var object
     */
    private $voucherParameterTransformer;
    
    /**
     * instance of g class
     * @var object
     */
    private $g;


    public function __construct(
            VoucherParametersTransformer $voucher_parameter_transformer,
            g $g
            ) {
//        Apply the jwt.auth middleware to all methods in this controller
        $this->middleware('jwt.auth');
        $this->voucherParameterTransformer = $voucher_parameter_transformer;
        $this->g = $g;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($voucher_type)
    {
        $voucher_type_secured = (string)$voucher_type;
        
        $voucher_parameters_objects = VoucherParameter::where('voucher_type', $voucher_type_secured)->get();
        
        return $this->respond($this->voucherParameterTransformer->transformCollection($voucher_parameters_objects->toArray()));
    }
    
    /**
     * Search for voucher parameters by title
     * @param string $voucher_title
     * @return collection
     */
    public function searchByVoucherTitle( $voucher_title) {
        $optimized_voucher_title = strtolower(urldecode($voucher_title));
        $voucher_parameter_exist = VoucherParameter::where('title', 'like', '%'.$optimized_voucher_title.'%')->exists();
        
        if ( !$voucher_parameter_exist ) {
            return $this->respond(["data"=>"No matched result"]);
        }//if ( !$voucher_parameter_exist )
        
        $voucher_parameter_objects = VoucherParameter::where('title', 'like', '%'.$optimized_voucher_title.'%')->get()->toArray();
        
        return $this->voucherParameterTransformer->transformCollection($voucher_parameter_objects);
    }
    
    /**
     * Search for voucher parameters by business name
     * @param string $business_name
     * @return Json collection
     */
    public function searchByBusinessName( $business_name) {
        $optimized_business_name = strtolower(urlencode($business_name));
        $business_exist = Business::where('business_name', 'like', '%'.$optimized_business_name.'%')->exists();
        if ( !$business_exist ) {
            return $this->respond(["data"=>"No matched result"]);
        }//if ( !$business_exist )
        $business_object = Business::where('business_name', 'like', '%'.$optimized_business_name.'%')->first(['id']);
        $voucher_parameter_objects = VoucherParameter::where('business_id', $business_object->id)->get()->toArray();
        return $this->voucherParameterTransformer->transformCollection($voucher_parameter_objects);
    }

    /**
     * Store Deal voucher parameters
     * @param CreateVoucherParametersRequest $request
     * @return Json response
     */
    public function storeDealVoucherParameters(CreateVoucherParametersRequest $request ) {
        $raw_input= $request->get("data");
        $raw_input['voucher_type'] = 'deal';
        return $this->generalStoreHelper( $raw_input );
    }
    
    /**
     * Store Gift voucher patameters
     * @param CreateVoucherParametersRequest $request
     * @return Json response
     */
    public function storeGiftVoucherParameters( CreateVoucherParametersRequest $request) {
       $raw_input = $request->get("data") ;
       $raw_input['voucher_type'] = 'gift';
       return $this->generalStoreHelper( $raw_input );
    }

    /**
     * Display a voucher by ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        try{
            $voucher_parameter_object = VoucherParameter::findOrFail($id);
        } catch (Exception $ex) {
                throw new Exception($ex->getMessage(), $ex->getCode());
        }//catch (\Exception $ex)
        return $this->respond($this->voucherParameterTransformer->transform($voucher_parameter_object->toArray()));
    }
    
//    todo create showGiftVoucherParameters method
//    todo Create showDealVoucherParameters method

    public function updateGiftVoucherParameters( UpdateVoucherParametersRequest $request) {
        $raw_input = $request->json("data");
        return $this->generalUpdateHelper($raw_input);
    }
    
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //todo perform destroy method
    }
    
//    Helper Methods
    
    /**
     * General Store a newly created resource in storage.
     *
     * @param  array  $raw_input
     * @return Response
     */
    public function generalStoreHelper( array $raw_input ) {
        $this->storeValidationHelper($raw_input);
        $input = $this->prepareDataForStoring($raw_input);
        DB::beginTransaction();
        $created_voucher_parameters = VoucherParameter::create( $input );
        if(  is_object( $created_voucher_parameters)){
        $created_voucher_parameters->useTerms()->attach($input['use_terms']);
        DB::commit();
            $response =  $this->voucherParameterTransformer->transform( $created_voucher_parameters->toArray());
        }else{
            DB::rollBack();
            $response = $this->respondInternalError();
        }//if(is_object( $created_voucher_parameters))
        return $response;
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function generalUpdateHelper(array $raw_input)
    {
        $voucher_parameter_object = VoucherParameter::findOrFail($raw_input['id']);
        $modified_input = $this->prepareDataForUpdating($raw_input);
        DB::beginTransaction();
        if($voucher_parameter_object->update($modified_input)){
            $voucher_parameter_object->useTerms()->sync($modified_input['use_term_ids']);
            DB::commit();
            return $voucher_parameter_object;
        }//if($voucher_parameter_object->update($input))
        DB::rollBack();
        return $this->respondBadRequest('Something went wrong while updating voucher');
    }
    
    /**
     * perform common sanitization for all vouchers
     * @param array $old_input
     * @return array
     */
    private function prepareDataForStoring( array $old_input) {
        $modified_input['business_id'] = (int)$old_input['relations']['business']['data']['business_id'];
        $modified_input['user_id'] = (int)$old_input['relations']['user']['data']['user_id'];
        $modified_input['voucher_image_id'] = (int)$old_input['relations']['voucher_image']['data']['voucher_image_id'];
        $modified_input['use_terms'] = (array)$old_input['relations']['use_terms']['data']['use_term_ids'];
        if ( isset( $old_input[ 'purchase_start' ] ) && !empty( $old_input[ 'purchase_start' ] ) ) {
            $modified_input[ 'purchase_start' ] = g::utcDateTime( $old_input[ 'purchase_start' ], 'd/m/Y H:i' );
        }else {
            $auckland_now_to_utc     = Carbon::now( 'Pacific/Auckland' )->setTimezone( 'UTC' );
            $modified_input[ 'purchase_start' ] = $auckland_now_to_utc;
        }//if (isset($input['purchase_start'])&&!empty($input['purchase_start']))
        if ( !empty( $old_input[ 'purchase_expiry' ] ) ) {
            $modified_input[ 'purchase_expiry' ] = g::utcDateTime( $old_input[ 'purchase_expiry' ], 'd/m/Y H:i' );
        }//if ( !empty($input['purchase_expiry']) )
        $modified_input[ 'is_expire' ] = 0;
        $modified_input[ 'is_display' ] = 1;
        $modified_input[ 'is_purchased' ] = 0;
        if ( !empty( $old_input[ 'valid_from' ] ) ) {
            $modified_input[ 'valid_from' ] = g::utcDateTime( $old_input[ 'valid_from' ], 'd/m/Y H:i' );
        }//if ( !empty($input['valid_from']) )
        if ( !empty( $old_input[ 'valid_until' ] ) ) {
            $modified_input[ 'valid_until' ] = g::utcDateTime( $old_input[ 'valid_until' ], 'd/m/Y H:i' );
        }//if ( !empty($input['valid_until']) )
//        secure fields from XSS attack
        $modified_input[ 'short_description' ] = (isset( $old_input[ 'short_description' ] )) ? preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $old_input[ 'short_description' ] ) : '';
        $modified_input[ 'long_description' ] = (isset( $old_input[ 'long_description' ] )) ? preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $old_input[ 'long_description' ] ) : '';
        return $modified_input;
    }

    /**
     * Store Validation helper for important data of each type of vouchers
     * @param array $input
     * @return boolean
     * @throws Exception
     */
    private function storeValidationHelper( array $input) {
//        todo move this method to be a validate rule in CreateVoucherParametersRequest class
        switch ( $input['voucher_type']) {
            case 'gift':
                if ( empty($input['min_value']) || empty($input['max_value']) ) {
                    throw new Exception('min_value and max_value must exist', 417);
                }//if ( empty($input['min_value']) || empty($input['max_value']) )
                break;
            case 'deal':
                if ( empty($input['retail_value']) || empty($input['value']) ) {
                    throw new Exception('retail_value and value must exist', 417);
                }//if ( empty($input['retail_value']) || empty($input['value']) )
                break;
            case 'birthday':
                if ( empty($input['value'])) {
                    throw new Exception('value must exist', 417);
                }//if ( empty($input['value']))
                break;
            case 'discount':
                if ( empty($input['discount_percentage'])) {
                    throw new Exception('discount_percentage must exist', 417);
                }//if ( empty($input['value']))
                break;
            case 'concession':
                if ( empty($input['retail_value']) || empty($input['value']) ) {
                    throw new Exception('retail_value and value must exist', 417);
                }//if ( empty($input['retail_value']) || empty($input['value']) )
                break;
        }//switch ( $input['voucher_type'])
        return TRUE;
    }
    
    /**
     * Modify data to be updated
     * @param array $raw_input
     * @return array
     */
    private function prepareDataForUpdating( $raw_input ) {
        ($business_id = $this->g->arrayKeySearchRecursively($raw_input, 'business_id')) ? $modified_input['business_id'] = (int)$business_id : FALSE;
        ($user_id = $this->g->arrayKeySearchRecursively($raw_input, 'user_id')) ? $modified_input['user_id'] = (int)$user_id : FALSE;
        ($voucher_image_id = $this->g->arrayKeySearchRecursively($raw_input, 'voucher_image_id')) ? $modified_input['voucher_image_id'] = (int)$voucher_image_id : FALSE;
        ($use_term_ids = $this->g->arrayKeySearchRecursively($raw_input, 'use_term_ids')) ? $modified_input['use_term_ids'] = array_map('intval', $use_term_ids) : FALSE;
        ($title = $this->g->arrayKeySearchRecursively($raw_input, 'title')) ? $modified_input['title'] = (string)$title:false;
        ($is_expire = $this->g->arrayKeySearchRecursively($raw_input, 'is_expire')) ? $modified_input['is_expire'] = (string)$is_expire : FALSE;
        ($is_display = $this->g->arrayKeySearchRecursively($raw_input, 'is_display')) ? $modified_input['is_display'] = (string)$is_display : FALSE;
        ($valid_for_amount = $this->g->arrayKeySearchRecursively($raw_input, 'valid_for_amount')) ? $modified_input['valid_for_amount'] = (int)$valid_for_amount : FALSE;
        ($valid_for_units = $this->g->arrayKeySearchRecursively($raw_input, 'valid_for_units')) ? $modified_input['valid_for_units'] = (string)$valid_for_units : FALSE;
        ($quantity = $this->g->arrayKeySearchRecursively($raw_input, 'quantity')) ? $modified_input['quantity' ] = (int)$quantity : FALSE;
        ($no_of_uses = $this->g->arrayKeySearchRecursively($raw_input, 'no_of_uses')) ? $modified_input['no_of_uses'] = (int)$no_of_uses : FALSE;
        ($retail_value = $this->g->arrayKeySearchRecursively($raw_input, 'retail_value')) ? $modified_input['retail_value'] = (double)$retail_value : FALSE;
        ($value = $this->g->arrayKeySearchRecursively($raw_input, 'value')) ? $modified_input['value'] = (double)$value : FALSE;
        ($min_value = $this->g->arrayKeySearchRecursively($raw_input, 'min_value')) ? $modified_input['min_value'] = (double)$min_value : FALSE;
        ($max_value = $this->g->arrayKeySearchRecursively($raw_input, 'max_value')) ? $modified_input['max_value'] = (double)$max_value : FALSE;
        ($discount_percentage = $this->g->arrayKeySearchRecursively($raw_input, 'discount_percentage')) ? $modified_input['discount_percentage'] = (double)$discount_percentage : FALSE;
        ($purchase_start = $this->g->arrayKeySearchRecursively($raw_input, 'purchase_start')) ? $modified_input['purchase_start'] = g::utcDateTime( $purchase_start, 'd/m/Y H:i' ) : FALSE;
        ($purchase_expiry = $this->g->arrayKeySearchRecursively($raw_input, 'purchase_expiry')) ? $modified_input['purchase_expiry'] = g::utcDateTime($purchase_expiry, 'd/m/Y H:i') : FALSE;
        ($valid_from = $this->g->arrayKeySearchRecursively($raw_input, 'valid_from')) ? $modified_input['valid_from'] = g::utcDateTime($valid_from, 'd/m/Y H:i') : FALSE;
        ($valid_until = $this->g->arrayKeySearchRecursively($raw_input, 'valid_until')) ? $modified_input['valid_until'] = g::utcDateTime($valid_until, 'd/m/Y H:i') : false;
        //        secure fields from XSS attack
         ($short_description = $this->g->arrayKeySearchRecursively($raw_input, 'short_description') ) ? $modified_input[ 'short_description' ] = preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $short_description ) : False;
         ($long_description = $this->g->arrayKeySearchRecursively( $raw_input, 'long_description')) ? $modified_input[ 'long_description' ] = preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $long_description ) : '';
        
        
//        todo take decision about working with (valid_for_amount and valid_for_units) or (valid_from and valid_until) or working with both
        
        return $modified_input;
    }
    
    
}
