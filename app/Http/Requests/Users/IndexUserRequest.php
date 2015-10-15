<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;

class IndexUserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * Only the users belongs to the group that has 'user_show_all' rule are authorized
     * @return bool
     */
    public function authorize()
    {
        return ($this->CurrentUserObject->hasRule('user_show_all')) ? TRUE : FALSE;
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
