<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|gt:5',
                'password_confirm' => 'same:password'
            ], [
                'name.required' => __('user/create_user.name_required'),
                'email.required' => __('user/create_user.email_required'),
                'email.unique' => __('user/create_user.unique_email'),
                'email.email' => __('user/create_user.email_email'),
                'password.required' => __('user/create_user.password_required'),
                'password.gt' => __('user/create_user.password_gt'),
                'password_confirm.same' => __('user/create_user.password_confirm_same')
            ]);
            if ($validator->fails()) {
                return $this->sendErrorResponse('Validation error', $validator->getMessageBag());
            }

            User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "merchant_id" => $request->merchant_id,
                "role" => $request->role,
                "phone" => $request->phone,
                "tel_phone" => $request->tel_phone,
                'email_verified_at' => now(),
                "province" => $request->province,
                "city" => $request->city,
                "address" => $request->address,
                "merchant_type" => $request->merchant_type,
                "notes" => $request->notes,
                "language" => $request->language
            ]);

            session()->put('success', __('user/create_user.user_created_successfully'));
            return $this->sendResponse(__('user/create_user.user_created_successfully'));
        }
        $merchants = User::where('role', 1)->get();
        $provinces = Province::get();
        return view('user.create_user', ['merchants' => $merchants, 'provinces' => $provinces]);
    }

    public function get_cities($provinceId)
    {
        $cities = City::where('province_id', $provinceId)->get();
        return $cities;
    }
}
