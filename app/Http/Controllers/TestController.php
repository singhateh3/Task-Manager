<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    public function SendTestMail()
    {
        $detail = [
            'title' => "Test email",
            'message' => "Now you understand how mails work with mailtrap"
        ];

        Mail::to('reciepient@example.com')->send(new TestMail($detail));
    }
}
