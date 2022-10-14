<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Shape;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Exception;

class ShapeController extends Controller
{
    public function create(Request $request)
    {
        if ($request->ajax()) {
            if ($request->shape_name_ar && $request->shape_name_en) {
                $validator = Validator::make($request->all(), [
                    'shape_name_ar' => 'unique:shapes,ar_shape_name',
                    'shape_name_en' => 'unique:shapes,en_shape_name'
                ], [
                    'shape_name_ar.unique' => __('shape/create_shape.shape_name_ar_unique'),
                    'shape_name_en.unique' => __('shape/create_shape.shape_name_en_unique')
                ]);
            }
            if ($request->shape_name_ar) {
                $validator = Validator::make($request->all(), [
                    'shape_name_ar' => 'unique:shapes,ar_shape_name'
                ], ['shape_name_ar.unique' => __('shape/create_shape.shape_name_ar_unique')]);
            }
            if ($request->shape_name_en) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'shape_name_en' => 'unique:shapes,en_shape_name'
                    ],
                    ['shape_name_ar.unique' => __('shape/create_shape.shape_name_en_unique')]
                );
            }
            if (isset($validator) && $validator->fails()) {
                return $this->sendErrorResponse('Validation error', $validator->getMessageBag());
            } else if (!isset($validator)) {
                return $this->sendErrorResponse('Validation error', [__('shape/create_shape.you_should_add_shape_data')]);
            }


            Shape::create([
                'ar_shape_name' => $request->shape_name_ar,
                'en_shape_name' => $request->shape_name_en,
                'merchant_type' => $request->type,
                'shape_id' => $this->get_unique_shape_id()
            ]);

            session()->put('success', __('shape/create_shape.shape_created_successfully'));
            return $this->sendResponse(__('shape/create_shape.shape_created_successfully'));
        }
        return view('shape.create_shape');
    }

    public function get_unique_shape_id()
    {
        $shape = Shape::where('shape_id', '!=', null)->orderBy('id', 'desc')->first();
        $shape_id = intval($shape->shape_id) + 1;
        while (1) {
            if (Shape::where('shape_id', $shape_id)->count() == 0)
                return $shape_id;
            else {
                $shape_id++;
            }
        }
    }

    public function list_shapes(Request $request)
    {
        if ($request->ajax()) {
            $shapes = Shape::orderBy('created_at', 'desc');
            if ($request->merchant_type) {
                $shapes = $shapes->where('merchant_type', $request->merchant_type);
            }
            return Datatables::of($shapes)
                ->addIndexColumn()
                ->editColumn('merchant_type', function ($row) {
                    if ($row->merchant_type == 1)
                        return __('shape/create_shape.merchant_type_pharmacy');
                    else  if ($row->merchant_type == 2)
                        return __('shape/create_shape.merchant_type_market');
                })
                ->addColumn('action', function ($row) {
                    if ($this->getCurrentLanguage() == "en") {
                        $btn = '<a href=' . route('update-shape', $row->id) . ' class="edit btn btn-primary btn-sm mt-2 ml-3 mr-3"><i class="mdi mdi-square-edit-outline"></i></a>';
                        $btn .= '<a id=' . $row->id . ' class="delete btn btn-danger btn-sm mt-2" style="margin-left:4%"><i class="mdi mdi-delete"></i></a>';
                        $btn .= '<a href=' . route('show-shape', $row->id) . ' class="btn btn-info btn-sm mt-2" style="margin-left:4%"><i class="far fa-eye"></i></a>';
                    } else if ($this->getCurrentLanguage() == "ar") {
                        $btn = '<a href=' . route('update-shape', $row->id) . ' class="edit btn btn-primary waves-effect waves-light btn-sm mt-2 ml-3 mr-3"><i class="mdi mdi-square-edit-outline"></i></a>';
                        $btn .= '<a id=' . $row->id . ' class="delete btn btn-danger waves-effect waves-light btn-sm mt-2" style="margin-right:4%"><i class="mdi mdi-delete"></i></a>';
                        $btn .= '<a href=' . route('show-shape', $row->id) . ' class="btn btn-info btn-sm mt-2" style="margin-right:4%"><i class="far fa-eye"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('shape.list_shapes');
    }

    public function show_shape($id)
    {
        $shape = Shape::find($id);
        return view('shape.show_shape', ['shape' => $shape]);
    }

    public function update_shape(Request $request, $id)
    {
        if ($request->ajax()) {
            if ($request->shape_name_ar && $request->shape_name_en) {
                $validator = Validator::make($request->all(), [
                    'shape_name_ar' => 'unique:shapes,ar_shape_name,' . $id,
                    'shape_name_en' => 'unique:shapes,en_shape_name,' . $id
                ], [
                    'shape_name_ar.unique' => __('shape/create_shape.shape_name_ar_unique'),
                    'shape_name_en.unique' => __('shape/create_shape.shape_name_en_unique')
                ]);
            }
            if ($request->shape_name_ar) {
                $validator = Validator::make($request->all(), [
                    'shape_name_ar' => 'unique:shapes,ar_shape_name,' . $id
                ], ['shape_name_ar.unique' => __('shape/create_shape.shape_name_ar_unique')]);
            }
            if ($request->shape_name_en) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'shape_name_en' => 'unique:shapes,en_shape_name,' . $id
                    ],
                    ['shape_name_ar.unique' => __('shape/create_shape.shape_name_en_unique')]
                );
            }
            if (isset($validator) && $validator->fails()) {
                return $this->sendErrorResponse('Validation error', $validator->getMessageBag());
            } else if (!isset($validator)) {
                return $this->sendErrorResponse('Validation error', [__('shape/create_shape.you_should_add_shape_data')]);
            }

            Shape::where('id', $id)->update([
                'ar_shape_name' => $request->shape_name_ar,
                'en_shape_name' => $request->shape_name_en
            ]);

            session()->put('success', __('shape/update_shape.shape_updated_successfully'));
            return $this->sendResponse(__('shape/update_shape.shape_updated_successfully'));
        }
        $shape = Shape::find($id);
        return view('shape.update_shape', ['shape' => $shape]);
    }

    public function deleteshape(Request $request)
    {
        try {
            $shape = Shape::find($request->id);
            if ($shape) {
                $shape->delete();
                return response()->json(['success' => true, 'message' => __('shape/list_shapes.shape_deleted_successfully')]);
            }
            return redirect()->route('list-items')->withErrors('shape not found');
        } catch (Exception $th) {
            return $this->errors("ShapeController@deleteshape", $th->getMessage());
        }
    }

    public function translate_to_ar(Request $request)
    {
        try {
            $tr = new GoogleTranslate('ar');
            return $tr->translate($request->word);
        } catch (Exception $th) {
            //throw $th;
        }
    }

    public function translate_to_en(Request $request)
    {
        try {
            $tr = new GoogleTranslate('en');
            return $tr->translate($request->word);
        } catch (Exception $th) {
            //throw $th;
        }
    }
}
