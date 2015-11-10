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
            $message->to("therock_624@hotmail.com", "Mohamed Atef")->subject('Comments from '.$data['email'])->replyTo($data['email']);
        });
        return $result;
    }
}
