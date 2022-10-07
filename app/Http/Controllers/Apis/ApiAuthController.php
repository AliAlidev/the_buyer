<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SendEmailsTriat;
use App\Http\Resources\UserResource;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Random;

class ApiAuthController extends Controller
{
    use SendEmailsTriat;
    public function login(Request $request)
    {
        $response = Http::asForm()->post(config('services.passport.base_url') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username' => $request->email,
            'password' => $request->password,
            'scope' => '',
        ]);
        $data = $response->json();
        $data['user'] = new UserResource(User::where('email', $request->email)->first());
        return $this->sendResponse('Proccess completed successfully', $data);
    }

    public function check_user_auth($email, $password)
    {
        $response = Http::asForm()->post(config('services.passport.base_url') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username' => $email,
            'password' => $password,
            'scope' => '',
        ]);
        return $response->successful();
    }

    /// in order to change password, first check_email this will send email message to the user contain secret key, second call change_password
    public function check_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email'
        ]);

        if ($validator->fails())
            return $this->sendErrorResponse("Validation errors", $validator->getMessageBag());

        $code = Random::generate(6, '0-9');
        $this->sendCheckEmail($request->email, $code);

        $user = User::where('email', $request->email)->first();
        Otp::updateOrCreate(['email' => $user->email], ['email' => $user->email, 'code' => $code]);

        return $this->sendResponse("Email has been sent, pleas check your inbox");
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'old_password' => 'required',
            'new_password' => 'required',
            'code' => 'required'
        ]);

        if ($validator->fails())
            return $this->sendErrorResponse("Validation errors", $validator->getMessageBag());

        $otp = Otp::where('email', $request->email)->first();
        if (!$otp)
            return $this->sendErrorResponse("Validation errors", "You should send otp to user email first");

        if ($otp->code != $request->code)
            return $this->sendErrorResponse("Validation errors", "Invalid code please try another one");

        $valid_date = Carbon::parse($otp->update_at)->addHours(6);
        if (Carbon::parse(now())->greaterThan($valid_date))
            return $this->sendErrorResponse("Validation errors", "Invalid code please send another one");

        $user = User::where('email', $request->email)->first();
        $newPass = Hash::make($request->new_password);
        if (!$this->check_user_auth($request->email, $request->old_password))
            return $this->sendErrorResponse("Validation errors", ['old_password' => 'Should be same as user current password']);

        User::where('email', $user->email)->update(['password' => $newPass]);

        $otp->delete();

        return $this->sendResponse("Password has been changed successfully");
    }

    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email'
        ]);

        if ($validator->fails())
            return $this->sendErrorResponse("Validation errors", $validator->getMessageBag());

        $user = User::where('email', $request->email)->first();
        $token = Random::generate(60, '0-9a-zA-Z');
        DB::table('password_resets')->insert(['email' => $user->email, 'token' => $token, 'created_at' => now()->toDateTimeString()]);

        $url = url('/') . '/recover-password/' . $token . '/' . $user->id;
        $this->sendForgotPasswordEamil($user->email, $url);
        return $this->sendResponse("Email has been sent, pleas check your inbox");
    }
}
