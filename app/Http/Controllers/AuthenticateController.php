<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Users\LoginUserRequest;
use App\Http\Controllers\ApiController;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\User;
use App\EssentialEntities\Transformers\UserTransformer;
use App\EssentialEntities\GeneralHelperTools;
use Exception;

class AuthenticateController extends ApiController {
    
    /**
     *instance of UserTransformer class
     * @var object
     */
    private $userTransformer;
    
    /**
     *instance of JWTAuth class
     * @var object
     */
    private $JWTAuth;


    public function __construct(
    UserTransformer $user_transformer,
            JWTAuth $jwtauth
            ) {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
        $this->userTransformer = $user_transformer;
        $this->JWTAuth = $jwtauth;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        // Retrieve all the users in the database and return them
//        $users = User::all();
//        return $users;
    }
    
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function authenticate(  LoginUserRequest $request, GeneralHelperTools $general_helper_tools) {
        $raw_input = $request->json("data");
        $credentials['email'] = $general_helper_tools->arrayKeySearchRecursively( $raw_input, 'email');
        $credentials['password'] = $general_helper_tools->arrayKeySearchRecursively($raw_input, 'password');
        // verify the credentials and create a token for the user
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new JWTException('invalid credentials', 417);
        }
        $user = Auth::user()->toArray();
        $user['token'] = $token;
        // if no errors are encountered we can return a JWT
        return $this->userTransformer->transform($user);
    }
    
    /**
     * logout method
     * @return Json response
     */
    public function logout( ) {
        if(JWTAuth::invalidate(JWTAuth::getToken())){
            return $this->respondWithError('token became invalid');
        }
    }
}
