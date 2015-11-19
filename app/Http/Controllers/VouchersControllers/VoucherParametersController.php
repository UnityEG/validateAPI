<?php

namespace App\Http\Controllers\VouchersControllers;

use App\Http\Controllers\ApiController;
use App\Http\Models\Business;
use App\Http\Models\VoucherParameter;
use App\Http\Requests\Vouchers\VoucherParameters\CreateVoucherParametersRequest;
use App\Http\Requests\Vouchers\VoucherParameters\UpdateVoucherParametersRequest;
use Carbon\Carbon;
use GeneralHelperTools;
use DB;
use JWTAuth;

class VoucherParametersController extends ApiController
{
//    todo refine VoucherParametersController to remove unused methods
//    todo update documentation of the class (@var, @param and @return)
//    todo apply lazy instantiation by applying dependency injection on methods
    
    /**
     * instance of VoucherParameter Model class
     * @var VoucherParameter
     */
    private $voucherParameterModel;

    public function __construct(VoucherParameter $voucher_parameter_model) {
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
//        simple authentication check rule, if complex check needed then create a request class
        if ( !JWTAuth::parseToken()->authenticate()->hasRule('voucher_parameter_show_all') ) {
            return $this->setStatusCode(403)->respondWithError('Forbidden');
        }//if ( !JWTAuth::parseToken()->authenticate()->hasRule('voucher_parameter_show_all') )
        $response = [];
        foreach ( $this->voucherParameterModel->all() as $voucher_parameter_object) {
            $response["data"][] = $voucher_parameter_object->getBeforeStandardArray();
        }
        return $response;
    }
    
    /**
     * List all active vouchers parameters method
     * Unlimited active voucher parameters means that is_expiry=0, is_display=1 and is_limited_quantity=0
     * Limited active voucher parameters means that is_expire=0, is_display=1, is_limited_quantity=1 and stock_quantity >= 1
     * @return Response
     */
    public function listAllActiveVouchersParameters( ) {
        $response = [];
        $active_unlimited_voucher_objects = $this->voucherParameterModel->where(['is_expire'=>0, 'is_display'=>1, 'is_limited_quantity'=>0])->get();
        $active_limited_voucher_objects = $this->voucherParameterModel->where(['is_expire'=>0, 'is_display'=>1, 'is_limited_quantity'=>1])->where('stock_quantity', '>=', 1)->get();
        foreach ( $active_unlimited_voucher_objects as $unlimited_voucher_parameter_object) {
            $response["data"][] = $unlimited_voucher_parameter_object->getBeforeStandardArray();
        }//foreach ( $active_unlimited_voucher_objects as $voucher_parameter_object)
        foreach ( $active_limited_voucher_objects as $limited_voucher_parameter_object) {
            $response["data"][] = $limited_voucher_parameter_object->getBeforeStandardArray();
        }//foreach ( $active_limited_voucher_objects as $limited_voucher_parameter)
        return $response;
    }
    
    /**
     * List all types and IDs of Voucher parameters
     * @param VoucherParameter $voucher_parameter_model
     * @return array
     */
    public function listVoucherParametersTypes(VoucherParameter $voucher_parameter_model){
        return $this->respond($voucher_parameter_model->all(['id', 'voucher_type'])->toArray());
    }
    
    /**
     * List all Gift Voucher Parameters of specific Business by business id
     * @param integer $business_id
     * @param VoucherParameter $voucher_parameter_model
     * @return array
     */
    public function listGiftVoucherParametersOfBusiness($business_id, VoucherParameter $voucher_parameter_model){
        $filtered_voucher_parameter_objects = $voucher_parameter_model->where(['business_id'=>(int)$business_id, 'voucher_type'=>'gift'])->get();
        $response["data"] = [];
        foreach ( $filtered_voucher_parameter_objects as $voucher_parameter_object) {
            $response["data"][] = $voucher_parameter_object->getBeforeStandardArray();
        }//foreach ( $filterd_voucher_parameter_objects as $voucher_parameter_object)
        return $response;
    }
    
