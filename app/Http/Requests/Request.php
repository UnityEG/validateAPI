<?php

namespace App\Http\Requests;

use App\Http\Controllers\ApiController;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class Request extends FormRequest
{
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
            return (new ApiController())->setStatusCode(403)->respondWithError('Forbidden');
        }//if ( $this->ajax() || $this->wantsJson() )
        return parent::forbiddenResponse();
    }
}
