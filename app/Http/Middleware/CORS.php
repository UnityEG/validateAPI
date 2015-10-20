<?php

namespace App\Http\Middleware;

use Closure;

class CORS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*
         * Get the response like normal.
         * When laravel cannot find the exact route it will try to find the same route for different methods
         * If the method is OPTION and there are other methods for the uri,
         * it will then return a 200 response with an Allow header
         *
         * Else it will throw an exception in which case the user is trying to do something it should not do.
         */
        $response = $next($request);
//        we only want the headers set to the api request so we check for pattern v1 as an evidence
        if ( 'v1' == $request->segment( 1) ) {
// Set the default headers for cors If you only want this for OPTION method put this in the if below
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization');
// Set the allowed methods for the specific uri if the request method is OPTION
            (!$request->isMethod( 'options')) ? : $response->headers->set('Access-Control-Allow-Methods', $response->headers->get('Allow'));
        }//if ( 'v1' == $request->segment( 1) )
        return $response;
    }
}
