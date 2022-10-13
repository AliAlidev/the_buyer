<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use DataTables;
use Exception;

class CompanyController extends Controller
{
    public function create(Request $request)
    {
        if ($request->ajax()) {
            if ($request->company_name_ar && $request->company_name_en) {
                $validator = Validator::make($request->all(), [
                    'company_name_ar' => 'unique:companies,ar_comp_name',
                    'company_name_en' => 'unique:companies,en_comp_name'
                ], [
                    'company_name_ar.unique' => __('company/create_company.company_name_ar_unique'),
                    'company_name_en.unique' => __('company/create_company.company_name_en_unique')
                ]);
            }
            if ($request->company_name_ar) {
                $validator = Validator::make($request->all(), [
                    'company_name_ar' => 'unique:companies,ar_comp_name'
                ], ['company_name_ar.unique' => __('company/create_company.company_name_ar_unique')]);
            }
            if ($request->company_name_en) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'company_name_en' => 'unique:companies,en_comp_name'
                    ],
                    ['company_name_ar.unique' => __('company/create_company.company_name_en_unique')]
                );
            }
            if (isset($validator) && $validator->fails()) {
                return $this->sendErrorResponse('Validation error', $validator->getMessageBag());
            } else if (!isset($validator)) {
                return $this->sendErrorResponse('Validation error', [__('company/create_company.you_should_add_company_data')]);
            }
            Company::create([
                'ar_comp_name' => $request->company_name_ar,
                'en_comp_name' => $request->company_name_en,
                'merchant_type' => $request->type,
                'description' => $request->description,
                'comp_id' => $this->get_unique_company_id()
            ]);

            session()->put('success', __('company/create_company.company_created_successfully'));
            return $this->sendResponse(__('company/create_company.company_created_successfully'));
        }
        return view('company.create_company');
    }

    public function get_unique_company_id()
    {
        $comp = Company::where('comp_id', '!=', null)->orderBy('id', 'desc')->first();
        $comp_id = intval($comp->comp_id) + 1;
        while (1) {
            if (Company::where('comp_id', $comp_id)->count() == 0)
                return $comp_id;
            else {
                $comp_id++;
            }
        }
    }

    public function list_companies(Request $request)
    {
        if ($request->ajax()) {
            $companies = Company::orderBy('created_at', 'desc');
            if ($request->merchant_type) {
                $companies = $companies->where('merchant_type', $request->merchant_type);
            }
            return Datatables::of($companies)
                ->addIndexColumn()
                ->editColumn('merchant_type', function ($row) {
                    if ($row->merchant_type == 1)
                        return __('company/create_company.merchant_type_pharmacy');
                    else  if ($row->merchant_type == 2)
                        return __('company/create_company.merchant_type_market');
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
        return view('company.list_companies');
    }

    public function show_company($id)
    {
        $company = Company::find($id);
        return view('company.show_company', ['company' => $company]);
    }

    public function update_company(Request $request, $id)
    {
        if ($request->ajax()) {
            if ($request->company_name_ar && $request->company_name_en) {
                $validator = Validator::make($request->all(), [
                    'company_name_ar' => 'unique:companies,ar_comp_name,' . $id,
                    'company_name_en' => 'unique:companies,en_comp_name,' . $id
                ], [
                    'company_name_ar.unique' => __('company/create_company.company_name_ar_unique'),
                    'company_name_en.unique' => __('company/create_company.company_name_en_unique')
                ]);
            }
            if ($request->company_name_ar) {
                $validator = Validator::make($request->all(), [
                    'company_name_ar' => 'unique:companies,ar_comp_name,' . $id
                ], ['company_name_ar.unique' => __('company/create_company.company_name_ar_unique')]);
            }
            if ($request->company_name_en) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'company_name_en' => 'unique:companies,en_comp_name,' . $id
                    ],
                    ['company_name_ar.unique' => __('company/create_company.company_name_en_unique')]
                );
            }
            if (isset($validator) && $validator->fails()) {
                return $this->sendErrorResponse('Validation error', $validator->getMessageBag());
            } else if (!isset($validator)) {
                return $this->sendErrorResponse('Validation error', [__('company/create_company.you_should_add_company_data')]);
            }

            Company::where('id', $id)->update([
                'ar_comp_name' => $request->company_name_ar,
                'en_comp_name' => $request->company_name_en,
                'description' => $request->description
            ]);

            session()->put('success', __('company/update_company.company_updated_successfully'));
            return $this->sendResponse(__('company/update_company.company_updated_successfully'));
        }
        $company = Company::find($id);
        return view('company.update_company', ['company' => $company]);
    }

    public function deleteCompany(Request $request)
    {
        try {
            $company = Company::find($request->id);
            if ($company) {
                $company->delete();
                return response()->json(['success' => true, 'message' => __('company/list_comapnies.company_deleted_successfully')]);
            }
            return redirect()->route('list-items')->withErrors('Company not found');
        } catch (Exception $th) {
            return $this->errors("ComapnyController@deleteCompany", $th->getMessage());
        }
    }
}
