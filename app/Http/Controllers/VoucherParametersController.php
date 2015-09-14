<?php

namespace App\Http\Controllers;

//Validations
use App\Http\Requests;
use App\Http\Requests\Vouchers\VoucherParameters\CreateVoucherParametersRequest as CreateRequest;
use App\Http\Requests\Vouchers\VoucherParameters\UpdateVoucherParametersRequest as UpdateRequest;

//Controllers
use App\Http\Controllers\ApiController;

//Models
use App\Http\Models\VoucherParameter as VoucherParameter;
use App\Http\Models\UseTerm as UseTerm;

//Helpers
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\aaa\g;
use App\aaa\Transformers\VoucherParametersTransformer;

class VoucherParametersController extends ApiController
{
    private $voucherParameterTransformer;
    
    public function __construct( ) {
//        Apply the jwt.auth middleware to all methods in this controller
        $this->middleware('jwt.auth');
        $this->voucherParameterTransformer = new VoucherParametersTransformer;
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
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store( CreateRequest $request ) {
        
        $input_before= $request->except('use_terms');
        
        $input = $this->storeUpdateHelper($input_before);
        
        \Illuminate\Support\Facades\DB::beginTransaction();
        if ( !is_array( $input ) ) {
            return $input;
        }
        if($created_voucher_parameters = VoucherParameter::create( $input )){
        
        $created_voucher_parameters->useTerms()->attach($request->get('use_terms'));
        \Illuminate\Support\Facades\DB::commit();
            return $created_voucher_parameters;
        }//if($created_voucher_parameters = VoucherParameter::create( $input ))
        else{
            \Illuminate\Support\Facades\DB::rollBack();
            return array('error');
        }
        
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
        } catch (\Exception $ex) {
                return $this->respondNotFound($ex->getMessage());
        }//catch (\Exception $ex)
        
        
        return $this->respond($this->voucherParameterTransformer->transform($voucher_parameter_object->toArray()));
    }

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
                throw new \Exception('This voucher has been sold you cannot update it' );
            }//if ( $voucher_parameter_object->is_purchased )
        } catch (\Exception $ex) {
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
        }  catch (\Exception $e){
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
        \Illuminate\Support\Facades\DB::beginTransaction();
        if($voucher_parameter_object->update($input)){
            $voucher_parameter_object->useTerms()->sync($request->input('use_terms'));
            \Illuminate\Support\Facades\DB::commit();
            return $voucher_parameter_object;
        }//if($voucher_parameter_object->update($input))
        \Illuminate\Support\Facades\DB::rollBack();
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
        //
    }
    
    
    
//    Helper Methods
    
    /**
     * perform common sanitization for all vouchers
     * @param object $input
     * @return array
     */
    private function storeUpdateHelper( $input) {
        if ( isset($input['purchase_start']) && !empty($input['purchase_start'])){
            $input['purchase_start'] = g::utcDateTime($input['purchase_start'], 'd/m/Y H:i');
        }//if (isset($input['purchase_start'])&&!empty($input['purchase_start'])){
        else{
            $auckland_now_to_utc =  Carbon::now('Pacific/Auckland')->setTimezone('UTC');
            $input['purchase_start'] = $auckland_now_to_utc;
        }
        
        if ( !empty($input['purchase_expiry']) ) {
            $input['purchase_expiry'] = g::utcDateTime($input['purchase_expiry'], 'd/m/Y H:i');
        }//if ( !empty($input['purchase_expiry']) )
        
        $input['is_expire'] = 0;
        
        $input['is_display'] = (isset($input['is_display']) && ($input['is_display'] == 1)) ? 1 : 0;
        
        $input['is_purchased'] = 0;
        
        if ( !empty($input['valid_from']) ) {
            $input['valid_from'] = g::utcDateTime($input['valid_from'], 'd/m/Y H:i');
        }//if ( !empty($input['valid_from']) )
        
        if ( !empty($input['valid_until']) ) {
            $input['valid_until'] = g::utcDateTime($input['valid_until'], 'd/m/Y H:i');
        }//if ( !empty($input['valid_until']) )
        
        $input['short_description'] = (isset($input['short_description']))?preg_replace(['/\<script\>/', '/\<\/script\>/'], '', $input['short_description']):'';
        
        $input['long_description'] = (isset($input['long_description']))?preg_replace(['/\<script\>/', '/\<\/script\>/'], '', $input['long_description']):'';
        
        switch ( $input['voucher_type']) {
            case 'gift':
                if ( empty($input['min_value']) || empty($input['max_value']) ) {
                    $this->setStatusCode(417);
                    return $this->respondWithError('min_value or max_value incorrect');
                }//if ( empty($input['min_value']) || empty($input['max_value']) )
                break;
                
            case 'deal':
                if ( empty($input['retail_value']) || empty($input['value']) ) {
                    $this->setStatusCode(417);
                    return $this->respondWithError('retail_value or value incorrect');
                }//if ( empty($input['retail_value']) || empty($input['value']) )
                break;
                
            case 'birthday':
                if ( empty($input['value'])) {
                    $this->setStatusCode(417);
                    return $this->respondWithError('retail_value or value incorrect');
                }//if ( empty($input['value']))
                break;
                
            case 'discount':
                if ( empty($input['discount_percentage'])) {
                    $this->setStatusCode(417);
                    return $this->respondWithError('retail_value or value incorrect');
                }//if ( empty($input['value']))
                break;
                
            case 'concession':
                if ( empty($input['retail_value']) || empty($input['value']) ) {
                    $this->setStatusCode(417);
                    return $this->respondWithError('retail_value or value incorrect');
                }//if ( empty($input['retail_value']) || empty($input['value']) )
                break;
        }//switch ( $input['voucher_type'])
        
        return $input;
    }
}
