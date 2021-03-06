<?php

namespace App\Http\Requests;

use App\Http\Controllers\ApiController;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class Request extends FormRequest
{
    /**
     * Instance of current authenticated User Model
     * @var \App\User
     */
    public $CurrentUserObject;
    
    protected $ForbiddenMessage='Forbidden';


    public function __construct( ) {
        parent::__construct();
        (!JWTAuth::getToken()) ?  : $this->CurrentUserObject = JWTAuth::parseToken()->authenticate();
    }
    
    /**
     * Customize Json Response
     * @param array $errors
     * @return JsonResponse
     */
    public function response( array $errors ) {
        if ( $this->ajax() || $this->wantsJson() ) {
            return (new ApiController() )->setStatusCode( 417 )->respondWithError( 'invalid parameters', $errors );
        }//if ($this->ajax() || $this->wantsJson())

        return $this->redirector->to( $this->getRedirectUrl() )
                        ->withInput( $this->except( $this->dontFlash ) )
                        ->withErrors( $errors, $this->errorBag );
    }
    
    /**
     * Cusomize Forbidden Json response
     * @return Json
     */
    public function forbiddenResponse( ) {
        if ( $this->ajax() || $this->wantsJson() ) {
            return (new ApiController())->setStatusCode(403)->respondWithError($this->ForbiddenMessage);
        }//if ( $this->ajax() || $this->wantsJson() )
        return parent::forbiddenResponse();
    }
    
    
}
