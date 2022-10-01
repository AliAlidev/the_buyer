<?php

namespace App\Http\Controllers;

use App\Mail\SendEmailToAdmin;
use Illuminate\Support\Facades\Mail;

trait SendEmailsTriat{

    public function sendEmailToAdmin()
    {
        Mail::to('ali@syrianforms.store')->send(new SendEmailToAdmin);
    }    
}