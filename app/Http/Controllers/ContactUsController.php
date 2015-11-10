<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Requests\ContactUsRequest;
use Mail;

class ContactUsController extends ApiController
{
    /**
     * Send comments message from clients to "info at validate.co.nz"
     * @param ContactUsRequest $request
     * @return Json response
     */
    public function contactUs(ContactUsRequest $request){
        $data = $request->get('data');
//        sanitize message
        $data['message'] = preg_replace('/(?:\<script\>|\<\/script\>)/', '', $data['message']);
        $data['message'] = htmlspecialchars($data['message']);
        $result = Mail::raw($data['message'], function($message) use ($data){
            $message->from("donotreply@validate.co.nz", "validate.co.nz")
                    ->sender("donotreply@validate.co.nz", "validate.co.nz")
                    ->to("info@validate.co.nz", "Validate Customer Service")
                    ->subject('Comments from '.$data['email'])
                    ->replyTo($data['email']);
        });
        return (1==$result) ? $this->respond( "Your message has been sent successfully and we will reply soon on ".$data['email']." Thanks") : $this->setStatusCode( 500)->respondWithError( "Internal Server Error");
    }
}
