<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
//
use Auth;
use Input;
use Illuminate\Support\Facades\Validator;
//
use App\Http\Models\GiftVoucher;
use App\Http\Models\GiftVoucherValidation;
//
use App\aaa\Transformers\GiftVoucherValidationTransformer;

//use App\aaa\g;

class GiftVoucherValidationController extends ApiController {

    protected $itemTransformer;

    public function __construct(GiftVoucherValidationTransformer $itemTransformer) {
        // Apply the jwt.auth middleware to all methods in this controller
        $this->middleware('jwt.auth');
        // 
        $this->itemTransformer = $itemTransformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        //
        return $this->respond(['data' => $this->readALL()]);
    }

    public function readALL() {
        //
        $group = GiftVoucherValidation::all();
        //
        return $this->transformGroup($group->toArray()); // $GiftVouchers->all() equals to $GiftVouchers->toArray()
    }

    public function transformGroup($group) {
        //
        return $this->itemTransformer->transformCollection($group);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function findByGiftVoucher($GiftVoucher_id) {
        //
        $group = GiftVoucherValidation::where('giftvoucher_id', '=', $GiftVoucher_id)->get();

        $item = GiftVoucherValidation::find($id);
        //
        return $this->display($item);
    }

    public function show($id) {
        //
        $item = GiftVoucherValidation::find($id);
        //
        return $this->display($item);
    }

    protected function display($item) {
        //
        if (!$item) {
            // item does not exist
            return $this->respondNotFound('Gift Voucher Validation does not exist');
        }
        //
        // item exists
        return $this->respond([
                    'data' => $this->itemTransformer->transform($item->toArray()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request) {
        //
        //  Authorization Check
//        if (!g::has('Gift Voucher Validation - Create')) {
//            return g::back();
//        }
        // Read Input ==========================================================
        $input = Input::all(); 
        // Validate Input ======================================================
        // Set Validation Rules
        $rules = [
            'giftvoucher_id' => 'required|numeric',
            'value' => 'required|numeric',
        ];

        // Check if all required data are given correctly
        $validation = Validator::make($input, $rules);

        // if validation fails response with error description
        if ($validation->fails()) {

            // Collect error messages
            $error_description = implode(', ', $validation->errors()->all());

            // Response bad request with error description
            return $this->respondBadRequest($error_description);
        }// Input Validation is OK go on ---------------------------------------------
        // find Gift Voucher by its id
        $GiftVoucher = GiftVoucher::find($input['giftvoucher_id']);

        // check if Gift Voucher is exists
        if (is_null($GiftVoucher)) {
            // Response bad request with error description
            return $this->respondBadRequest('The gift voucher id is invalide.');
        }

        // check input business logic ------------------------------------------
        // check if Gift Voucher status is Valid
        if ($GiftVoucher['status'] != 1) {
            // Response bad request with error description
            return $this->respondBadRequest('The gift voucher is NOT valid.');
        }

        // check if Gift Voucher exseeds its NoOfUses limit
        if ($GiftVoucher->parameter->NoOfUses != null) { // if voucher is limited use
            if ($GiftVoucher->used_times >= $GiftVoucher->parameter->NoOfUses) {
                // Response bad request with error description
                return $this->respondBadRequest('The gift voucher Allowed Validations limit reached.');
            }
        }

        // check if value less than $1.00
        if ($input['value'] < 1) {
            // Response bad request with error description
            return $this->respondBadRequest('The validation value must be greater than or equal to $1.00.');
        }

        // check if value greater than voucher_balance
        if ($input['value'] > $GiftVoucher->voucher_balance) {
            // Response bad request with error description
            return $this->respondBadRequest('The validation value is greater than voucher balance.');
        }

        // 
        // Create Gift Voucher Validation Log ==================================
        //
        // Gathering data
        // at this point items in $input array are: giftvoucher_id, value 
        $input['user_id'] = Auth::user()->id;
        $input['date'] = date("Y-m-d H:i:s"); //  no need created_at field is enough
        $input['balance'] = $GiftVoucher->voucher_balance - $input['value'];
        // build log string ----------------------------------------------------
        $isValidated = false;
        //
        if ($input['balance'] <= 0) {
            $isValidated = true;
        }
        //
        $input_GiftVoucher['used_times'] = $GiftVoucher->used_times + 1;
        if ($GiftVoucher->parameter->NoOfUses != null) { // if voucher is limited use
            if ($input_GiftVoucher['used_times'] >= $GiftVoucher->parameter->NoOfUses) {
                $isValidated = true;
            }
        }
        // if voucher is single use 
        if ($GiftVoucher->parameter->NoOfUses == 1) {
            // set validation value = voucher_value
            $input['value'] = $GiftVoucher->voucher_value;
            // set validation balance = voucher balance = 0
            $input['balance'] = 0;
            $isValidated = true;
        }
        //
        if ($isValidated) {
            $input_GiftVoucher['status'] = 2; // Validated            
            $input['log'] = 'Validated';
        } else {
            $input_GiftVoucher['status'] = 1; // Still Valid
            $input['log'] = 'Valid';
        }
        // Create Validation Log
        $GiftVoucherValidation = GiftVoucherValidation::create($input);
        //
        // if Create Validation Log fails
        if (!$GiftVoucherValidation) {
            // Response Internal Error with error description
            return $this->respondInternalError('Can NOT validate the voucher.');
        }
        // Create Validation Log OK, go on -------------------------------------
        // 
        // Update Gift Voucher =================================================
        //
        // Gathering data
        // at this point items in $input_GiftVoucher array are: used_times, status 
        $input_GiftVoucher['voucher_balance'] = $input['balance'];
        $input_GiftVoucher['validation_date'] = $input['date'];
        // Update Gift Voucher
        // if update gift voucher fails
        if (!$GiftVoucher->update($input_GiftVoucher)) {
            // Response Internal Error with error description
            return $this->respondInternalError('Can NOT update voucher with validation data.');
        }
        // Update Gift Voucher OK, go on ---------------------------------------
        //                         
        // Response with data
        return $this->display($GiftVoucherValidation);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //
    }

}
