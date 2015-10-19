<?php

namespace App\Http\Controllers;

use App\EssentialEntities\GeneralHelperTools;
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
//    todo refine VoucherParametersController to remove unused methods
//    todo update documentation of the class (@var, @param and @return)
//    todo apply lazy instantiation by applying dependency injection on methods
    
    /**
     * instance of VoucherParameter Model class
     * @var \App\Http\Models\VoucherParameter
     */
    private $voucherParameterModel;


    public function __construct(VoucherParameter $voucher_parameter_model) {
//        Apply the jwt.auth middleware to all methods in this controller
        $this->middleware('jwt.auth', ['only'=>['storeDealVoucherParameters', 'storeGiftVoucherParameters', 'updateDealVoucherParameters', 'updateGiftVoucherParameters']]);
//        todo apply jwt.refresh middleware to refresh token every request
        $this->voucherParameterModel = $voucher_parameter_model;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
//        todo Create IndexVoucherParameterRequest to apply authentication rules
        $response = [];
        foreach ( $this->voucherParameterModel->all() as $voucher_parameter_object) {
            $response["data"][] = $voucher_parameter_object->getBeforeStandardArray();
        }
        return $response;
    }
    
    /**
     * List all active vouchers parameters method
     * active voucher parameters means that is_expiry=0 and is_display=1
     * @return Response
     */
    public function listAllActiveVouchersParameters( ) {
        $response = [];
        foreach ( $this->voucherParameterModel->where(['is_expire'=>0, 'is_display'=>1])->get() as $voucher_parameter_object) {
            $response["data"][] = $voucher_parameter_object->getBeforeStandardArray();
        }
        return $response;
    }
    
    /**
     * Display a voucher parameters by ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return $this->voucherParameterModel->findOrFail((int)$id)->getStandardJsonFormat();
    }
    
//    todo create showGiftVoucherParameters method
//    todo Create showDealVoucherParameters method
    
    /**
     * Search for voucher parameters by title
     * @param string $voucher_title
     * @return Response
     */
    public function searchByVoucherTitle( $voucher_title) {
        $optimized_voucher_title = strtolower(urldecode($voucher_title));
        $voucher_parameter_exist = VoucherParameter::where('title', 'like', '%'.$optimized_voucher_title.'%')->exists();
        if ( !$voucher_parameter_exist ) {
            return $this->respond(["data"=>"No matched result"]);
        }//if ( !$voucher_parameter_exist )
        
        $voucher_parameter_objects = VoucherParameter::where('title', 'like', '%'.$optimized_voucher_title.'%')->get();
        $response = [];
        foreach ( $voucher_parameter_objects as $voucher_parameter_object) {
            $response["data"][] = $voucher_parameter_object->getBeforeStandardArray();
        }
        return $response;
    }
    
    /**
     * Search for voucher parameters by business name
     * @param string $business_name
     * @return Response
     */
    public function searchByBusinessName( $business_name) {
        $optimized_business_name = strtolower(urlencode($business_name));
        $business_exist = Business::where('business_name', 'like', '%'.$optimized_business_name.'%')->exists();
        if ( !$business_exist ) {
            return $this->respond(["data"=>"No matched result"]);
        }//if ( !$business_exist )
        $business_object = Business::where('business_name', 'like', '%'.$optimized_business_name.'%')->first(['id']);
        $voucher_parameter_objects = VoucherParameter::where('business_id', $business_object->id)->get();
        $response = [];
        foreach ( $voucher_parameter_objects as $voucher_parameter_object) {
            $response["data"][] = $voucher_parameter_object->getBeforeStandardArray();
        }
        return $response;
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
        $input = $this->prepareDataForStoringHelper($raw_input, new GeneralHelperTools());
        DB::beginTransaction();
        $created_voucher_parameters = VoucherParameter::create( $input );
        if(  is_object( $created_voucher_parameters)){
        $created_voucher_parameters->useTerms()->attach($input['use_terms']);
        DB::commit();
            $response =  $created_voucher_parameters->getStandardJsonFormat();
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
        $modified_input = $this->prepareDataForUpdatingHelper($raw_input, new GeneralHelperTools());
        DB::beginTransaction();
        if($voucher_parameter_object->update($modified_input)){
            $voucher_parameter_object->useTerms()->sync($modified_input['use_term_ids']);
            DB::commit();
            $response = $voucher_parameter_object->getStandardJsonFormat();
        }else{
            DB::rollBack();
            $response = $this->respondBadRequest('Something went wrong while updating voucher');
        }//if($voucher_parameter_object->update($input))
        return $response;
    }
    
    /**
     * Prepare Data for generalStoreHelper method
     * @param array $old_input
     * @return array
     */
    private function prepareDataForStoringHelper( array $old_input, GeneralHelperTools $general_helper_tools) {
        $modified_input['business_id'] = (int)$general_helper_tools->arrayKeySearchRecursively( $old_input, 'business_id');
        $modified_input['user_id'] = (int)$general_helper_tools->arrayKeySearchRecursively( $old_input, 'user_id');
        $modified_input['voucher_image_id'] = (int)$general_helper_tools->arrayKeySearchRecursively( $old_input, 'voucher_image_id');
        $modified_input['use_terms'] = array_map( 'intval', $general_helper_tools->arrayKeySearchRecursively( $old_input, 'use_term_ids'));
        if ( $purchase_start = $general_helper_tools->arrayKeySearchRecursively( $old_input, 'purchase_start') ) {
            $modified_input[ 'purchase_start' ] = $general_helper_tools->utcDateTime( $purchase_start, 'd/m/Y H:i' );
        }else {
            $auckland_now_to_utc     = Carbon::now( 'Pacific/Auckland' )->setTimezone( 'UTC' );
            $modified_input[ 'purchase_start' ] = $auckland_now_to_utc;
        }//if (isset($input['purchase_start'])&&!empty($input['purchase_start']))
        if ( $purchase_expiry = $general_helper_tools->arrayKeySearchRecursively( $old_input, 'purchase_expiry') ) {
            $modified_input[ 'purchase_expiry' ] = $general_helper_tools->utcDateTime( $purchase_expiry, 'd/m/Y H:i' );
        }else{
//            todo take a decision about the purchase_expiry if not exist
        }//if ( !empty($input['purchase_expiry']) )
        $modified_input['title'] = (string)$general_helper_tools->arrayKeySearchRecursively($old_input, 'title');
        $modified_input['voucher_type'] = $old_input['voucher_type'];
        $modified_input[ 'is_expire' ] = 0;
        $modified_input[ 'is_display' ] = 1;
        $modified_input[ 'is_purchased' ] = 0;
        if ( $valid_from = $general_helper_tools->arrayKeySearchRecursively( $old_input, 'valid_from') ) {
            $modified_input[ 'valid_from' ] = $general_helper_tools->utcDateTime( $valid_from, 'd/m/Y H:i' );
        }//if ( !empty($input['valid_from']) )
        if ( $valid_until = $general_helper_tools->arrayKeySearchRecursively( $old_input, 'valid_until') ) {
            $modified_input[ 'valid_until' ] = $general_helper_tools->utcDateTime( $valid_until, 'd/m/Y H:i' );
        }else{
            $modified_input['valid_for_amount'] = (int)  $general_helper_tools->arrayKeySearchRecursively($old_input, 'valid_for_amount');
            $modified_input['valid_for_units'] = (string)  $general_helper_tools->arrayKeySearchRecursively($old_input, 'valid_for_units');
        }//if ( !empty($input['valid_until']) )
//        secure fields from XSS attack
        ($short_description = $general_helper_tools->arrayKeySearchRecursively( $old_input, 'short_description')) ? $modified_input[ 'short_description' ] = preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $short_description ) : FALSE;
         ($long_description = $general_helper_tools->arrayKeySearchRecursively( $old_input, 'long_description')) ? $modified_input[ 'long_description' ] = preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $long_description ) : FALSE;
         ($retail_value = $general_helper_tools->arrayKeySearchRecursively( $old_input, 'retail_value')) ? $modified_input['retail_value'] = (double)$retail_value : FALSE;
         ($value = $general_helper_tools->arrayKeySearchRecursively($old_input, 'value')) ? $modified_input['value'] = (double)$value : FALSE;
         ($min_value = $general_helper_tools->arrayKeySearchRecursively($old_input, 'min_value')) ? $modified_input['min_value'] = (double)$min_value : FALSE;
         ($max_value = $general_helper_tools->arrayKeySearchRecursively($old_input, 'max_value')) ? $modified_input['max_value'] = (double)$max_value : FALSE;
