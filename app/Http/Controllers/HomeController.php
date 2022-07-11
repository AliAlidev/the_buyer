<?php

namespace App\Http\Controllers;

use App\Models\Amount;
use App\Models\Data;
use App\Models\Home;
use App\Models\ItemDate;
use App\Models\Price;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DataTables;
use Exception;

use function PHPUnit\Framework\returnSelf;

class HomeController extends Controller
{
    public function adminIndex()
    {
        return view('admin.home');
    }

    public function createitem(Request $request)
    {
        if ($request->code != null) {
            $request->validate([
                'code' => 'unique:data,code',
                'name' => 'required|unique:data,name'
            ]);
        } else {
            $request->validate([
                'name' => 'required|unique:data,name'
            ]);
        }
        try {
            $userId = 1;
            $user = User::find($userId);
            if ($request->code != null) {
                $data = Data::Create([
                    'code' => $request->code,
                    'name' => $request->name,
                    'description' => $request->description
                ]);
            } else {
                $data = Data::Create([
                    'name' => $request->name,
                    'description' => $request->description
                ]);
            }

            UserData::create([
                'user_id' => $user->id,
                'merchant_id' => $user->merchant_id,
                'data_id' => $data->id,
            ]);

            $amount = Amount::create([
                'data_id' => $data->id,
                'amount' => $request->quantity,
                'amount_part' => $request->quantityparts,
                'user_id' => $user->id,
                'merchant_id' => $user->merchant_id,
            ]);

            $price = Price::create([
                'data_id' => $data->id,
                'price' => $request->price,
                'price_part' => $request->partprice,
                'user_id' => $user->id,
                'merchant_id' => $user->merchant_id,
            ]);

            $itemDate = ItemDate::create([
                'data_id' => $data->id,
                'start_date' => $request->start_date,
                'expiry_date' => $request->expiry_date,
                'user_id' => $user->id,
                'merchant_id' => $user->merchant_id,
            ]);

            if ($data->wasRecentlyCreated) {
                return response()->json(['success' => true, 'message' => 'Data addedd successfully']);
            }
        } catch (Exception $th) {
            return $this->errors("HomeController@createitem", $th->getMessage());
        }
    }

    public function createitemindex()
    {
        return view('inventory.create_element');
    }

    public function hasGreaterPriceFromAnotherUser($dataId, $merchantId)
    {
        // get max price for product
        $max_price = Price::where('data_id', $dataId)->orderBy('price', 'desc')->first();

        // get price for prouct for specifc user
        $user_price = Price::where('data_id', $dataId)->where('merchant_id', $merchantId)->first();

        if ($max_price != null && $user_price != null) {
            if ($user_price->price < $max_price->price) {
                return true;
            } else {
                return false;
            }
        } else {
        }
    }

    public function getMaxPriceForElement($dataId)
    {
        // get max price for product
        $max_price = Price::where('data_id', $dataId)->orderBy('price', 'desc')->first();

        if ($max_price != null) {
            return $max_price->price;
        } else {
            return 0;
        }
    }

    public function getCurrentPriceForElement($dataId, $userId)
    {
        // get max price for product
        $max_price = Price::where('data_id', $dataId)->where('merchant_id', $userId)->orderBy('price', 'desc')->first();

        if ($max_price != null) {
            return $max_price->price;
        } else {
            return 0;
        }
    }

