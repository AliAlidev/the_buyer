<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Amount;
use App\Models\Data;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Http\Request;
use Exception;
use DataTables;
use Illuminate\Support\Facades\Auth;

use function GuzzleHttp\Promise\all;

class InventoryController extends Controller
{

    public function fast_initilize_store(Request $request)
    {
        try {
            if ($request->ajax()) {
                $length = $request->length_select != null ? $request->length_select : 25;
                $data = [];
                $merchant_id = null;
                if (Auth::user()->isMerchant()) {
                    $merchant_id = Auth::user()->id;
                    if ($request->search_input) {
                        $data = User::where('id', $merchant_id)->first()->with(['data', 'data.amountsForUser' => function ($query) use ($merchant_id) {
                            $query->where('user_id', $merchant_id);
                        }])->where('id', $merchant_id)->first()->data()->groupBy('data.id')->where('name', 'like', '%' . $request->search_input . '%')->orWhere('code', 'like', '%' . $request->search_input . '%')->paginate($length);
                    } else {
                        $data = User::where('id', $merchant_id)->first()->with(['data', 'data.amountsForUser' => function ($query) use ($merchant_id) {
                            $query->where('user_id', $merchant_id);
                        }])->where('id', $merchant_id)->first()->data()->groupBy('data.id')->paginate($length);
                    }
                    return view('inventory.fast_inventory_table', ['data' => $data])->render();
                } else if (Auth::user()->isEmployee()) {
                    $merchant_id = Auth::user()->merchant_id;
                    $data = User::where('id', Auth::user()->merchant_id)->first()->data;
                } else if (Auth::user()->isAdmin()) {
                    // check if admin select merchant_id
                    if ($request->merchant_id) {
                        $merchant_id = $request->merchant_id;
                        if ($request->search_input) {
                            $data = User::where('id', $merchant_id)->first()->with(['data', 'data.amountsForUser' => function ($query) use ($merchant_id) {
                                $query->where('user_id', $merchant_id);
                            }])->where('id', $merchant_id)->first()->data()->groupBy('data.id')->where('name', 'like', '%' . $request->search_input . '%')->orWhere('code', 'like', '%' . $request->search_input . '%')->paginate($length);
                        } else {
                            $data = User::where('id', $merchant_id)->first()->with(['data', 'data.amountsForUser' => function ($query) use ($merchant_id) {
                                $query->where('user_id', $merchant_id);
                            }])->where('id', $merchant_id)->first()->data()->groupBy('data.id')->paginate($length);
                        }
                        return view('inventory.fast_inventory_table', ['data' => $data])->render();
                    }
                    // else
                    //     return $this->sendErrorResponse("validation error", [__("you_should_select_merchant")]);
                }

                if (is_array($data) && count($data) > 0) {
                }
            }

            $merchants = User::where('role', 1)->get();
            return view('inventory.fast_inventory_list', ['merchants' => $merchants]);
        } catch (Exception $th) {
            return $this->errors("HomeController@listitems", $th->getMessage());
        }
    }

    // public function fast_initilize_store(Request $request)
    // {
    //     try {
    //         if ($request->ajax()) {
    //             $data = [];
    //             $merchant_id = null;
    //             if (Auth::user()->isMerchant()) {
    //                 $merchant_id = Auth::user()->id;
    //                 $data = Auth::user()->data;
    //             } else if (Auth::user()->isEmployee()) {
    //                 $merchant_id = Auth::user()->merchant_id;
    //                 $data = User::where('id', Auth::user()->merchant_id)->first()->data;
    //             } else if (Auth::user()->isAdmin()) {
    //                 // check if admin select merchant_id
    //                 if ($request->merchant_id) {
    //                     $merchant_id = $request->merchant_id;
    //                     $data = User::where('id', $merchant_id)->first()->with(['data','data.amountsForUser' => function ($query) use ($merchant_id) {
    //                         $query->where('user_id', $merchant_id);
    //                     }])->where('id', $merchant_id);
    //                 }
    //                 // else
    //                 //     return $this->sendErrorResponse("validation error", [__("you_should_select_merchant")]);
    //             }
    //             $data = $data->first()->data;

