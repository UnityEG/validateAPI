<?php

namespace App\Http\Requests\Business;

use App\Http\Requests\Request;
// todo remove this class to BusinessLogos directory
class StoreBusinessLogoRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * Users belongs to the business that the logo is going to be created for are authorized.
     * Users belongs to groups that have rule 'business_logo_store' rule are authorized.
     * @return bool
     */
    public function authorize()
    {
        $response = FALSE;
//        todo waiting for business_id to check if the current user belongs to this business or not
        (!$this->CurrentUserObject->hasRule( 'business_logo_store')) ?  : $response = TRUE;
        return $response;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "business_logo" => ['required', 'image', 'max:2096']
        ];
    }
}
