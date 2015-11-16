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
        $data['comment'] = htmlspecialchars($data['message']);
        $result = Mail::send('email.misc.ContactUs', $data, function($message) use ($data){
            $message->to("en.mohamed.atef@gmail.com", "Validate Customer Service")
            	    ->from("donotreply@validate.co.nz", "donotreply@validate.co.nz")
                    ->subject("Message Via Validate Website")
                    ->replyTo($data['email']);
//            Add custom header to the message if necessary
//            $headers = $message->getHeaders();
//            $headers->addTextHeader('Header-Text', 'value');
        });
        return (1==$result) ? $this->respond( "Your message has been sent successfully and we will reply soon on ".$data['email']." Thanks") : $this->setStatusCode( 500)->respondWithError( "Internal Server Error");
    }
}
