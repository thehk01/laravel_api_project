<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\DemoMail;

class MailController extends Controller
{
    public function index()
    {
        $mailData = [
            'title' => 'Mail from Webappfix',
            'body' => 'This is for testing email usign smtp',
        ];

        Mail::to('thehaiderali01@gmail.com')->send(new DemoMail($mailData));

        dd('Email send successfully.');
    }
}
