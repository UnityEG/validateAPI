<?php

namespace App\Http\Requests\Business\BusinessLogos;

use App\Http\Models\BusinessLogo;
use App\Http\Requests\Request;

class DestroyBusinessLogoRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * Users belongs to the business that the logo had been created for are authorized.
     * Users belongs to the groups that have the rule "business_logo_destroy" are authorized.
     * @return bool
     */
    public function authorize()
    {
        $response = FALSE;
        $business_logo_object = BusinessLogo::findOrFail((int)$this->route()->getParameter('BusinessLogos'));
        (!$business_logo_object->business->users()->where('user_id', $this->CurrentUserObject->id)->exists()) ? : $response = TRUE;
        (!$this->CurrentUserObject->hasRule( 'business_logo_destroy')) ?  : $response = TRUE;
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
            //
        ];
    }
}
