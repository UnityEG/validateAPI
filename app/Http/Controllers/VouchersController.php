<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
//Controllers
use App\Http\Controllers\ApiController;
//Models
use App\Http\Models\Voucher;

class VouchersController extends ApiController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $all_sold_vouchers = Voucher::orderBy( 'created_at', 'desc' )->get();

        return $this->respond( $all_sold_vouchers );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $input
     * @return Response
     */
    public function store( $input ) {
        $validation_rules = [
//            'user_id'              => 'required|integer|exists:user,id',
//            'voucher_parameter_id' => 'required|integer|exists:voucher_parameters,id',
//            'status' =>'required|in:valid,invalid,validated',
//            'code'=>'reqired|integer|size:9',
            'value'                => 'sometimes|required|numeric',
//            'balance'              => 'sometimes|required|numeric',
//            'is_gift'              => 'boolean',
            'delivery_date'        => 'date_format:d/m/Y H:i',
            'recipient_email'      => 'email',
            'message'              => 'string',
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show( $id ) {
        try {
            $voucher_object = Voucher::findOrFail( $id );
        } catch ( \Exception $ex ) {
            return $this->respondNotFound( $ex->getMessage() );
        }

        return $this->respond( $voucher_object );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit( $id ) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update( Request $request, $id ) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy( $id ) {
        //
    }

}
