<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShapeResource;
use App\Models\Amount;
use App\Models\Company;
use App\Models\Data;
use App\Models\EffMaterial;
use App\Models\Shape;
use App\Models\TreatementGroup;
use App\Models\UserData;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiProductController extends Controller
{
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
                'duration_type' => $request->duration_type,
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
        return $this->sendResponse('Proccess completed successfully', $effMaterials);
    }

    public function getTreatementGroup()
    {
        $treatementGroups = TreatementGroup::where('merchant_type', Auth::guard('api')->user()->merchant_type)->get();
        return $this->sendResponse('Proccess completed successfully', $treatementGroups);
    }
}
