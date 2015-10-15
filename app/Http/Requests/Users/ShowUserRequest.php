<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShowUserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * User can only show his profile.
     * Users who has user_show rule can show any user's profile.
     * @return bool
     */
    public function authorize()
    {
//        todo modify mehod to use CurrentUserObject property
        $response = FALSE;
        $current_user_object = JWTAuth::parseToken()->authenticate();
        if ( $current_user_object->isActiveUser() && ((int)$current_user_object->id === (int)$this->route()->getParameter('Users'))) {
            $response =  TRUE;
        }//if ( $current_user_object->isActiveUser() && ((int)$current_user_object->id === (int)$this->route()->getParameter('Users')))
        if ( $current_user_object->hasRule('user_show') ) {
            $response = TRUE;
        }//if ( $current_user_object->hasRule('user_show') )
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
