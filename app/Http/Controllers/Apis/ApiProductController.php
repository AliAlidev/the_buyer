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
use App\Models\ProductReturn;
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
    public function listMerchantInActiveData(Request $request)
    {
        return $this->listMerchantData($request, '2');
    }

    public function listMerchantData(Request $request, $active = '1')
    {
        $length = $request->query('lenght');
        $only_names = $request->query('only_names');
        $user = Auth::guard('api')->user();
        $filter = $only_names ? 'data.name' : '*';
        $page = $request->page ?? 0;

        if ($filter != '*' && $length != '*') {
            $data = DB::table('user_data')->leftJoin('data', 'user_data.data_id', '=', 'data.id')
                ->where('user_data.merchant_id', $user->merchant_id)
                ->where('data.status', $active)
                ->select($filter)
                ->limit($length)
                ->skip($page)
                ->pluck('data.name')
                ->toArray();
        } else if ($filter == '*' && $length != '*') {
            $data = DB::table('user_data')->leftJoin('data', 'user_data.data_id', '=', 'data.id')
                ->where('user_data.merchant_id', $user->merchant_id)
                ->where('data.status', $active)
                ->select($filter)
                ->limit($length)
                ->skip($page)
                ->get();
        } else if ($filter != '*' && $length == '*') {
            $data = DB::table('user_data')->leftJoin('data', 'user_data.data_id', '=', 'data.id')
                ->where('user_data.merchant_id', $user->merchant_id)
                ->where('data.status', $active)
                ->select($filter)
                ->pluck('data.name')
                ->toArray();
        } else if ($filter == '*' && $length == '*') {
            $data = DB::table('user_data')->leftJoin('data', 'user_data.data_id', '=', 'data.id')
                ->where('user_data.merchant_id', $user->merchant_id)
                ->where('data.status', $active)
                ->select($filter)
                ->get();
        }

        return $this->sendResponse('Proccess completed succssfully', $data);
    }

    public function listInActiveData(Request $request)
    {
        return $this->listData($request, '2');
    }

    public function listData(Request $request, $active = '1')
    {
        try {
            $length = $request->query('lenght');
            $only_names = $request->query('only_names');
            $filter = $only_names ? 'data.name' : '*';
            $page = $request->page ?? 0;

            if ($filter != '*' && $length != '*') {
                $data = DB::table('data')
                    ->select($filter)
                    ->where('data.status', $active)
                    ->limit($length)
                    ->skip($page)
                    ->pluck('data.name')
                    ->toArray();
            } else if ($filter == '*' && $length != '*') {
                $data = DB::table('data')
                    ->select($filter)
                    ->where('data.status', $active)
                    ->limit($length)
                    ->skip($page)
                    ->get();
            } else if ($filter != '*' && $length == '*') {
                $data = DB::table('data')
                    ->select($filter)
                    ->where('data.status', $active)
                    ->pluck('data.name')
                    ->toArray();
            } else if ($filter == '*' && $length == '*') {
                $data = DB::table('data')
                    ->select($filter)
                    ->where('data.status', $active)
                    ->get();
            }

            return $this->sendResponse('Proccess completed succssfully', $data);
        } catch (Exception $th) {
            return $this->errors("ApiProductController@listData", $th->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
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

            if ($request->quantity == 0 && $request->quantityparts == 0)
                return $this->sendErrorResponse('Validation error', ['amount' => 'You should enter amounts']);

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
                $has_parts = isset($request->hasparts) ? 1 : 0;
                $num_of_parts = $request->numofparts != null ? $request->numofparts : 0;
                $minimum_amount = $request->minimum_amount == null ? 0 : $request->minimum_amount;
                $maximum_amount = $request->maximum_amount == null ? 0 : $request->maximum_amount;

                DB::beginTransaction();

                $dataId = DB::table('data')->insertGetId([
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
                    'created_by' => Auth::guard('api')->user()->id,
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                    'status' => '2' // inactive
                ]);

                DB::table('user_data')->insert([
                    'user_id' => $user->id,
                    'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id,
                    'data_id' => $dataId
                ]);

                if (isset($request->eff_materials)) {
                    $materials =  json_decode($request->eff_materials);
                    if (is_array($materials)) {
                        foreach ($materials as $key => $material) {
                            DB::table('data_effmaterials')->insert([
                                'data_id' => $dataId,
                                'effict_matterials_id' => $material->mat,
                                'dose' => $material->dose
                            ]);
                        }
                    }
                }

                if ($request->quantity != 0 || $request->quantityparts != 0) {
                    $expiry_date = null;
                    $duration_type = $request->duration_type;
                    $duration_value = $request->duration_value;

                    if ($duration_type == "Date") {
                        $expiry_date = $request->duration_value;
                    } else if ($duration_type == "Months") {
                        $expiry_date = Carbon::parse($request->start_date)->addMonths($duration_value);
                    }

                    DB::table('amounts')->insert([
                        'data_id' => $dataId,
                        'amount' => $request->quantity,
                        'amount_part' => $request->quantityparts,
                        'price' => $request->price,
                        'price_part' => $request->partprice,
                        'expiry_type' => $request->expiry_type,
                        'start_date' => $request->start_date,
                        'expiry_date' => $expiry_date,
                        'user_id' => $user->id,
                        'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString()
                    ]);
                }

                DB::commit();
            }
            //////////////////////////////////////////////////////  Market  //////////////////////////////////////////////////////
            elseif ($user->merchant_type == 2) {
                $has_parts = isset($request->hasparts) ? 1 : 0;
                $num_of_parts = $request->numofparts != null ? $request->numofparts : 0;
                $minimum_amount = $request->minimum_amount == null ? 0 : $request->minimum_amount;
                $maximum_amount = $request->maximum_amount == null ? 0 : $request->maximum_amount;

                DB::beginTransaction();

                $dataId = DB::table('data')->insertGetId([
                    'code' => $request->code,
                    'name' => $request->name,
                    'shape_id' => $request->shape_id == null ? 0 : $request->shape_id,
                    'comp_id' => $request->comp_id == null ? 0 : $request->comp_id,
                    'has_parts' => $has_parts,
                    'num_of_parts' => $num_of_parts,
                    'description' => $request->description,
                    'minimum_amount' => $minimum_amount,
                    'maximum_amount' => $maximum_amount,
                    'created_by' => Auth::guard('api')->user()->id,
                    'status' => '1'  // active
                ]);

                DB::table('user_data')->insert([
                    'user_id' => $user->id,
                    'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id,
                    'data_id' => $dataId
                ]);

                if ($request->quantity != 0 || $request->quantityparts != 0) {
                    $expiry_date = null;
                    $duration_type = $request->duration_type;
                    $duration_value = $request->duration_value;

                    if ($duration_type == "Date") {
                        $expiry_date = $request->duration_value;
                    } else if ($duration_type == "Months") {
                        $expiry_date = Carbon::parse($request->start_date)->addMonths($duration_value);
                    }

                    DB::table('amounts')->insert([
                        'data_id' => $dataId,
                        'amount' => $request->quantity,
                        'amount_part' => $request->quantityparts,
                        'price' => $request->price,
                        'price_part' => $request->partprice,
                        'expiry_type' => $request->expiry_type,
                        'start_date' => $request->start_date,
                        'expiry_date' => $expiry_date,
                        'user_id' => $user->id,
                        'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString()
                    ]);
                }

                DB::commit();
            }

            return response()->json(['success' => true, 'message' => 'Product addedd successfully']);
        } catch (Exception $th) {
            return $this->errors("HomeController@store", $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // check code or name existance
            $product = Data::where('id', $id)->first();
            if (!$product) {
                return $this->sendErrorResponse('Product not found!');
            }

            if ($product->status == '2')
                return $this->sendErrorResponse("This product should be activated by system admin!");

            if ($request->code != null) {
                $validator = Validator::make($request->all(), [
                    'code' => 'unique:data,code,' . $id,
                    'name' => 'required|unique:data,name,' . $id
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:data,name,' . $id
                ]);
            }

            if ($validator->fails()) {
                return $this->sendErrorResponse('Validation errors', $validator->getMessageBag());
            }

            if (isset($request->hasparts) && $request->numofparts == 0) {
                return $this->sendErrorResponse('Validation error', ['amount' => 'You should enter the number of parts for this item']);
            }

            $user = Auth::guard('api')->user();
            //////////////////////////////////////////////////////  Pharmacist  //////////////////////////////////////////////////////
            if ($user->merchant_type == 1) {
                $has_parts = isset($request->hasparts) ? 1 : 0;
                $num_of_parts = $request->numofparts != null ? $request->numofparts : 0;
                $minimum_amount = $request->minimum_amount == null ? 0 : $request->minimum_amount;
                $maximum_amount = $request->maximum_amount == null ? 0 : $request->maximum_amount;

                $filter_data = [];
                if ($request->code)
                    $filter_data['code'] = $request->code;
                if ($request->name)
                    $filter_data['name'] = $request->name;
                if ($request->shape_id)
                    $filter_data['shape_id'] = $request->shape_id;
                if ($request->comp_id)
                    $filter_data['comp_id'] = $request->comp_id;
                $filter_data['has_parts'] = $has_parts;
                if ($request->description)
                    $filter_data['description'] = $request->description;
                $filter_data['minimum_amount'] = $request->minimum_amount;
                $filter_data['maximum_amount'] = $request->maximum_amount;
                if ($request->dose)
                    $filter_data['dose'] = $request->dose;
                if ($request->tab_count)
                    $filter_data['tab_count'] = $request->tab_count;
                if ($request->treatements)
                    $filter_data['treatements'] = $request->treatements;
                if ($request->special_alarms)
                    $filter_data['special_alarms'] = $request->special_alarms;
                if ($request->interference)
                    $filter_data['interference'] = $request->interference;
                if ($request->side_effects)
                    $filter_data['side_effects'] = $request->side_effects;
                if ($request->treatement_group)
                    $filter_data['treatement_group'] = $request->treatement_group;
                $filter_data['created_by'] = Auth::guard('api')->user()->id;
                $filter_data['updated_at'] = now()->toDateTimeString();

                DB::beginTransaction();
                DB::table('data')->Where('id', $product->id)->update($filter_data);

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

                DB::commit();
                $product = Data::where('id', $id)->first();
            }
            //////////////////////////////////////////////////////  Market  //////////////////////////////////////////////////////
            elseif ($user->merchant_type == 2) {
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
                $product->save();
            }

            return $this->sendResponse('Product updated successfully', new ProductResource($product));
        } catch (Exception $th) {
            return $this->errors("HomeController@update", $th->getMessage());
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

    public function deleteProduct(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), ['data_id' => 'required|exists:data,id']);
            if ($validator->fails())
                return $this->sendErrorResponse("Validation errors", $validator->getMessageBag());

            DB::beginTransaction();
            DB::table('data')->where('id', $request->data_id)->delete();
            DB::table('amounts')->where('data_id', $request->data_id)->delete();
            DB::table('data_effmaterials')->where('data_id', $request->data_id)->delete();
            DB::table('invoice_items')->where('data_id', $request->data_id)->delete();
            DB::table('product_returns')->where('data_id', $request->data_id)->delete();
            DB::table('user_data')->where('data_id', $request->data_id)->delete();

            DB::commit();
            return $this->sendResponse("Proccess completed successfully");
        } catch (Exception $th) {
            return $this->errors("ApiProductController@deleteProduct", $th->getMessage());
        }
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

        $general_amounts = DB::table('amounts')->leftJoin('data', 'amounts.data_id', '=', 'data.id')->where('merchant_id', $merchant_id)->where('data_id', $dataId)->get();
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
        $max_price = DB::select("SELECT MAX(price) as price from (select MAX(created_at)as maxdatevalue, price from (select data_id, amounts.merchant_id, price, amounts.created_at from amounts left join users on users.id = amounts.merchant_id where data_id = $dataId and amounts.merchant_id != $merchant_id and users.merchant_type=" . Auth::guard('api')->user()->role . " group by data_id, amounts.merchant_id,price ORDER BY amounts.created_at DESC) as t1 GROUP BY data_id, merchant_id) as t2;");
        $max_price = $max_price != null ? $max_price[0]->price : 0;

        // get max part price for product
        $max_part_price = DB::select("SELECT MAX(price_part) as price_part from (select MAX(created_at)as maxdatevalue, price_part from (select data_id, amounts.merchant_id, price_part, amounts.created_at from amounts left join users on users.id = amounts.merchant_id where data_id = $dataId and amounts.merchant_id != $merchant_id and users.merchant_type=" . Auth::guard('api')->user()->role . " group by data_id, amounts.merchant_id,price_part ORDER BY amounts.created_at DESC) as t1 GROUP BY data_id, merchant_id) as t2;");
        $max_part_price = $max_part_price != null ? $max_part_price[0]->price_part : 0;
        if ($max_part_price != null) {
            return ['price' => $max_price, 'part_price' => $max_part_price];
        } else {
            return ['price' => 0, 'part_price' => 0];
        }
    }
}