    public function findBySerialName(Request $request)
    {
        $data = Data::where('name', $request->name)->first();
        if ($data) {
            return response()->json(['success' => true, 'data' => $data], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found'], 400);
        }
    }

    public function findBySerialCode(Request $request)
    {
        $data = Data::where('code', $request->code)->first();
        if ($data) {
            return response()->json(['success' => true, 'data' => $data], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found'], 400);
        }
    }

    public function listitems(Request $request)
    {
        try {
            if ($request->ajax()) {
                $merchantId = 1;
                $items = User::where('merchant_id', $merchantId)->first()->data;
                $final = [];
                foreach ($items as $key => $item) {
                    $temp_amount = $item->amounts()->sum('amount');
                    $temp_amount_parts = $item->amounts()->sum('amount_part');
                    $temp_price = $item->pricesForUser($merchantId)->orderBy('price', 'desc')->first();
                    $temp_date = $item->itemdates()->first();
                    $data = $item->toArray();
                    $data['quantity'] = $temp_amount != null ? $temp_amount : 0;
                    $data['quantity_parts'] = $temp_amount_parts != null ? $temp_amount_parts : 0;
                    $data['price'] = $temp_price != null ? $temp_price->price : 0;
                    $data['start_date'] = $temp_date != null ? $temp_date->start_date : '';
                    $data['expiry_date'] = $temp_date != null ? $temp_date->expiry_date : '';
                    $data['has_greater_price'] = $this->hasGreaterPriceFromAnotherUser($item->id, $merchantId);
                    $final[] = $data;
                }

                return Datatables::of($final)
                    ->addIndexColumn()
                    ->editcolumn('price', function ($row) {
                        if ($row['has_greater_price']) {
                            return '<div style="color:red">' . $row['price'] . '<div>';
                        } else {
                            return '<div style="color:black">' . $row['price'] . '<div>';
                        }
                    })
                    ->addColumn('action', function ($row) use ($merchantId) {
                        $btn = '<a href="' . route('view-item-index', $row['id']) . '" class="view btn btn-info btn-sm">View</a> &nbsp';
                        $btn .= '<a href="' . route('edit-item-index', $row['id']) . '" class="edit btn btn-primary btn-sm">Edit</a> &nbsp';
                        $btn .= '<a id=' . $row['id'] . ' class="delete btn btn-danger btn-sm mt-2">Delete</a>';
                        if ($row['has_greater_price']) {
                            $btn .= '<a id=' . $row['id'] . '_' . $merchantId . ' class="show_max_price btn btn-info btn-sm mt-2">Max Price</a>';
                        }

                        return $btn;
                    })
                    ->rawColumns(['action', 'price'])
                    ->make(true);
            }
            return view('inventory.list_inventory_items');
        } catch (Exception $th) {
            dd($th->getMessage());
            return $this->errors("HomeController@listitems", $th->getMessage());
        }
    }

    public function edititemindex($itemId)
    {
        $data = Data::find($itemId);
        return view('inventory.edit_element', ['element' => $data]);
    }

    public function viewitemindex($itemId)
    {
        $data = Data::find($itemId);
        return view('inventory.view_element', ['element' => $data]);
    }

    public function deleteitem(Request $request)
    {
        try {
            $data = Data::find($request->id);
            if ($data) {
                $data->delete();
                return response()->json(['success' => true, 'message' => 'Element deleted successfully']);
            }
            return redirect()->route('list-items')->withErrors('Element not found');
        } catch (Exception $th) {
            return $this->errors("HomeController@deleteitem", $th->getMessage());
        }
    }

    public function edititem($itemId, Request $request)
    {
        $data = Data::find($itemId);
        if ($request->code != null) {
            $request->validate([
                'code' => 'unique:data,code,' . $data->code . ',code',
                'name' => 'required|unique:data,name,' . $data->name . ',name'
            ], [
                'code.unique' => "Code :input already used!",
                'name.unique' => "Name :input already used!"
            ]);
        } else {
            $request->validate([
                'name' => 'required|unique:data,name,' . $data->name . ',name'
            ], [
                'name.unique' => "Name :input already used!"
            ]);
        }

        try {
            $data->code = $request->code;
            $data->name = $request->name;
            $data->description = $request->description;
            $data->save();
            return redirect()->route('list-items')->with('success', 'Element updated successfully');
        } catch (Exception $th) {
            return $this->errors("HomeController@edititem", $th->getMessage());
        }
    }

    public function getItemsName(Request $request)
    {
        $data = Data::where('name', 'like', $request->get('searchText') . '%')->skip(0)->take(25)->get();
        return $data;
    }
}
