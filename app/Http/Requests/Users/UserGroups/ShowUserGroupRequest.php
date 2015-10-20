<?php

namespace App\Http\Requests\Users\UserGroups;

use App\Http\Requests\Request;

class ShowUserGroupRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * Only users belong to the group that has the rule 'user_group_show' will be authorized
     * @return bool
     */
    public function authorize()
    {
       return ($this->CurrentUserObject->hasRule( 'user_group_show')) ? TRUE : FALSE;
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
