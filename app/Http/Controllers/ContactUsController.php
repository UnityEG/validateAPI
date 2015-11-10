<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactUsRequest;
use Mail;

class ContactUsController extends Controller
{
    public function contactUs(ContactUsRequest $request){
        $data = $request->get('data');
        $result = Mail::raw($data['message'], function($message) use ($data){
            $message->from("donotreply@validate.co.nz", "validate.co.nz")
                    ->sender("donotreply@validate.co.nz", "validate.co.nz")
                    ->to("en.mohamed.atef@gmail.com", "Mohamed Atef")
                    ->subject('Comments from '.$data['email'])
                    ->replyTo($data['email']);
        });
        return $result;
    }
}
