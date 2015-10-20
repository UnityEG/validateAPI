<?php

namespace App\Http\Requests\Business;

use App\Http\Requests\Request;

class ShowBusinessRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * Only users belongs to the business OR groups that have "business_show" will be authorized
     * @return bool
     */
    public function authorize()
    {
        $response = FALSE;
       (!$this->CurrentUserObject->business()->where('business_id', (int)$this->route()->getParameter('Business'))->exists()) ? : $response = TRUE;
        (!$this->CurrentUserObject->hasRule( 'business_show')) ?  : $response = TRUE;
        return $response;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
