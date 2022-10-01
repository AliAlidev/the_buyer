<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompResource;
use App\Http\Resources\EffMatResouce;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShapeResource;
use App\Http\Resources\TreatementGroupResource;
use App\Models\Amount;
use App\Models\Company;
use App\Models\Data;
use App\Models\EffMaterial;
use App\Models\Invoice;
use App\Models\Shape;
use App\Models\TreatementGroup;
use App\Models\User;
use App\Models\UserData;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiProductController extends Controller
{
    public function listMerchantData(Request $request)
    {
        $length = $request->query('lenght');
        $only_names = $request->query('only_names');
        $user = Auth::guard('api')->user();
        $filter = $only_names ? 'data.name' : '*';
        $page = $request->page ?? 0;

        if ($filter != '*' && $length != '*') {
            $data = DB::table('data')->join('user_data', 'user_data.data_id', '=', 'data.id')
                ->where('user_data.merchant_id', $user->merchant_id)
                ->select($filter)
                ->limit($length)
                ->skip($page)
                ->pluck('data.name')
                ->toArray();
        } else if ($filter == '*' && $length != '*') {
            $data = DB::table('data')->join('user_data', 'user_data.data_id', '=', 'data.id')
                ->where('user_data.merchant_id', $user->merchant_id)
                ->select($filter)
                ->limit($length)
                ->skip($page)
                ->get();
        } else if ($filter != '*' && $length == '*') {
            $data = DB::table('data')->join('user_data', 'user_data.data_id', '=', 'data.id')
                ->where('user_data.merchant_id', $user->merchant_id)
                ->select($filter)
                ->pluck('data.name')
                ->toArray();
        } else if ($filter == '*' && $length == '*') {
            $data = DB::table('data')->join('user_data', 'user_data.data_id', '=', 'data.id')
                ->where('user_data.merchant_id', $user->merchant_id)
                ->select($filter)
                ->get();
        }

        return $this->sendResponse('Proccess completed succssfully', $data);
    }

    public function listData(Request $request)
    {
        $length = $request->query('lenght');
        $only_names = $request->query('only_names');
        $user = Auth::guard('api')->user();
        $filter = $only_names ? 'data.name' : '*';
        $page = $request->page ?? 0;

        if ($filter != '*' && $length != '*') {
            $data = DB::table('data')->join('user_data', 'user_data.data_id', '=', 'data.id')
                ->select($filter)
                ->limit($length)
                ->skip($page)
                ->pluck('data.name')
                ->toArray();
        } else if ($filter == '*' && $length != '*') {
            $data = DB::table('data')->join('user_data', 'user_data.data_id', '=', 'data.id')
                ->select($filter)
                ->limit($length)
                ->skip($page)
                ->get();
        } else if ($filter != '*' && $length == '*') {
            $data = DB::table('data')->join('user_data', 'user_data.data_id', '=', 'data.id')
                ->select($filter)
                ->pluck('data.name')
                ->toArray();
        } else if ($filter == '*' && $length == '*') {
            $data = DB::table('data')->join('user_data', 'user_data.data_id', '=', 'data.id')
                ->select($filter)
                ->get();
        }

        return $this->sendResponse('Proccess completed succssfully', $data);
    }

    public function store(Request $request)
    {
        if ($request->code != null) {
            $validator = Validator::make($request->all(), [
                'code' => 'unique:data,code',
                'name' => 'required|unique:data,name'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:data,name'
            ]);
        }

        if ($validator->fails()) {
            return $this->sendErrorResponse('Validation errors', $validator->getMessageBag());
        }

        // if ($request->quantity == 0 && $request->quantityparts == 0)
        //     return $this->sendErrorResponse('Validation error', ['amount' => 'You should enter amounts']);

        if ($request->quantity != 0 && $request->price == 0)
            return $this->sendErrorResponse('Validation error', ['amount' => 'You should enter price']);

        if ($request->quantity == 0 && $request->price != 0)
            return $this->sendErrorResponse('Validation error', ['amount' => 'You should enter amount']);

        if ($request->quantityparts != 0 && $request->partprice == 0)
            return $this->sendErrorResponse('Validation error', ['amount' => 'You should enter part price']);

        if ($request->quantityparts == 0 && $request->partprice != 0)
            return $this->sendErrorResponse('Validation error', ['amount' => 'You should enter part part amount']);

        if ($request->start_date != null && $request->duration_value != null) {
            if ($request->duration_type == "Date") {
                $expiry_date = $request->duration_value;
                if (Carbon::parse($request->start_date)->greaterThan(Carbon::parse($expiry_date)))
                    return $this->sendErrorResponse('Validation error', ['price' => 'Expiry date should be greater than start date']);
            } else if ($request->duration_type == "Months") {
            }
        }

        if (isset($request->hasparts) && $request->numofparts == 0) {
            return $this->sendErrorResponse('Validation error', ['amount' => 'You should enter the number of parts for this item']);
        }

        $user = Auth::guard('api')->user();
        //////////////////////////////////////////////////////  Pharmacist  //////////////////////////////////////////////////////
        if ($user->merchant_type == 1) {
            try {
                $has_parts = isset($request->hasparts) ? 1 : 0;
                $num_of_parts = $request->numofparts != null ? $request->numofparts : 0;
                $minimum_amount = $request->minimum_amount == null ? 0 : $request->minimum_amount;
                $maximum_amount = $request->maximum_amount == null ? 0 : $request->maximum_amount;

                $data = Data::Create([
                    'code' => $request->code,
                    'name' => $request->name,
                    'shape_id' => $request->shape_id == null ? 0 : $request->shape_id,
                    'comp_id' => $request->comp_id == null ? 0 : $request->comp_id,
                    'has_parts' => $has_parts,
                    'num_of_parts' => $num_of_parts,
                    'description' => $request->description,
                    'minimum_amount' => $minimum_amount,
                    'maximum_amount' => $maximum_amount,
                    'dose' => $request->dose,
                    'tab_count' => $request->tab_count,
                    'treatements' => $request->treatements,
                    'special_alarms' => $request->special_alarms,
                    'interference' => $request->interference,
                    'side_effects' => $request->side_effects,
                    'treatement_group' => $request->treatement_group,
                    'merchant_type' => $user->merchant_type,
                    'created_by' => Auth::guard('api')->user()->id
                ]);

                UserData::create([
                    'user_id' => $user->id,
                    'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id,
                    'data_id' => $data->id
                ]);

                if (isset($request->eff_materials)) {
                    $materials =  json_decode($request->eff_materials);
                    if (is_array($materials)) {
                        foreach ($materials as $key => $material) {
                            DB::table('data_effmaterials')->insert([
                                'data_id' => $data->id,
                                'effict_matterials_id' => $material->mat,
                                'dose' => $material->dose
                            ]);
                        }
                    }
                }
            } catch (Exception $th) {
                return $this->errors("HomeController@store", $th->getMessage());
            }
        }
        //////////////////////////////////////////////////////  Market  //////////////////////////////////////////////////////
        elseif ($user->merchant_type == 2) {
            try {
                $has_parts = isset($request->hasparts) ? 1 : 0;
                $num_of_parts = $request->numofparts != null ? $request->numofparts : 0;
                $minimum_amount = $request->minimum_amount == null ? 0 : $request->minimum_amount;
                $maximum_amount = $request->maximum_amount == null ? 0 : $request->maximum_amount;

                $data = Data::Create([
                    'code' => $request->code,
                    'name' => $request->name,
                    'shape_id' => $request->shape_id == null ? 0 : $request->shape_id,
                    'comp_id' => $request->comp_id == null ? 0 : $request->comp_id,
                    'has_parts' => $has_parts,
                    'num_of_parts' => $num_of_parts,
                    'description' => $request->description,
                    'minimum_amount' => $minimum_amount,
                    'maximum_amount' => $maximum_amount,
                    'created_by' => Auth::guard('api')->user()->id
                ]);

                UserData::create([
                    'user_id' => $user->id,
                    'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id,
                    'data_id' => $data->id
                ]);
            } catch (Exception $th) {
                return $this->errors("HomeController@store", $th->getMessage());
            }
        }

        //////////////////////////////////////////////////////  ٍShared Data  //////////////////////////////////////////////////////
        if ($request->quantity != 0 || $request->quantityparts != 0) {
            $expiry_date = null;
            $duration_type = $request->duration_type;
            $duration_value = $request->duration_value;

            if ($duration_type == "Date") {
                $expiry_date = $request->duration_value;
            } else if ($duration_type == "Months") {
                $expiry_date = Carbon::parse($request->start_date)->addMonths($duration_value);
            }
            Amount::create([
                'data_id' => $data->id,
                'amount' => $request->quantity,
                'amount_part' => $request->quantityparts,
                'price' => $request->price,
                'price_part' => $request->partprice,
                'expiry_type' => $request->expiry_type,
                'start_date' => $request->start_date,
                'expiry_date' => $expiry_date,
                'user_id' => $user->id,
                'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id
            ]);
        }

        if ($data->wasRecentlyCreated) {
            return response()->json(['success' => true, 'message' => 'Product addedd successfully']);
        }
    }

    public function update(Request $request, $id_name)
    {
        // check code or name existance
        $product = Data::where('code', $id_name)->orWhere('name', $id_name)->first();
        if (!$product) {
            return $this->sendErrorResponse('Product not found!');
        }

        if ($request->code != null) {
            $validator = Validator::make($request->all(), [
                'code' => 'unique:data,code',
                'name' => 'required|unique:data,name'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:data,name'
            ]);
        }

        if ($validator->fails()) {
            return $this->sendErrorResponse('Validation errors', $validator->getMessageBag());
        }

        if ($request->quantity != 0 && $request->price == 0)
            return $this->sendErrorResponse('Validation error', ['amount' => 'You should enter price']);

        if ($request->quantity == 0 && $request->price != 0)
            return $this->sendErrorResponse('Validation error', ['amount' => 'You should enter amount']);

        if ($request->quantityparts != 0 && $request->partprice == 0)
            return $this->sendErrorResponse('Validation error', ['amount' => 'You should enter part price']);

        if ($request->quantityparts == 0 && $request->partprice != 0)
            return $this->sendErrorResponse('Validation error', ['amount' => 'You should enter part part amount']);

        if ($request->start_date != null && $request->duration_value != null) {
            if ($request->duration_type == "Date") {
                $expiry_date = $request->duration_value;
                if (Carbon::parse($request->start_date)->greaterThan(Carbon::parse($expiry_date)))
                    return $this->sendErrorResponse('Validation error', ['price' => 'Expiry date should be greater than start date']);
            } else if ($request->duration_type == "Months") {
            }
        }

        if (isset($request->hasparts) && $request->numofparts == 0) {
            return $this->sendErrorResponse('Validation error', ['amount' => 'You should enter the number of parts for this item']);
        }

        $user = Auth::guard('api')->user();
        //////////////////////////////////////////////////////  Pharmacist  //////////////////////////////////////////////////////
        if ($user->merchant_type == 1) {
            try {
                $has_parts = isset($request->hasparts) ? 1 : 0;
                $num_of_parts = $request->numofparts != null ? $request->numofparts : 0;
                $minimum_amount = $request->minimum_amount == null ? 0 : $request->minimum_amount;
                $maximum_amount = $request->maximum_amount == null ? 0 : $request->maximum_amount;

                $request->code != null ? $product->code = $request->code : null;
                $request->name != null ? $product->name = $request->name : null;
                $request->shape_id != null ? $product->shape_id = $request->shape_id : null;
                $request->comp_id != null ? $product->comp_id = $request->comp_id : null;
                $product->has_parts = $has_parts;
                $product->num_of_parts = $num_of_parts;
                $request->description != null ? $product->description = $request->description : null;
                $product->minimum_amount = $minimum_amount;
                $product->maximum_amount = $maximum_amount;
                $request->dose != null ? $product->dose = $request->dose : null;
                $request->tab_count != null ? $product->tab_count = $request->tab_count : null;
                $request->treatements != null ? $product->treatements = $request->treatements : null;
                $request->special_alarms != null ? $product->special_alarms = $request->special_alarms : null;
                $request->interference != null ? $product->interference = $request->interference : null;
                $request->side_effects != null ? $product->side_effects = $request->side_effects : null;
                $request->treatement_group != null ? $product->treatement_group = $request->treatement_group : null;
                $request->treatement_group != null ? $product->treatement_group = $request->treatement_group : null;
                $product->created_by = Auth::guard('api')->user()->id;
                $product->Save();

                if (isset($request->eff_materials)) {
                    // remove old data
                    DB::table('data_effmaterials')->where('data_id', $product->id)->delete();

                    $materials =  json_decode($request->eff_materials);
                    if (is_array($materials)) {
                        foreach ($materials as $key => $material) {
                            DB::table('data_effmaterials')->insert([
                                'data_id' => $product->id,
                                'effict_matterials_id' => $material->mat,
                                'dose' => $material->dose
                            ]);
                        }
                    }
                }
            } catch (Exception $th) {
                return $this->errors("HomeController@update", $th->getMessage());
            }
        }
        //////////////////////////////////////////////////////  Market  //////////////////////////////////////////////////////
        elseif ($user->merchant_type == 2) {
            try {
                $has_parts = isset($request->hasparts) ? 1 : 0;
                $num_of_parts = $request->numofparts != null ? $request->numofparts : 0;
                $minimum_amount = $request->minimum_amount == null ? 0 : $request->minimum_amount;
                $maximum_amount = $request->maximum_amount == null ? 0 : $request->maximum_amount;

                $request->code != null ? $product->code = $request->code : null;
                $request->name != null ? $product->name = $request->name : null;
                $request->shape_id != null ? $product->shape_id = $request->shape_id : null;
                $request->comp_id != null ? $product->comp_id = $request->comp_id : null;
                $product->has_parts = $has_parts;
                $product->num_of_parts = $num_of_parts;
                $request->description != null ? $product->description = $request->description : null;
                $product->minimum_amount = $minimum_amount;
                $product->maximum_amount = $maximum_amount;
                $product->created_by = Auth::guard('api')->user()->id;
                $product->Save();
            } catch (Exception $th) {
                return $this->errors("HomeController@store", $th->getMessage());
            }
        }

        //////////////////////////////////////////////////////  ٍShared Data  //////////////////////////////////////////////////////
        if ($request->quantity != 0 || $request->quantityparts != 0) {
            $expiry_date = null;
            $duration_type = $request->duration_type;
            $duration_value = $request->duration_value;

            if ($duration_type == "Date") {
                $expiry_date = $request->duration_value;
            } else if ($duration_type == "Months") {
                $expiry_date = Carbon::parse($request->start_date)->addMonths($duration_value);
            }
            Amount::create([
                'data_id' => $product->id,
                'amount' => $request->quantity,
                'amount_part' => $request->quantityparts,
                'price' => $request->price,
                'price_part' => $request->partprice,
                'duration_type' => $request->duration_type,
                'start_date' => $request->start_date,
                'expiry_date' => $expiry_date,
                'user_id' => $user->id,
                'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id
            ]);
        }

        if ($product) {
            return $this->sendResponse('Product updated successfully', new ProductResource($product));
            // return response()->json(['success' => true, 'message' => 'Product updated successfully']);
        }
    }

    public function details($id_name)
    {
        // check code or name existance
        $product = Data::where('code', $id_name)->orWhere('name', $id_name)->first();
        if (!$product) {
            return $this->sendErrorResponse('Product not found!');
        }

        return $this->sendResponse("Proccess completed successfully", new ProductResource($product));
    }

    /*
        return all shapes depending on merchant type
    */
    public function getShapes()
    {
        $shapes = Shape::where('merchant_type', Auth::guard('api')->user()->merchant_type)->get();
        return $this->sendResponse('Proccess completed successfully', ShapeResource::collection($shapes));
    }

    public function getCompanies()
    {
        $companies = Company::where('merchant_type', Auth::guard('api')->user()->merchant_type)->get();
        return $this->sendResponse('Proccess completed successfully', CompResource::collection($companies));
    }

    public function getEffMaterials()
    {
        $effMaterials = EffMaterial::where('merchant_type', Auth::guard('api')->user()->merchant_type)->get();
        return $this->sendResponse('Proccess completed successfully', EffMatResouce::collection($effMaterials));
    }

    public function getTreatementGroup()
    {
        $treatementGroups = TreatementGroup::where('merchant_type', Auth::guard('api')->user()->merchant_type)->get();
        return $this->sendResponse('Proccess completed successfully', TreatementGroupResource::collection($treatementGroups));
    }

    public function getProductAmounts($dataId, $merchant_id = null)
    {
        if (!$merchant_id)
            $merchant_id = Auth::guard('api')->user()->merchant_id;

        $general_amounts = DB::table('amounts')->join('data', 'amounts.data_id', '=', 'data.id')->where('merchant_id', $merchant_id)->where('data_id', $dataId)->get();
        $num_of_parts = 0;
        if ($general_amounts->first()) {
            /// get inventory amounts
            $inventoryAmounts = $general_amounts->where('amount_type', 0)->sum('amount');
            $inventoryPartAmounts = $general_amounts->where('amount_type', 0)->sum('amount_part');

            /// get buyed amounts
            $buyedAmounts = $general_amounts->where('amount_type', 1)->sum('amount');
            $buyedPartAmounts = $general_amounts->where('amount_type', 1)->sum('amount_part');

            /// get selled amounts
            $selledAmounts = $general_amounts->where('amount_type', 2)->sum('amount');
            $selledPartAmounts = $general_amounts->where('amount_type', 2)->sum('amount_part');

            $totalAmounts = $inventoryAmounts + $buyedAmounts - $selledAmounts;
            $totalParts = 0;
            $num_of_parts =  $general_amounts[0]->num_of_parts;
            if ($num_of_parts && $num_of_parts > 0) {
                $temp = $totalAmounts * $num_of_parts + ($inventoryPartAmounts + $buyedPartAmounts - $selledPartAmounts);
                $totalAmounts = intval($temp / $num_of_parts);
                $totalParts = $temp % $num_of_parts;
            }
            return ['amounts' => $totalAmounts, 'part_amounts' => $totalParts, 'num_of_parts' => $num_of_parts];
        }
        return ['amounts' => 0, 'part_amounts' => 0, 'num_of_parts' => $num_of_parts];
    }

    public function getCurrentPriceForElement($dataId, $merchant_id = null)
    {
        if (!$merchant_id)
            $merchant_id = Auth::guard('api')->user()->merchant_id;
        // get max price for product
        $max_price = Amount::where('data_id', $dataId)->where('merchant_id', $merchant_id)->where('amount_type', '1')->orderBy('created_at', 'desc')->first();
        if ($max_price != null) {
            return ['price' => $max_price->price, 'part_price' => $max_price->price_part];
        } else {
            $max_price = Amount::where('data_id', $dataId)->where('merchant_id', $merchant_id)->where('amount_type', '0')->orderBy('created_at', 'desc')->first();
            if ($max_price != null)
                return ['price' => $max_price->price, 'part_price' => $max_price->price_part];
            else
                return ['price' => 0, 'part_price' => 0];
        }
    }

    public function getMaxPriceForElement($dataId, $merchant_id = null)
    {
        if (!$merchant_id)
            $merchant_id = Auth::guard('api')->user()->merchant_id;
        // get max price for product
        $max_price = DB::select("SELECT MAX(price) as price from (select MAX(created_at)as maxdatevalue, price from (select data_id, merchant_id, price, created_at from amounts where data_id = $dataId and merchant_id != $merchant_id group by data_id, merchant_id,price ORDER BY created_at DESC) as t1 GROUP BY data_id, merchant_id) as t2;");
        $max_price = $max_price != null ? $max_price[0]->price : 0;

        // get max part price for product
        $max_part_price = DB::select("SELECT MAX(price_part) as price_part from (select MAX(created_at)as maxdatevalue, price_part from (select data_id, merchant_id, price_part, created_at from amounts where data_id = $dataId and merchant_id != $merchant_id group by data_id, merchant_id,price_part ORDER BY created_at DESC) as t1 GROUP BY data_id, merchant_id) as t2;");
        $max_part_price = $max_part_price != null ? $max_part_price[0]->price_part : 0;
        if ($max_part_price != null) {
            return ['price' => $max_price, 'part_price' => $max_part_price];
        } else {
            return ['price' => 0, 'part_price' => 0];
        }

    }
}