//         todo continue prepare the rest of the fields for the rest types of vouchers
        return $modified_input;
    }
    
    /**
     * Prepare data to be updated
     * @param array $raw_input
     * @return array
     */
    private function prepareDataForUpdatingHelper( $raw_input , GeneralHelperTools $general_helper_tools) {
        ($business_id = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'business_id')) ? $modified_input['business_id'] = (int)$business_id : FALSE;
        ($user_id = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'user_id')) ? $modified_input['user_id'] = (int)$user_id : FALSE;
        ($voucher_image_id = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'voucher_image_id')) ? $modified_input['voucher_image_id'] = (int)$voucher_image_id : FALSE;
        ($use_term_ids = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'use_term_ids')) ? $modified_input['use_term_ids'] = array_map('intval', $use_term_ids) : FALSE;
        ($title = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'title')) ? $modified_input['title'] = (string)$title:false;
        if($is_expire = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'is_expire')){
            $modified_input['is_expire'] = ("false" === $is_expire) ? FALSE : TRUE;
        }//if($is_expire = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'is_expire'))
        if($is_display = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'is_display')){
            $modified_input['is_display'] = ("false" === $is_display) ? FALSE : TRUE;
        }//if($is_display = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'is_display'))
        ($valid_for_amount = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'valid_for_amount')) ? $modified_input['valid_for_amount'] = (int)$valid_for_amount : FALSE;
        ($valid_for_units = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'valid_for_units')) ? $modified_input['valid_for_units'] = (string)$valid_for_units : FALSE;
        ($quantity = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'quantity')) ? $modified_input['quantity' ] = (int)$quantity : FALSE;
        ($no_of_uses = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'no_of_uses')) ? $modified_input['no_of_uses'] = (int)$no_of_uses : FALSE;
        ($retail_value = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'retail_value')) ? $modified_input['retail_value'] = (double)$retail_value : FALSE;
        ($value = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'value')) ? $modified_input['value'] = (double)$value : FALSE;
        ($min_value = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'min_value')) ? $modified_input['min_value'] = (double)$min_value : FALSE;
        ($max_value = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'max_value')) ? $modified_input['max_value'] = (double)$max_value : FALSE;
        ($discount_percentage = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'discount_percentage')) ? $modified_input['discount_percentage'] = (double)$discount_percentage : FALSE;
        ($purchase_start = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'purchase_start')) ? $modified_input['purchase_start'] = $general_helper_tools->utcDateTime( $purchase_start, 'd/m/Y H:i' ) : FALSE;
        ($purchase_expiry = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'purchase_expiry')) ? $modified_input['purchase_expiry'] = $general_helper_tools->utcDateTime($purchase_expiry, 'd/m/Y H:i') : FALSE;
        ($valid_from = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'valid_from')) ? $modified_input['valid_from'] = $general_helper_tools->utcDateTime($valid_from, 'd/m/Y H:i') : FALSE;
        ($valid_until = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'valid_until')) ? $modified_input['valid_until'] = $general_helper_tools->utcDateTime($valid_until, 'd/m/Y H:i') : false;
        //        secure fields from XSS attack
         ($short_description = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'short_description') ) ? $modified_input[ 'short_description' ] = preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $short_description ) : False;
         ($long_description = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'long_description')) ? $modified_input[ 'long_description' ] = preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $long_description ) : '';
//        todo take decision about working with (valid_for_amount and valid_for_units) or (valid_from and valid_until) or working with both
        return $modified_input;
    }
}