    //             return $data;
    //             // return DataTables::of($data)
    //             //     ->addIndexColumn()
    //             //     ->addColumn('amount', function ($row) {
    //             //         $inentory_amount = $row->amountsForUser()->where('amount_type', '0')->first();
    //             //         if ($inentory_amount != null) {
    //             //             return '<input type="number" class="form-control  w-100" value=' . $inentory_amount->amount . '>';
    //             //         } else {
    //             //             return '<input type="number" class="form-control  w-100" value=0>';
    //             //         }
    //             //     })
    //             //     ->addColumn('partamount', function ($row) {
    //             //         $inentory_amount = $row->amountsForUser()->where('amount_type', '0')->first();
    //             //         if ($inentory_amount != null) {
    //             //             return '<input type="number" class="form-control  w-100" value=' . $inentory_amount->amount_part . '>';
    //             //         } else {
    //             //             return '<input type="number" class="form-control  w-100" value=0>';
    //             //         }
    //             //     })
    //             //     ->addColumn('price', function ($row) {
    //             //         $inentory_amount = $row->amountsForUser()->where('amount_type', '0')->first();
    //             //         if ($inentory_amount != null) {
    //             //             return '<input type="number" class="form-control  w-100" value=' . $inentory_amount->price . '>';
    //             //         } else {
    //             //             return '<input type="number" class="form-control  w-100" value=0>';
    //             //         }
    //             //     })
    //             //     ->addColumn('part_price', function ($row) {
    //             //         $inentory_amount = $row->amountsForUser()->where('amount_type', '0')->first();
    //             //         if ($inentory_amount != null) {
    //             //             return '<input type="number" class="form-control  w-100" value=' . $inentory_amount->price_part . '>';
    //             //         } else {
    //             //             return '<input type="number" class="form-control  w-100" value=0>';
    //             //         }
    //             //     })
    //             //     ->addColumn('action', function ($row) {
    //             //         $inentory_amount = $row->amountsForUser()->where('amount_type', '0')->first();
    //             //         if ($inentory_amount != null) {
    //             //             $btn = '<a href="' . route('edit-item-in-fast-initilize-store', $inentory_amount->id) . '" class="btn btn-info btn-sm mt-2 btn_edit">Edit</a> &nbsp';
    //             //         } else {
    //             //             $btn = '<a href="' . route('save-item-in-fast-initilize-store') . '" class="btn btn-info btn-sm mt-2 btn_add">Add</a> &nbsp';
    //             //         }
    //             //         return $btn;
    //             //     })
    //             //     ->rawColumns(['action', 'price', 'part_price', 'amount', 'partamount'])
    //             //     ->make(true);
    //         }

    //         $merchants = User::where('role', 1)->get();
    //         return view('inventory.fast_inventory_list', ['merchants' => $merchants]);
    //     } catch (Exception $th) {
    //         return $this->errors("HomeController@listitems", $th->getMessage());
    //     }
    // }