    /**
     * list all Deal Voucher Parameters of specific Business by business_id
     * @param integer $business_id
     * @param VoucherParameter $voucher_parameter_model
     * @return array
     */
    public function listDealVoucherParametersOfBusiness($business_id, VoucherParameter $voucher_parameter_model){
        $filtered_voucher_parameter_object = $voucher_parameter_model->where(['business_id'=>(int)$business_id, 'voucher_type'=>'deal'])->get();
        $response["data"] = [];
        foreach ( $filtered_voucher_parameter_object as $voucher_parameter_object) {
            $response['data'][] = $voucher_parameter_object->getBeforeStandardArray();
        }//foreach ( $filtered_voucher_parameter_object as $voucher_parameter_object)
        return $response;
    }
    
    /**
     * Search for voucher parameters by title
     * @param string $voucher_title
     * @return array
     */
    public function searchByVoucherTitle( $voucher_title) {
        $optimized_voucher_title = strtolower(urldecode($voucher_title));
        $voucher_parameter_exist = VoucherParameter::where('title', 'like', '%'.$optimized_voucher_title.'%')->exists();
        if ( !$voucher_parameter_exist ) {
            return $this->respond([]);
        }//if ( !$voucher_parameter_exist )
//        todo use getStandardJsonCollection method in VoucherParameter Model
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
     * @return array
     */
    public function searchByBusinessName( $business_name, Business $business_model, VoucherParameter $voucher_parameter_model) {
        $optimized_business_name = strtolower(urldecode($business_name));
        $business_object = $business_model->where('business_name', 'like', "%{$optimized_business_name}%")->first(['id']);
        if ( !is_object($business_object) ) {
            return $this->respond([]);
        }//if ( !is_object($business_object) )
        $response["data"] = [];
        foreach ( $voucher_parameter_model->where('business_id', (int)$business_object->id)->get() as $voucher_parameter_object) {
            $response["data"][] = $voucher_parameter_object->getBeforeStandardArray();
        }//foreach($voucher_parameter_model->where('business_id', (int)$business_object->id)->get() as $voucher_parameter_object)
        return $response;
    }
    
    /**
     * Search for Voucher Parameters by Business ID
     * @param integer $business_id
     * @param VoucherParameter $voucher_parameter_model
     * @return array
     */
    public function searchByBusinessId ($business_id, VoucherParameter $voucher_parameter_model){
        $response["data"] = [];
        foreach ( $voucher_parameter_model->where('business_id', (int)$business_id)->get() as $voucher_parameter_object ) {
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
    
    /**
     * Show Active Voucher Parameters that cross these conditions (is_expire=>0, is_display=>1, is_limited_quantity=>0 || (is_limited_quantity=>1 && stock_quantity >= 1))
     * @param type $voucher_parameter_id
     * @param VoucherParameter $voucher_parameter_model
     * @return array
     */
    public function showActiveVoucherParameter($voucher_parameter_id, VoucherParameter $voucher_parameter_model){
        $voucher_parameter_object = $voucher_parameter_model->where(['id'=>(int)$voucher_parameter_id, 'is_expire'=>0, 'is_display'=>1])->first();
        if(!is_object( $voucher_parameter_object )){
            return $this->respondNotFound();
        }elseif((bool)$voucher_parameter_object->is_limited_quantity && 1 <= $voucher_parameter_object->stock_quantity){
            return $voucher_parameter_object->getStandardJsonFormat();
        }elseif(!(bool)$voucher_parameter_object->is_limited_quantity){
            return $voucher_parameter_object->getStandardJsonFormat();
        }//if(!is_object( $voucher_parameter_object ))
        return $this->respondNotFound();
    }
//    todo create showGiftVoucherParameters method
//    todo Create showDealVoucherParameters method

    /**
     * Store Deal voucher parameters
     * @param \App\Http\Requests\VoucherParameters\CreateVoucherParametersRequest $request
     * @return array
     */
    public function storeDealVoucherParameters(CreateVoucherParametersRequest $request ) {
        $raw_input= $request->get("data");
        $raw_input['voucher_type'] = 'deal';
        return $this->generalStoreHelper( $raw_input );
    }
    
    /**
     * Store Gift voucher patameters
     * @param \App\Http\Requests\VoucherParameters\CreateVoucherParametersRequest $request
     * @return Json response
     */
    public function storeGiftVoucherParameters( CreateVoucherParametersRequest $request) {
       $raw_input = $request->get("data") ;
       $raw_input['voucher_type'] = 'gift';
       return $this->generalStoreHelper( $raw_input );
    }

    /**
     * Update Gift voucher parameters
     * @param \App\Http\Requests\VoucherParameters\UpdateVoucherParametersRequest $request
     * @return Json response
     */
    public function updateGiftVoucherParameters( UpdateVoucherParametersRequest $request) {
        $raw_input = $request->json("data");
        return $this->generalUpdateHelper($raw_input);
    }
    
    /**
     * Update Deal voucher parameters
     * @param \App\Http\Requests\VoucherParameters\UpdateVoucherParametersRequest $request
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
            $response =  $created_voucher_parameters->getStandardJsonFormat();
        }else{
            DB::rollBack();
            $response = $this->respondInternalError();
        }//if(is_object( $created_voucher_parameters))
        return $response;
    }
    
    /**
     * General Update helper.
     *
     * @param array $raw_input  raw input
     * @return Response
     */
    public function generalUpdateHelper(array $raw_input)
    {
        $voucher_parameter_object = VoucherParameter::findOrFail($raw_input['id']);
        $modified_input = $this->prepareDataForUpdatingHelper($raw_input);
        dd($modified_input);
        DB::beginTransaction();
        if($voucher_parameter_object->update($modified_input)){
            (!isset($modified_input['use_term_ids']))?:$voucher_parameter_object->useTerms()->sync($modified_input['use_term_ids']);
            DB::commit();
            return $voucher_parameter_object->getStandardJsonFormat();
        }else{
            DB::rollBack();
            return $this->respondBadRequest('Something went wrong while updating voucher');
        }//if($voucher_parameter_object->update($input))
    }
    
    /**
     * Prepare Data for generalStoreHelper method
     * @param array $old_input
     * @return array
     */
    private function prepareDataForStoringHelper( array $old_input) {
//        todo refactor prepareDataForStoringHelper method
        $modified_input['business_id'] = (int)GeneralHelperTools::arrayKeySearchRecursively( $old_input, 'business_id');
        $modified_input['user_id'] = (int)JWTAuth::parseToken()->authenticate()->id;
        $modified_input['voucher_image_id'] = (int)GeneralHelperTools::arrayKeySearchRecursively( $old_input, 'voucher_image_id');
        $modified_input['use_terms'] = array_map( 'intval', GeneralHelperTools::arrayKeySearchRecursively( $old_input, 'use_term_ids'));
        if ( $purchase_start = GeneralHelperTools::arrayKeySearchRecursively( $old_input, 'purchase_start') ) {
            $modified_input[ 'purchase_start' ] = GeneralHelperTools::utcDateTime( $purchase_start, 'd/m/Y H:i' );
        }else {
            $modified_input[ 'purchase_start' ] = Carbon::now( 'Pacific/Auckland' )->setTimezone( 'UTC' );;
        }//if (isset($input['purchase_start'])&&!empty($input['purchase_start']))
        if ( $purchase_expiry = GeneralHelperTools::arrayKeySearchRecursively( $old_input, 'purchase_expiry') ) {
            $modified_input[ 'purchase_expiry' ] = GeneralHelperTools::utcDateTime( $purchase_expiry, 'd/m/Y H:i' );
        }else{
        }//if ( !empty($input['purchase_expiry']) )
        $modified_input['title'] = (string)GeneralHelperTools::arrayKeySearchRecursively($old_input, 'title');
        $modified_input['voucher_type'] = $old_input['voucher_type'];
        $modified_input[ 'is_expire' ] = $modified_input['is_purchased'] = 0;
        $modified_input[ 'is_display' ] = 1;
        $modified_input[ 'valid_from' ] = GeneralHelperTools::utcDateTime( GeneralHelperTools::arrayKeySearchRecursively( $old_input, 'valid_from'), 'd/m/Y H:i' );
        if($valid_until = GeneralHelperTools::arrayKeySearchRecursively( $old_input, 'valid_until')){
            $modified_input[ 'valid_until' ] = GeneralHelperTools::utcDateTime( $valid_until, 'd/m/Y H:i' );
        }else{
            $modified_input['valid_for_amount'] = (int)GeneralHelperTools::arrayKeySearchRecursively($old_input, 'valid_for_amount');
            $modified_input['valid_for_units'] = (string)GeneralHelperTools::arrayKeySearchRecursively($old_input, 'valid_for_units');
            $valid_from_clone = clone $modified_input['valid_from'];
            switch ($modified_input['valid_for_units']){
                case 'h':
                    $modified_input['valid_until'] = $valid_from_clone->addHours($modified_input['valid_for_amount']);
                    break;
                case 'd':
                    $modified_input['valid_until'] = $valid_from_clone->addDays($modified_input['valid_for_amount']);
                    break;
                case 'w':
                    $modified_input['valid_until'] = $valid_from_clone->addWeeks($modified_input['valid_for_amount']);
                    break;
                case 'm':
                    $modified_input['valid_until'] = $valid_from_clone->addMonths($modified_input['valid_for_amount']);
                    break;
            }//switch ($modified_input['valid_for_units'])
        }//if($valid_until = $general_helper_tools->arrayKeySearchRecursively( $old_input, 'valid_until'))
        $quantity = GeneralHelperTools::arrayKeySearchRecursively( $old_input, 'quantity');
        if ( FALSE !== $quantity ) {
            $modified_input['is_limited_quantity'] = (0 < $quantity) ? TRUE : FALSE;
            $modified_input['quantity'] = $modified_input['stock_quantity'] = (int)$quantity;
        }else{
            $modified_input['is_limited_quantity'] = FALSE;
            $modified_input['quantity'] = $modified_input['stock_quantity'] = 0;
        }//if ( FALSE !== $quantity )
//        secure fields from XSS attack
        ($short_description = GeneralHelperTools::arrayKeySearchRecursively( $old_input, 'short_description')) ? $modified_input[ 'short_description' ] = preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $short_description ) : FALSE;
         ($long_description = GeneralHelperTools::arrayKeySearchRecursively( $old_input, 'long_description')) ? $modified_input[ 'long_description' ] = preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $long_description ) : FALSE;
         $no_of_uses = GeneralHelperTools::arrayKeySearchRecursively( $old_input, 'no_of_uses');
         if ( FALSE !== $no_of_uses ) {
             $modified_input['is_single_use'] = (1 == (int)$no_of_uses) ? TRUE : FALSE;
             $modified_input['no_of_uses'] = (int) $no_of_uses;
         }else{
             $modified_input['is_single_use'] = FALSE;
             $modified_input['no_of_uses'] = 0;
         }//if ( FALSE !== $no_of_uses )
         ($retail_value = GeneralHelperTools::arrayKeySearchRecursively( $old_input, 'retail_value')) ? $modified_input['retail_value'] = (double)$retail_value : FALSE;
         ($value = GeneralHelperTools::arrayKeySearchRecursively($old_input, 'value')) ? $modified_input['value'] = (double)$value : FALSE;
         ($min_value = GeneralHelperTools::arrayKeySearchRecursively($old_input, 'min_value')) ? $modified_input['min_value'] = (double)$min_value : FALSE;
         ($max_value = GeneralHelperTools::arrayKeySearchRecursively($old_input, 'max_value')) ? $modified_input['max_value'] = (double)$max_value : FALSE;
//         todo continue prepare the rest of the fields for the rest types of vouchers
        return $modified_input;
    }
    
    /**
     * Prepare data to be updated
     * @param array $raw_input
     * @return array
     */
    private function prepareDataForUpdatingHelper( array $raw_input) {
//        todo use GeneralHelperTool Facade
//        todo refactor prepareDataForUpdatingHelper method
        ($voucher_image_id = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'voucher_image_id')) ? $modified_input['voucher_image_id'] = (int)$voucher_image_id : FALSE;
        ($use_term_ids = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'use_term_ids')) ? $modified_input['use_term_ids'] = array_map('intval', $use_term_ids) : FALSE;
        ($title = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'title')) ? $modified_input['title'] = (string)$title:false;
        if($is_expire = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'is_expire')){
            $modified_input['is_expire'] = ("false" === $is_expire) ? FALSE : TRUE;
        }//if($is_expire = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'is_expire'))
        if($is_display = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'is_display')){
            $modified_input['is_display'] = ("false" === $is_display) ? FALSE : TRUE;
        }//if($is_display = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'is_display'))
        if($quantity = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'quantity')) {
            $modified_input['is_limited_quantity'] = 1;
            $modified_input['quantity' ] = $modified_input['stock_quantity'] = (int)$quantity;
        }else{
            $modified_input['is_limited_quantity'] = $modified_input['quantity'] = $modified_input['stock_quantity'] = 0;
        }//if($quantity = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'quantity'))
        if($no_of_uses = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'no_of_uses')){
            $modified_input['no_of_uses'] = (int)$no_of_uses;
            $modified_input['is_single_use'] = 0;
        }else{
            $modified_input['no_of_uses'] = 0;
            $modified_input['is_single_use'] = 1;
        }//if($no_of_uses = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'no_of_uses'))
        ($retail_value = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'retail_value')) ? $modified_input['retail_value'] = (double)$retail_value : FALSE;
        ($value = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'value')) ? $modified_input['value'] = (double)$value : FALSE;
        ($min_value = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'min_value')) ? $modified_input['min_value'] = (double)$min_value : FALSE;
        ($max_value = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'max_value')) ? $modified_input['max_value'] = (double)$max_value : FALSE;
        ($discount_percentage = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'discount_percentage')) ? $modified_input['discount_percentage'] = (double)$discount_percentage : FALSE;
        ($purchase_start = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'purchase_start')) ? $modified_input['purchase_start'] = $general_helper_tools->utcDateTime( $purchase_start, 'd/m/Y H:i' ) : FALSE;
        ($purchase_expiry = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'purchase_expiry')) ? $modified_input['purchase_expiry'] = $general_helper_tools->utcDateTime($purchase_expiry, 'd/m/Y H:i') : FALSE;
        ($valid_from = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'valid_from')) ? $modified_input['valid_from'] = $general_helper_tools->utcDateTime($valid_from, 'd/m/Y H:i') : FALSE;
        if($valid_until = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'valid_until')){
            $modified_input['valid_until'] = $general_helper_tools->utcDateTime($valid_until, 'd/m/Y H:i');
            $modified_input['valid_for_amount'] = 0;
            $modified_input['valid_for_units'] = '';
        }elseif(($valid_for_amount = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'valid_for_amount')) && ($valid_for_units = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'valid_for_units'))){
            $modified_input['valid_for_amount'] = (int)$valid_for_amount;
            $modified_input['valid_for_units'] = (string)$valid_for_units;
            $clone_valid_from = clone $modified_input['valid_from'];
            switch ( $valid_for_units ) {
                case 'h':
                    $modified_input['valid_until'] = $clone_valid_from->addHours($modified_input['valid_for_amount']);
                    break;
                case 'd':
                    $modified_input['valid_until'] = $clone_valid_from->addDays($modified_input['valid_for_amount']);
                    break;
                case 'w':
                    $modified_input['valid_until'] = $clone_valid_from->addWeeks($modified_input['valid_for_amount']);
                    break;
                case 'm':
                    $modified_input['valid_until'] = $clone_valid_from->addMonths($modified_input['valid_for_amount']);
                    break;
            }//switch ( $valid_for_units )
        }//if($valid_until = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'valid_until'))
        //        secure fields from XSS attack
         ($short_description = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'short_description') ) ? $modified_input[ 'short_description' ] = preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $short_description ) : False;
         ($long_description = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'long_description')) ? $modified_input[ 'long_description' ] = preg_replace( ['/\<script\>/', '/\<\/script\>/' ], '', $long_description ) : '';
//         todo continue add specific fields to each voucher type to be updated
        return $modified_input;
    }
}
