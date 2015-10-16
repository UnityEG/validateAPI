<?php

namespace App\Http\Requests\Business;

use App\Http\Requests\Request;

class IndexBusinessRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * Only users that belong to the group has the rule "business_show_all" will be authorized.
     * @return bool
     */
    public function authorize()
    {
        return ($this->CurrentUserObject->hasRule( 'business_show_all')) ? TRUE : FALSE;
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
