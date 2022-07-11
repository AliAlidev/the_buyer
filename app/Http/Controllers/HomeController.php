<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Home;
use App\Models\Price;
use App\Models\User;
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
        $request->validate([
            'code' => 'required',
            'quantity' => 'required',
            'price' => 'required'
        ]);
        try {
            $data = Data::firstOrCreate(['code' => $request->code], [
                'name' => $request->name,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'expiry_date' => $request->expiry_date,
                'description' => $request->description
            ]);
            if ($data->wasRecentlyCreated) {
                return back()->with('success', 'Data addedd successfully');
            } else {
                return back()->withErrors('This code already found!')->withInput();
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
                $items = User::find(1)->data;
                $final = [];
                foreach ($items as $key => $item) {
                    $temp_amount = $item->amounts()->first();
                    $temp_price = $item->prices()->orderBy('price', 'desc')->first();
                    $temp_date = $item->itemdates()->first();
                    $data = $item->toArray();
                    $data['quantity'] = $temp_amount != null ? $temp_amount->amount : 0;
                    $data['price'] = $temp_price != null ? $temp_price->price : 0;
                    $data['expiry_date'] = $temp_date != null ? $temp_date->expiry_date : '';
                    $data['has_greater_price'] = $this->hasGreaterPriceFromAnotherUser($item->id, User::find(1)->merchant_id);
                    $final[] = $data;
                }

                return Datatables::of($final)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $btn = '<a href="' . route('view-item-index', $row['id']) . '" class="view btn btn-info btn-sm">View</a> &nbsp';
                        $btn .= '<a href="' . route('edit-item-index', $row['id']) . '" class="edit btn btn-primary btn-sm">Edit</a> &nbsp';
                        $btn .= '<a id=' . $row['id'] . ' class="delete btn btn-danger btn-sm mt-2">Delete</a>';

                        return $btn;
                    })
                    ->rawColumns(['action'])
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
        $request->validate([
            'code' => 'unique:data,code,' . $itemId
        ]);
        try {
            $data = Data::find($itemId);
            $data->code = $request->code;
            $data->name = $request->name;
            $data->quantity = $request->quantity;
            $data->price = $request->price;
            $data->expiry_date = $request->expiry_date;
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
