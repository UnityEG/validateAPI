<?php

namespace App\Http\Controllers;

use App\aaa\g;
use App\aaa\Transformers\VoucherParametersTransformer;
use App\Http\Controllers\ApiController;
use App\Http\Models\Business;
use App\Http\Models\VoucherParameter;
use App\Http\Requests\Vouchers\VoucherParameters\CreateVoucherParametersRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use \Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VoucherParametersController extends ApiController
{
    private $voucherParameterTransformer;
    
    public function __construct(
    VoucherParametersTransformer $voucher_parameter_transformer
            ) {
//        Apply the jwt.auth middleware to all methods in this controller
        $this->middleware('jwt.auth');
        $this->voucherParameterTransformer = $voucher_parameter_transformer;
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
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data = ['You are in create vouchers method'];
        
        return $data;
    }

    /**
     * Store Deal voucher parameters
     * @param CreateVoucherParametersRequest $request
     * @return Json response
     */
    public function storeDealVoucherParameters(CreateVoucherParametersRequest $request ) {
        $raw_input= $request->get("data");
        $raw_input['voucher_type'] = 'deal';
        return $this->store($raw_input);
    }
    
    /**
     * Store Gift voucher patameters
     * @param CreateVoucherParametersRequest $request
     * @return Json response
     */
    public function storeGiftVoucherParameters( CreateVoucherParametersRequest $request) {
       $raw_input = $request->get("data") ;
       $raw_input['voucher_type'] = 'gift';
       return $this->store($raw_input);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        try{
            $voucher_parameter_object = VoucherParameter::findOrFail($id);
        } catch (Exception $ex) {
                return $this->respondNotFound($ex->getMessage());
        }//catch (\Exception $ex)
        return $this->respond($this->voucherParameterTransformer->transform($voucher_parameter_object->toArray()));
    }
    
//    todo create showGiftVoucherParameters method
//    todo Create showDealVoucherParameters method

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        try{
            $voucher_parameter_object = VoucherParameter::findOrFail($id);
            
            if ( $voucher_parameter_object->is_purchased ) {
                throw new Exception('This voucher has been sold you cannot update it' );
            }//if ( $voucher_parameter_object->is_purchased )
        } catch (Exception $ex) {
            return $this->respondNotFound($ex->getMessage());
        }
        
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(UpdateRequest $request, $id)
    {
        try{
            $voucher_parameter_object = VoucherParameter::findOrFail($id);
        }  catch (Exception $e){
            return $this->respondNotFound();
        }//catch (\Exception $e)
        if ( $voucher_parameter_object->is_purchased ) {
            $this->setStatusCode(417);
            return $this->respondWithError( 'Updating Voucher Parmeters Error', 'This voucher already purchased so It cannot be updated!');
        }//if ( $voucher_parameter_object->is_purchased )
        $raw_input = $request->except('use_terms');
        $input = $this->storeUpdateHelper($raw_input);
        if ( !is_array( $input ) ) {
//            return with error respond
            return $input;
        }//if ( !is_array( $input ) )
        DB::beginTransaction();
        if($voucher_parameter_object->update($input)){
            $voucher_parameter_object->useTerms()->sync($request->input('use_terms'));
            DB::commit();
            return $voucher_parameter_object;
        }//if($voucher_parameter_object->update($input))
        DB::rollBack();
        return $this->respondBadRequest('Something went wrong while updating voucher');
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
    public function store( array $raw_input ) {
        $this->storeValidationHelper($raw_input);
        $input = $this->storeUpdateHelper($raw_input);
        DB::beginTransaction();
        if($created_voucher_parameters = VoucherParameter::create( $input )){
        $created_voucher_parameters->useTerms()->attach($input['use_terms']);
        DB::commit();
            $response =  $this->voucherParameterTransformer->transform( $created_voucher_parameters->toArray());
        }//if($created_voucher_parameters = VoucherParameter::create( $input ))
        else{
            DB::rollBack();
            $response = $this->respondInternalError();
        }
        return $response;
    }
    
    /**
     * perform common sanitization for all vouchers
     * @param object $input
     * @return array
     */
    private function storeUpdateHelper( array $input) {
        $input['business_id'] = (int)$input['relations']['business']['data']['business_id'];
        $input['user_id'] = (int)$input['relations']['user']['data']['user_id'];
        $input['voucher_image_id'] = (int)$input['relations']['voucher_image']['data']['voucher_image_id'];
        $input['use_terms'] = (array)$input['relations']['use_terms']['data']['use_term_ids'];
        if ( isset( $input[ 'purchase_start' ] ) && !empty( $input[ 'purchase_start' ] ) ) {
            $input[ 'purchase_start' ] = g::utcDateTime( $input[ 'purchase_start' ], 'd/m/Y H:i' );
        }//if (isset($input['purchase_start'])&&!empty($input['purchase_start'])){
        else {
            $auckland_now_to_utc     = Carbon::now( 'Pacific/Auckland' )->setTimezone( 'UTC' );
            $input[ 'purchase_start' ] = $auckland_now_to_utc;
        }
        if ( !empty( $input[ 'purchase_expiry' ] ) ) {
            $input[ 'purchase_expiry' ] = g::utcDateTime( $input[ 'purchase_expiry' ], 'd/m/Y H:i' );
        }//if ( !empty($input['purchase_expiry']) )
        $input[ 'is_expire' ] = 0;
        $input[ 'is_display' ] = 1;
        $input[ 'is_purchased' ] = 0;
        if ( !empty( $input[ 'valid_from' ] ) ) {
            $input[ 'valid_from' ] = g::utcDateTime( $input[ 'valid_from' ], 'd/m/Y H:i' );
        }//if ( !empty($input['valid_from']) )
        if ( !empty( $input[ 'valid_until' ] ) ) {
            $input[ 'valid_until' ] = g::utcDateTime( $input[ 'valid_until' ], 'd/m/Y H:i' );
        }//if ( !empty($input['valid_until']) )
//        secure fields from XSS attack
        $input[ 'short_description' ] = (isset( $input[ 'short_description' ] )) ? preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $input[ 'short_description' ] ) : '';
        $input[ 'long_description' ] = (isset( $input[ 'long_description' ] )) ? preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $input[ 'long_description' ] ) : '';
        return $input;
    }

    /**
     * Store Validation helper for important data of each type of vouchers
     * @param array $input
     * @return boolean
     * @throws Exception
     */
    private function storeValidationHelper( array $input) {
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
}
