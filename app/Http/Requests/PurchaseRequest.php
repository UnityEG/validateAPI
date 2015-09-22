<?php

namespace App\Http\Requests;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class PurchaseRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return JWTAuth::parseToken()->authenticate()->hasRule('purchase_voucher');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
//        dd($this->request->get( 'data' )[0]['recipient_email']);
        $rules = [];
        foreach ( $this->request->get( 'data' ) as $key=>$voucher_to_purchase ) {
            $rules_to_merge = [
                'data.'.$key.'.relations.user.data.user_id' => 'required|integer|exists:users,id',
                'data.'.$key.'.relations.voucher_parameter.data.voucher_parameter_id' => 'required|integer|exists:voucher_parameters,id',
               'data.'.$key.'.value'                => 'required|numeric|min:1',
                'data.'.$key.'.delivery_date'        => 'date_format:d/m/Y',
                'data.'.$key.'.recipient_email'      => 'email',
                'data.'.$key.'.message'              => 'string',
            ];
            $rules = array_merge($rules, $rules_to_merge);
        }//foreach ( $this->request->get( 'data' ) as $key=>$voucher_to_purchase )
        return $rules;
    }
    
    /**
     * Customize error messages
     * @return array
     */
    public function messages( ) {
        $error_messages = [];
        foreach ( $this->request->get('data') as $key => $voucher_to_purchase ) {
            $error_messages_to_merge = [
                'data.'.$key.'.relations.user.data.user_id.required' => 'user_id is required',
                'data.'.$key.'.relations.user.data.user_id.integer' => 'user_id must be an integer',
                'data.'.$key.'.relations.user.data.user_id.exists' => 'invalid user',
                'data.'.$key.'.relations.voucher_parameter.data.voucher_parameter_id.required' => 'voucher_parameter_id is required',
                'data.'.$key.'.relations.voucher_parameter.data.voucher_parameter_id.integer' => 'voucher_parameter_id must be integer',
                'data.'.$key.'.relations.voucher_parameter.data.voucher_parameter_id.exists' => 'invalid voucher',
                'data.'.$key.'.value.required' => 'value is required',
                'data.'.$key.'.value.numeric' => 'value must be numeric',
                'data.'.$key.'.delivery_date.date_format' => 'format must be d/m/Y H:i',
                'data.'.$key.'.recipient_email.email' => 'recipient_email must be valid email address',
                'data.'.$key.'.message.string' => 'message must be string',
            ];
            $error_messages = array_merge($error_messages_to_merge, $error_messages);
        }//foreach ( $this->request->get('data') as $key => $voucher_to_purchase )
        return $error_messages;
    }
    
    /**
     * Customize Json Response
     * @param array $errors
     * @return JsonResponse
     */
    public function response(array $errors)
    {
        if ($this->ajax() || $this->wantsJson()) {
            return (new ApiController())->setStatusCode(417)->respondWithError('invalid parameters', $errors);
        }//if ($this->ajax() || $this->wantsJson())

        return $this->redirector->to($this->getRedirectUrl())
                                        ->withInput($this->except($this->dontFlash))
                                        ->withErrors($errors, $this->errorBag);
    }

}
