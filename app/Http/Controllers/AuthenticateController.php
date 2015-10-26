<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\LoginUserRequest;
use App\Http\Controllers\ApiController;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\EssentialEntities\GeneralHelperTools;

class AuthenticateController extends ApiController {
    
    /**
     * Login user and return with user information with token or error
     * @param \App\Http\Requests\Users\LoginUserRequest $request
     * @param \App\EssentialEntities\GeneralHelperTools $general_helper_tools
     * @return Response
     */
    public function authenticate(  LoginUserRequest $request, GeneralHelperTools $general_helper_tools) {
        $raw_input = $request->json("data");
        $credentials['email'] = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'email');
        $credentials['password'] = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'password');
        // verify the credentials and create a token for the user
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new JWTException('invalid credentials', 417);
        }//if (!$token = JWTAuth::attempt($credentials))
        $user = Auth::user()->getStandardJsonFormat();
        $user["data"]["token"] = $token;
        return $user;
    }
    
    /**
     * logout method
     * @return Json response
     */
    public function logout( ) {
        $this->middleware('jwt.auth');
        if(JWTAuth::invalidate(JWTAuth::getToken())){
            return $this->respond('token became invalid');
        }//if(JWTAuth::invalidate(JWTAuth::getToken()))
    }
}