    public function save_item_in_fast_initilize_store(Request $request)
    {
        $user_id = null;
        $merchant_id = null;
        if (Auth::user()->isMerchant()) {
            $merchant_id = Auth::user()->id;
            $user_id = $merchant_id;
        } else if (Auth::user()->isEmployee()) {
            $merchant_id = Auth::user()->merchant_id;
            $user_id = Auth::user()->id;
        } else if (Auth::user()->isAdmin()) {
            // check if admin select merchant_id
            if ($request->merchant_id) {
                $merchant_id = $request->merchant_id;
                $user_id = $merchant_id;
            } else
                return $this->sendErrorResponse("validation error", [__("you_should_select_merchant")]);
        }

        $userId = Auth::user()->id;
        $merchantId = Auth::user()->role == 3 ? Auth::user()->merchant_id : Auth::user()->id;
        if (($request->quantity == 0 && $request->price != 0) || ($request->quantity != 0 && $request->price == 0)) {
            return response()->json(['success' => false, 'message' => [__('inventory/inventory.labels.yous_should_select_quantity_and_prices')]]);
        }

        if (($request->quantityP == 0 && $request->priceP != 0) || ($request->quantityP != 0 && $request->priceP == 0)) {
            return response()->json(['success' => false, 'message' => [__('inventory/inventory.labels.yous_should_select_parts_quantity_and_prices')]]);
        }

        if ($request->quantity == 0 && $request->quantityP == 0 && $request->price == 0 && $request->priceP == 0) {
            return response()->json(['success' => false, 'message' => [__('inventory/inventory.labels.yous_should_select_quantity_and_prices')]]);
        }
        $data = Data::where('name', $request->name)->First();
        if ($data) {
            $amount = Amount::create([
                'data_id' => $data->id,
                'amount' => $request->quantity != null ? $request->quantity : 0,
                'amount_part' => $request->quantityP != null ? $request->quantityP : 0,
                'price' => $request->price != null ? $request->price : 0,
                'price_part' => $request->priceP != null ? $request->priceP : 0,
                'user_id' => $user_id,
                'merchant_id' => $merchant_id,
                'amount_type' => '0'
            ]);
            return $this->sendResponse([__('inventory/inventory.labels.data_added_successfully')], ['amount_id' => $amount->id]);
            // return response()->json(['success' => true, 'message' => [__('inventory/inventory.labels.data_added_successfully')]]);
        }
    }

    public function edit_item_in_fast_initilize_store(Request $request, $id)
    {
        $user_id = null;
        $merchant_id = null;
        if (Auth::user()->isMerchant()) {
            $merchant_id = Auth::user()->id;
            $user_id = $merchant_id;
        } else if (Auth::user()->isEmployee()) {
            $merchant_id = Auth::user()->merchant_id;
            $user_id = Auth::user()->id;
        } else if (Auth::user()->isAdmin()) {
            // check if admin select merchant_id
            if ($request->merchant_id) {
                $merchant_id = $request->merchant_id;
                $user_id = $merchant_id;
            } else
                return $this->sendErrorResponse("validation error", [__("you_should_select_merchant")]);
        }

        $userId = Auth::user()->id;
        $merchantId = Auth::user()->role == 3 ? Auth::user()->merchant_id : Auth::user()->id;
        if (($request->quantity == 0 && $request->price != 0) || ($request->quantity != 0 && $request->price == 0)) {
            return response()->json(['success' => false, 'message' => [__('inventory/inventory.labels.yous_should_select_quantity_and_prices')]]);
        }

        if (($request->quantityP == 0 && $request->priceP != 0) || ($request->quantityP != 0 && $request->priceP == 0)) {
            return response()->json(['success' => false, 'message' => [__('inventory/inventory.labels.yous_should_select_parts_quantity_and_prices')]]);
        }

        if ($request->quantity == 0 && $request->quantityP == 0 && $request->price == 0 && $request->priceP == 0) {
            return response()->json(['success' => false, 'message' => [__('inventory/inventory.labels.yous_should_select_quantity_and_prices')]]);
        }
        $data = Data::where('name', $request->name)->First();
        if ($data) {
            Amount::where('id', $id)->update([
                'data_id' => $data->id,
                'amount' => $request->quantity != null ? $request->quantity : 0,
                'amount_part' => $request->quantityP != null ? $request->quantityP : 0,
                'price' => $request->price != null ? $request->price : 0,
                'price_part' => $request->priceP != null ? $request->priceP : 0,
                'user_id' => $user_id,
                'merchant_id' => $merchant_id
            ]);
            return response()->json(['success' => true, 'message' => [__('inventory/inventory.labels.data_updated_successfully')]]);
        }
    }
}
