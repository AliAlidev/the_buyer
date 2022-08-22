<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiAuthController extends Controller
{
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
        return $response->json();
    }
}
