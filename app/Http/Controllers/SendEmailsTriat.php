<?php

namespace App\Http\Controllers;

use App\Mail\SendCheckEmail;
use App\Mail\SendEmailToAdmin;
use App\Mail\SendForgotPasswordEmail;
use Illuminate\Support\Facades\Mail;
use Nette\Utils\Random;

trait SendEmailsTriat
{

    public function sendEmailToAdmin()
    {
        Mail::to('ali@syrianforms.store')->send(new SendEmailToAdmin);
    }

    public function sendCheckEmail($email, $code)
    {
        Mail::to($email)->send(new SendCheckEmail($code));
    }

    public function sendForgotPasswordEamil($email, $url)
    {
        Mail::to($email)->send(new SendForgotPasswordEmail($url));
    }
}
