<?php

namespace App\Http\Controllers\UsersControllers;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Users\LoginUserRequest;
use Auth;
use GeneralHelperTools;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use JWTAuth;
use Psy\Util\Json;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateController extends ApiController {
    
    /**
     * Login user and return with user information with token or error
     * @param LoginUserRequest $request
     * @return Response
     */
    public function authenticate(  LoginUserRequest $request) {
        $raw_input = $request->json("data");
        $credentials['email'] = GeneralHelperTools::arrayKeySearchRecursively( $raw_input, 'email');
        $credentials['password'] = GeneralHelperTools::arrayKeySearchRecursively($raw_input, 'password');
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
     * @param Request $request
     * @param Client $client
     */
    public function facebook(Request $request)
    {
        $accessTokenUrl = 'https://graph.facebook.com/v2.3/oauth/access_token';
        $graphApiUrl = 'https://graph.facebook.com/v2.3/me';

        $params = [
            'code' => $request->input('code'),
            'client_id' => $request->input('client_id'),
            'redirect_uri' => $request->input('redirect_uri'),
            'client_secret' => \Config::get('app.facebook_secret')
        ];
        
        $client = new \GuzzleHttp\Client();

        $client->setDefaultOption('verify', false);
// Step 1. Exchange authorization code for access token.
        $accessToken = $client->get($accessTokenUrl, ['query' => $params])->json();

// Step 2. Retrieve profile information about the current user.
        $profile = $client->get($graphApiUrl, ['query' => $accessToken])->json();

        return response()->json(["data" => ["profile" => $profile]]);

    }
}
