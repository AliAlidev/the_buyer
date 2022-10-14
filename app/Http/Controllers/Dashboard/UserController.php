<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DataTables;

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

    public function list_users(Request $request)
    {
        if ($request->ajax()) {
            $users = User::orderBy('created_at', 'desc');
            if ($request->merchant_type) {
                $users = $users->where('merchant_type', $request->merchant_type);
            }
            if ($request->province) {
                $users = $users->where('province', $request->province);
            }
            if ($request->city) {
                $users = $users->where('city', $request->city);
            }
            if ($request->role) {
                $users = $users->where('role', $request->role);
            }
            if ($request->language) {
                $users = $users->where('language', $request->language);
            }
            if ($request->merchant_type) {
                $users = $users->where('merchant_type', $request->merchant_type);
            }
            return DataTables::of($users)
                ->addIndexColumn()
                ->editColumn('merchant_type', function ($row) {
                    if ($row->merchant_type == 1)
                        return __('user/list_users.merchant_type_pharmacy');
                    else  if ($row->merchant_type == 2)
                        return __('user/list_users.merchant_type_market');
                })
                ->editColumn('role', function ($row) {
                    if ($row->role == 1)
                        return __('user/list_users.role_merchant');
                    else  if ($row->role == 2)
                        return __('user/list_users.role_employee');
                })
                ->editColumn('language', function ($row) {
                    if ($row->language == 'ar')
                        return __('user/list_users.language_ar');
                    else  if ($row->language == 'en')
                        return __('user/list_users.language_en');
                })
                ->editColumn('merchant_id', function ($row) {
                    $user = User::find($row->merchant_id);
                    return $user ? $user->name : '';
                })
                ->editColumn('province', function ($row) {
                    $province = Province::find($row->province);
                    if (strtolower(session()->get('locale')) == 'ar')
                        return $province ? $province->ar_name : '';
                    else
                        return $province ? $province->en_name : '';
                })
                ->editColumn('city', function ($row) {
                    $city = City::find($row->city);
                    if (strtolower(session()->get('locale')) == 'ar')
                        return $city ? $city->ar_name : '';
                    else
                        return $city ? $city->en_name : '';
                })
                ->addColumn('action', function ($row) {
                    if ($this->getCurrentLanguage() == "en") {
                        $btn = '<a href=' . route('update-company', $row->id) . ' class="edit btn btn-primary btn-sm mt-2 ml-3 mr-3"><i class="mdi mdi-square-edit-outline"></i></a>';
                        $btn .= '<a id=' . $row->id . ' class="delete btn btn-danger btn-sm mt-2" style="margin-left:4%"><i class="mdi mdi-delete"></i></a>';
                        $btn .= '<a href=' . route('show-company', $row->id) . ' class="btn btn-info btn-sm mt-2" style="margin-left:4%"><i class="far fa-eye"></i></a>';
                    } else if ($this->getCurrentLanguage() == "ar") {
                        $btn = '<a href=' . route('update-company', $row->id) . ' class="edit btn btn-primary waves-effect waves-light btn-sm mt-2 ml-3 mr-3"><i class="mdi mdi-square-edit-outline"></i></a>';
                        $btn .= '<a id=' . $row->id . ' class="delete btn btn-danger waves-effect waves-light btn-sm mt-2" style="margin-right:4%"><i class="mdi mdi-delete"></i></a>';
                        $btn .= '<a href=' . route('show-company', $row->id) . ' class="btn btn-info btn-sm mt-2" style="margin-right:4%"><i class="far fa-eye"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $provinces = Province::get();
        return view('user.list_users', ['provinces' => $provinces]);
    }

    public function get_cities($provinceId)
    {
        $cities = City::where('province_id', $provinceId)->get();
        return $cities;
    }
}
