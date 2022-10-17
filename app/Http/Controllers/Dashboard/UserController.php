<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Company;
use App\Models\Data;
use App\Models\Province;
use App\Models\Shape;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Exception;
use Illuminate\Support\Facades\DB;

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
                        $btn = '<a href=' . route('update-user', $row->id) . ' class="edit btn btn-primary btn-sm mt-2 ml-3 mr-3"><i class="mdi mdi-square-edit-outline"></i></a>';
                        $btn .= '<a id=' . $row->id . ' class="delete btn btn-danger btn-sm mt-2" style="margin-left:4%"><i class="mdi mdi-delete"></i></a>';
                        $btn .= '<a href=' . route('show-user', $row->id) . ' class="btn btn-info btn-sm mt-2" style="margin-left:4%"><i class="far fa-eye"></i></a>';
                        $btn .= '<a href=' . route('list_assigned_products', $row->merchant_id) . ' class="btn btn-info btn-sm mt-2" style="margin-left:4%"><i class="fab fa-product-hunt"></i></a>';
                    } else if ($this->getCurrentLanguage() == "ar") {
                        $btn = '<a href=' . route('update-user', $row->id) . ' class="edit btn btn-primary waves-effect waves-light btn-sm mt-2 ml-3 mr-3"><i class="mdi mdi-square-edit-outline"></i></a>';
                        $btn .= '<a id=' . $row->id . ' class="delete btn btn-danger waves-effect waves-light btn-sm mt-2" style="margin-right:4%"><i class="mdi mdi-delete"></i></a>';
                        $btn .= '<a href=' . route('show-user', $row->id) . ' class="btn btn-info btn-sm mt-2" style="margin-right:4%"><i class="far fa-eye"></i></a>';
                        $btn .= '<a href=' . route('list_assigned_products', $row->merchant_id) . ' class="btn btn-info btn-sm mt-2" style="margin-right:4%"><i class="fab fa-product-hunt"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $provinces = Province::get();
        return view('user.list_users', ['provinces' => $provinces]);
    }

    public function list_assigned_products(Request $request, $id)
    {
        if ($request->ajax()) {
            $exp = 'select `data`.*, `companies`.`ar_comp_name`, `shapes`.`ar_shape_name` from `data` left join `companies` on `companies`.`comp_id` = `data`.`comp_id` left join `shapes` on `shapes`.`shape_id` = `data`.`shape_id` where data.id in (select data_id from user_data where merchant_id=' . $id . ')';
            if ($request->comp_id) {
                $exp .= ' and data.comp_id=' . $request->comp_id;
            }
            if ($request->shape_id) {
                $exp .= ' and data.shape_id=' . $request->shape_id;
            }
            $data = DB::select(DB::raw($exp));
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('merchant_type', function ($row) {
                    if ($row->merchant_type == 1)
                        return __('product/create_product.merchant_type_pharmacy');
                    else  if ($row->merchant_type == 2)
                        return __('product/create_product.merchant_type_market');
                })
                ->addColumn('action', function ($row) {
                    if ($this->getCurrentLanguage() == "en") {
                        $btn = '<a href=' . route('edit-product', $row->id) . ' class="edit btn btn-primary btn-sm mt-2 ml-3 mr-3"><i class="mdi mdi-square-edit-outline"></i></a>';
                        $btn .= '<a id=' . $row->id . ' class="delete btn btn-danger btn-sm mt-2" style="margin-left:4%"><i class="mdi mdi-delete"></i></a>';
                        $btn .= '<a href=' . route('show-product', $row->id) . ' class="btn btn-info btn-sm mt-2" style="margin-left:4%"><i class="far fa-eye"></i></a>';
                    } else if ($this->getCurrentLanguage() == "ar") {
                        $btn = '<a href=' . route('edit-product', $row->id) . ' class="edit btn btn-primary waves-effect waves-light btn-sm mt-2 ml-3 mr-3"><i class="mdi mdi-square-edit-outline"></i></a>';
                        $btn .= '<a id=' . $row->id . ' class="delete btn btn-danger waves-effect waves-light btn-sm mt-2" style="margin-right:4%"><i class="mdi mdi-delete"></i></a>';
                        $btn .= '<a href=' . route('show-product', $row->id) . ' class="btn btn-info btn-sm mt-2" style="margin-right:4%"><i class="far fa-eye"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $companies = Company::get();
        $shapes = Shape::get();
        return view('product.list_assigned_products', ['companies' => $companies, 'shapes' => $shapes]);
    }

    public function get_cities($provinceId)
    {
        $cities = City::where('province_id', $provinceId)->get();
        return $cities;
    }

    public function show_user($user_id)
    {
        $merchants = User::where('role', 1)->get();
        $provinces = Province::get();
        $cities = City::get();
        $user = User::find($user_id);
        return view('user.show_user', ['user' => $user, 'merchants' => $merchants, 'provinces' => $provinces, 'cities' => $cities]);
    }

    public function update_user(Request $request, $id)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id,
                'password_confirm' => 'same:password'
            ], [
                'name.required' => __('user/update_user.name_required'),
                'email.required' => __('user/update_user.email_required'),
                'email.unique' => __('user/update_user.unique_email'),
                'email.email' => __('user/update_user.email_email'),
                'password.required' => __('user/update_user.password_required'),
                'password.gt' => __('user/update_user.password_gt'),
                'password_confirm.same' => __('user/update_user.password_confirm_same')
            ]);
            if ($validator->fails()) {
                return $this->sendErrorResponse('Validation error', $validator->getMessageBag());
            }

            User::where('id', $id)->update([
                "name" => $request->name,
                "email" => $request->email,
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

            if ($request->password) {
                User::where('id', $id)->update([
                    "password" => Hash::make($request->password)
                ]);
            }

            session()->put('success', __('user/update_user.user_updated_successfully'));
            return $this->sendResponse(__('user/update_user.user_updated_successfully'));
        }
        $merchants = User::where('role', 1)->get();
        $provinces = Province::get();
        $user = User::find($id);
        $cities = City::where('province_id', $user->province)->get();
        return view('user.update_user', ['merchants' => $merchants, 'provinces' => $provinces, 'user' => $user, 'cities' => $cities]);
    }

    public function delete_user(Request $request)
    {
        try {
            $user = User::find($request->id);
            if ($user) {
                $user->delete();
                return response()->json(['success' => true, 'message' => __('user/list_users.user_deleted_successfully')]);
            }
            return redirect()->route('list-items')->withErrors('User not found');
        } catch (Exception $th) {
            return $this->errors("UserController@delete_user", $th->getMessage());
        }
    }

    public function products_assign(Request $request)
    {
        if ($request->ajax()) {
            $exp = 'select `data`.*, `companies`.`ar_comp_name`, `shapes`.`ar_shape_name` from `data` left join `companies` on `companies`.`comp_id` = `data`.`comp_id` left join `shapes` on `shapes`.`shape_id` = `data`.`shape_id` where 1';
            if ($request->comp_id) {
                $exp .= ' and data.comp_id=' . $request->comp_id;
            }
            if ($request->shape_id) {
                $exp .= ' and data.shape_id=' . $request->shape_id;
            }
            if ($request->merchant_type) {
                $exp .= ' and data.merchant_type=' . $request->merchant_type;
            }

            if ($request->merchant_id) {
                $merchant = User::find($request->merchant_id);
                $exp .= ' and data.merchant_type=' . $merchant->merchant_type;
                $exp .= ' and data.id not in (' . implode(',', $merchant->data()->pluck('data.id')->toArray()) . ')';
            }
            $data = DB::select(DB::raw($exp));

            // $data = DB::table('data')->leftJoin('companies', 'companies.comp_id', '=', 'data.comp_id')->leftJoin('shapes', 'shapes.shape_id', '=', 'data.shape_id')->select('data.*', 'companies.ar_comp_name', 'shapes.ar_shape_name');
            // if ($request->comp_id) {
            //     $data = $data->where('data.comp_id', $request->comp_id);
            // }
            // if ($request->shape_id) {
            //     $data = $data->where('data.shape_id', $request->shape_id);
            // }
            // if ($request->merchant_type) {
            //     $data = $data->where('data.merchant_type', $request->merchant_type);
            // }
            // if ($request->merchant_id) {
            //     $userData = $merchant->data()->pluck('data.id')->toArray();
            //     $data = $data->whereNotIn('data.id', $userData);
            // }
            return DataTables::of($data)
                ->addColumn('check', function () {
                })
                ->editColumn('merchant_type', function ($row) {
                    if ($row->merchant_type == 1)
                        return __('product/create_product.merchant_type_pharmacy');
                    else  if ($row->merchant_type == 2)
                        return __('product/create_product.merchant_type_market');
                })
                ->rawColumns(['check'])
                ->make(true);
        }

        $merchants = User::where('role', 1)->get();
        $companies = Company::get();
        $shapes = Shape::get();
        return view('user.assign_products', ['companies' => $companies, 'shapes' => $shapes, 'merchants' => $merchants]);
    }

    public function assing_user_products(Request $request)
    {
        // assign_type: (1 -> all products), (2 -> pharmacy products), (3 -> market products), (4 -> custome)
        $validator = Validator::make($request->all(), [
            'assign_type' => 'required',
            'merchant_id' => 'required'
        ]);
        if ($validator->fails())
            return $this->sendErrorResponse('Validation error', $validator->getMessageBag());

        $assign_type = $request->assign_type;
        $merchant_id = $request->merchant_id;
        $data = json_decode($request->data);
        $merchant = User::find($merchant_id);
        if ($assign_type == 1) {
            $data = Data::pluck('id')->toArray();
            $merchant->data()->attach($data);
        } elseif ($assign_type == 2) {
            $data = Data::where('merchant_type', 1)->pluck('id')->toArray();
            $merchant->data()->attach($data);
        } elseif ($assign_type == 3) {
            $data = Data::where('merchant_type', 2)->pluck('id')->toArray();
            $merchant->data()->attach($data);
        } elseif ($assign_type == 4) {
            $merchant->data()->attach($data);
        }
        return $this->sendResponse(__('user/assign_products.products_assigned_successfully'));
    }
}
