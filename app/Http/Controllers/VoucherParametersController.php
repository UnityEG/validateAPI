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
    
    /**
     * instance of VoucherParameter Model class
     * @var object
     */
    private $voucherParameterModel;


    public function __construct(
            VoucherParametersTransformer $voucher_parameter_transformer,
            g $g,
            VoucherParameter $voucher_parameter_model
            ) {
//        Apply the jwt.auth middleware to all methods in this controller
        $this->middleware('jwt.auth');
        $this->voucherParameterTransformer = $voucher_parameter_transformer;
        $this->g = $g;
        $this->voucherParameterModel = $voucher_parameter_model;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $voucher_parameters_objects = VoucherParameter::all();
        return $this->respond($this->voucherParameterTransformer->transformCollection($voucher_parameters_objects->toArray()));
    }
    
    /**
     * List all active vouchers parameters method
     * active voucher parameters means that is_expiry=0 and is_display=1
     * @return Json response
     */
    public function listAllActiveVouchersParameters( ) {
        $active_vouchers_parameters_arrays = $this->voucherParameterModel->where('is_expire', 0)->where('is_display', 1)->get()->toArray();
        return $this->voucherParameterTransformer->transformCollection($active_vouchers_parameters_arrays);
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
    
    /**
     * Search for voucher parameters by title
     * @param string $voucher_title
     * @return collection
     */
    public function searchByVoucherTitle( $voucher_title) {
//        todo modify response
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
     * Update Gift voucher parameters
     * @param UpdateVoucherParametersRequest $request
     * @return Json response
     */
    public function updateGiftVoucherParameters( UpdateVoucherParametersRequest $request) {
        $raw_input = $request->json("data");
        return $this->generalUpdateHelper($raw_input);
    }
    
    /**
     * Update Deal voucher parameters
     * @param UpdateVoucherParametersRequest $request
     * @return Json response
     */
    public function updateDealVoucherParameters(  UpdateVoucherParametersRequest $request) {
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
        $input = $this->prepareDataForStoringHelper($raw_input);
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
        $modified_input = $this->prepareDataForUpdatingHelper($raw_input);
        DB::beginTransaction();
        if($voucher_parameter_object->update($modified_input)){
            $voucher_parameter_object->useTerms()->sync($modified_input['use_term_ids']);
            DB::commit();
            $response = $this->voucherParameterTransformer->transform($voucher_parameter_object->toArray());
        }else{
            DB::rollBack();
            $response = $this->respondBadRequest('Something went wrong while updating voucher');
        }//if($voucher_parameter_object->update($input))
        return $response;
    }
    
    /**
     * perform common sanitization for all vouchers
     * @param array $old_input
     * @return array
     */
    private function prepareDataForStoringHelper( array $old_input) {
        $modified_input['business_id'] = (int)$this->g->arrayKeySearchRecursively( $old_input, 'business_id');
        $modified_input['user_id'] = (int)$this->g->arrayKeySearchRecursively( $old_input, 'user_id');
        $modified_input['voucher_image_id'] = (int)$this->g->arrayKeySearchRecursively( $old_input, 'voucher_image_id');
        $modified_input['use_terms'] = array_map( 'intval', $this->g->arrayKeySearchRecursively( $old_input, 'use_term_ids'));
        if ( $purchase_start = $this->g->arrayKeySearchRecursively( $old_input, 'purchase_start') ) {
            $modified_input[ 'purchase_start' ] = g::utcDateTime( $purchase_start, 'd/m/Y H:i' );
        }else {
            $auckland_now_to_utc     = Carbon::now( 'Pacific/Auckland' )->setTimezone( 'UTC' );
            $modified_input[ 'purchase_start' ] = $auckland_now_to_utc;
        }//if (isset($input['purchase_start'])&&!empty($input['purchase_start']))
        if ( $purchase_expiry = $this->g->arrayKeySearchRecursively( $old_input, 'purchase_expiry') ) {
            $modified_input[ 'purchase_expiry' ] = g::utcDateTime( $purchase_expiry, 'd/m/Y H:i' );
        }else{
//            todo take a decision about the purchase_expiry if not exist
        }//if ( !empty($input['purchase_expiry']) )
        $modified_input['title'] = (string)$this->g->arrayKeySearchRecursively($old_input, 'title');
        $modified_input['voucher_type'] = $old_input['voucher_type'];
        $modified_input[ 'is_expire' ] = 0;
        $modified_input[ 'is_display' ] = 1;
        $modified_input[ 'is_purchased' ] = 0;
        if ( $valid_from = $this->g->arrayKeySearchRecursively( $old_input, 'valid_from') ) {
            $modified_input[ 'valid_from' ] = g::utcDateTime( $valid_from, 'd/m/Y H:i' );
        }//if ( !empty($input['valid_from']) )
        if ( $valid_until = $this->g->arrayKeySearchRecursively( $old_input, 'valid_until') ) {
            $modified_input[ 'valid_until' ] = g::utcDateTime( $valid_until, 'd/m/Y H:i' );
        }else{
            $modified_input['valid_for_amount'] = (int)  $this->g->arrayKeySearchRecursively($old_input, 'valid_for_amount');
            $modified_input['valid_for_units'] = (string)  $this->g->arrayKeySearchRecursively($old_input, 'valid_for_units');
        }//if ( !empty($input['valid_until']) )
//        secure fields from XSS attack
        ($short_description = $this->g->arrayKeySearchRecursively( $old_input, 'short_description')) ? $modified_input[ 'short_description' ] = preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $short_description ) : FALSE;
         ($long_description = $this->g->arrayKeySearchRecursively( $old_input, 'long_description')) ? $modified_input[ 'long_description' ] = preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $long_description ) : FALSE;
         ($retail_value = $this->g->arrayKeySearchRecursively( $old_input, 'retail_value')) ? $modified_input['retail_value'] = (double)$retail_value : FALSE;
         ($value = $this->g->arrayKeySearchRecursively($old_input, 'value')) ? $modified_input['value'] = (double)$value : FALSE;
         ($min_value = $this->g->arrayKeySearchRecursively($old_input, 'min_value')) ? $modified_input['min_value'] = (double)$min_value : FALSE;
         ($max_value = $this->g->arrayKeySearchRecursively($old_input, 'max_value')) ? $modified_input['max_value'] = (double)$max_value : FALSE;
//         todo continue prepare the rest of the fields for the rest types of vouchers
        return $modified_input;
    }
    
    /**
     * Modify data to be updated
     * @param array $raw_input
     * @return array
     */
    private function prepareDataForUpdatingHelper( $raw_input ) {
        ($business_id = $this->g->arrayKeySearchRecursively($raw_input, 'business_id')) ? $modified_input['business_id'] = (int)$business_id : FALSE;
        ($user_id = $this->g->arrayKeySearchRecursively($raw_input, 'user_id')) ? $modified_input['user_id'] = (int)$user_id : FALSE;
        ($voucher_image_id = $this->g->arrayKeySearchRecursively($raw_input, 'voucher_image_id')) ? $modified_input['voucher_image_id'] = (int)$voucher_image_id : FALSE;
        ($use_term_ids = $this->g->arrayKeySearchRecursively($raw_input, 'use_term_ids')) ? $modified_input['use_term_ids'] = array_map('intval', $use_term_ids) : FALSE;
        ($title = $this->g->arrayKeySearchRecursively($raw_input, 'title')) ? $modified_input['title'] = (string)$title:false;
        if($is_expire = $this->g->arrayKeySearchRecursively($raw_input, 'is_expire')){
            $modified_input['is_expire'] = ("false" === $is_expire) ? FALSE : TRUE;
        }//if($is_expire = $this->g->arrayKeySearchRecursively($raw_input, 'is_expire'))
        if($is_display = $this->g->arrayKeySearchRecursively($raw_input, 'is_display')){
            $modified_input['is_display'] = ("false" === $is_display) ? FALSE : TRUE;
        }//if($is_display = $this->g->arrayKeySearchRecursively($raw_input, 'is_display'))
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
