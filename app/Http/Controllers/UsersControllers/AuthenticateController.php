<?php

namespace App\Http\Controllers\UsersControllers;

use App\Http\Requests\Users\LoginUserRequest;
use App\Http\Controllers\ApiController;
use JWTAuth;
use Auth;
use App\EssentialEntities\GeneralHelperTools\GeneralHelperTools;

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
            return $this->setStatusCode(417)->respondWithError('invalid email or password');
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
    
    /**
     * Facebook Authentication method
     * @param \Illuminate\Http\Request $request
     * @param \GuzzleHttp\Client $client
     */
    public function facebook( \Illuminate\Http\Request $request, \GuzzleHttp\Client $client) {
        $access_token_url = 'https://graph.facebook.com/v2.3/oauth/access_token';
        $graph_api_url = 'https://graph.facebook.com/v2.3/me';
        $params = [
            'code' => $request->get( 'data[code]', '', TRUE),
            'client_id' => $request->get('data[client_id]', '', TRUE),
            'redirect_uri' => $request->get('data[redirect_uri]', '', TRUE),
            'client_secret' => config('app.facebook_secret'),
        ];
//        step.1 Exchange authorization code for access token
        $access_token = $client->get($access_token_url, ['query' => $params])->json();
//        step.2 Retrieve profile information about the current user
        $profile = $client->get($graph_api_url, ['query' => $access_token])->json();
        
        return (!empty($profile)) ? $this->respond( "success logging in with facebook account") : $this->setStatusCode( 402)->respondWithError( "Error while logging in with facebook account");
    }
}
