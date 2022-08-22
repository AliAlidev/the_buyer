<?php

namespace App\Http\Controllers;

use App\Imports\CompaniesImport;
use App\Imports\DataImport;
use App\Imports\ShapesImport;
use App\Imports\TreatementGroupImport;
use App\Models\Amount;
use App\Models\Data;
use App\Models\Home;
use App\Models\ItemDate;
use App\Models\Price;
use App\Models\User;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DataTables;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use function PHPUnit\Framework\returnSelf;

class HomeController extends Controller
{

    public function importDataWithShapesAndCompanies()
    {
        $this->importShapes();
        $this->importCompanies();
        $this->importData();
        $this->importTreatementGroup();
    }

    public function importCompanies()
    {
        try {
            (new CompaniesImport)->import('2.csv');
        } catch (\Exception $th) {
            dd($th->getMessage());
        }
    }

    public function importShapes()
    {
        try {
            (new ShapesImport)->import('2.csv');
        } catch (\Exception $th) {
            dd($th->getMessage());
        }
    }

    public function importData()
    {
        try {
            (new DataImport)->import('2.csv');
        } catch (\Exception $th) {
            dd($th->getMessage());
        }
    }

    public function importTreatementGroup()
    {
        try {
            (new TreatementGroupImport)->import('3.csv');
        } catch (\Exception $th) {
            dd($th->getMessage());
        }
    }

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

        if ($request->quantity != 0 && $request->price == 0)
            throw ValidationException::withMessages(['amount' => 'You should enter price']);

        if ($request->quantity == 0 && $request->price != 0)
            throw ValidationException::withMessages(['amount' => 'You should enter amount']);

        if ($request->quantityparts != 0 && $request->partprice == 0)
            throw ValidationException::withMessages(['amount' => 'You should enter part price']);

        if ($request->quantityparts == 0 && $request->partprice != 0)
            throw ValidationException::withMessages(['amount' => 'You should enter part part amount']);

        if ($request->start_date != null && $request->expiry_date != null) {
            if (Carbon::parse($request->start_date)->greaterThan(Carbon::parse($request->expiry_date)))
                throw ValidationException::withMessages(['price' => 'Expiry date should be greater than start date']);
        }

        if (isset($request->hasparts) && $request->numofparts == 0) {
            throw ValidationException::withMessages(['amount' => 'You should enter the number of parts for this item']);
        }

        try {
            $user = Auth::user();
            if ($request->code != null) {
                $has_parts = isset($request->hasparts) ? 1 : 0;
                $num_of_parts = $request->numofparts != null ? $request->numofparts : 0;
                $data = Data::Create([
                    'code' => $request->code,
                    'name' => $request->name,
                    'has_parts' => $has_parts,
                    'num_of_parts' => $num_of_parts,
                    'description' => $request->description
                ]);
            } else {
                $has_parts = isset($request->hasparts) ? 1 : 0;
                $num_of_parts = $request->numofparts != null ? $request->numofparts : 0;
                $data = Data::Create([
                    'name' => $request->name,
                    'has_parts' => $has_parts,
                    'num_of_parts' => $num_of_parts,
                    'description' => $request->description
                ]);
            }

            UserData::create([
                'user_id' => $user->id,
                'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id,
                'data_id' => $data->id
            ]);

            $amount = Amount::create([
                'data_id' => $data->id,
                'amount' => $request->quantity,
                'amount_part' => $request->quantityparts,
                'price' => $request->price,
                'price_part' => $request->partprice,
                'start_date' => $request->start_date,
                'expiry_date' => $request->expiry_date,
                'user_id' => $user->id,
                'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id
            ]);

            if ($data->wasRecentlyCreated) {
                return response()->json(['success' => true, 'message' => 'Data addedd successfully']);
            }
        } catch (Exception $th) {
            dd($th->getMessage());
            return $this->errors("HomeController@createitem", $th->getMessage());
        }
    }

    public function createitemindex()
    {
        return view('inventory.create_element');
    }

    public function hasGreaterPartPriceFromAnotherUser($dataId, $merchantId)
    {
        // get max price for product
        $max_price = DB::select("SELECT MAX(price_part) as price_part from (select MAX(created_at)as maxdatevalue, price_part from (select data_id, merchant_id, price_part, created_at from amounts where data_id = $dataId and merchant_id != $merchantId group by data_id, merchant_id,price_part ORDER BY created_at DESC) as t1 GROUP BY data_id, merchant_id) as t2;");
        $max_price = $max_price != null ? $max_price[0]->price_part : '';

        // get price for prouct for specifc user
        $user_price = Amount::where('data_id', $dataId)->orderBy('created_at', 'desc')->where('merchant_id', $merchantId)->first();

        if ($max_price != null && $user_price != null) {
            if ($user_price->price_part < $max_price) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function hasGreaterPriceFromAnotherUser($dataId, $merchantId)
    {
        // get max price for product
        $max_price = DB::select("SELECT MAX(price) as price from (select MAX(created_at)as maxdatevalue, price from (select data_id, merchant_id, price, created_at from amounts where data_id = $dataId and merchant_id != $merchantId group by data_id, merchant_id,price ORDER BY created_at DESC) as t1 GROUP BY data_id, merchant_id) as t2;");
        $max_price = $max_price != null ? $max_price[0]->price : '';

        // get price for prouct for specifc user
        $user_price = Amount::where('data_id', $dataId)->where('merchant_id', $merchantId)->orderBy('created_at', 'desc')->first();

        if ($max_price != null && $user_price != null) {
            if ($user_price->price < $max_price) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getMaxPartPriceForElement($dataId)
    {
        // get max price for product
        $max_price = Amount::where('data_id', $dataId)->orderBy('created_at', 'desc')->first();

        if ($max_price != null) {
            return $max_price->price_part;
        } else {
            return 0;
        }
    }

    public function getMaxPriceForElement($dataId)
    {
        // get max price for product
        $max_price = DB::select("SELECT MAX(price) as price from (select MAX(created_at)as maxdatevalue, price from (select data_id, merchant_id, price, created_at from amounts where data_id = 1 and merchant_id != 1 group by data_id, merchant_id,price ORDER BY created_at DESC) as t1 GROUP BY data_id, merchant_id) as t2;");
        $max_price = $max_price != null ? $max_price[0]->price : 0;
        if ($max_price != null) {
            return $max_price;
        } else {
            return 0;
        }
    }

    public function getCurrentPriceForElement($dataId, $userId)
    {
        // get max price for product
        $max_price = Amount::where('data_id', $dataId)->where('merchant_id', $userId)->orderBy('created_at', 'desc')->first();

        if ($max_price != null) {
            return $max_price->price;
        } else {
            return 0;
        }
    }

    public function getCurrentPartPriceForElement($dataId, $userId)
    {
        // get max price for product
        $max_price = Amount::where('data_id', $dataId)->where('merchant_id', $userId)->orderBy('created_at', 'desc')->first();

        if ($max_price != null) {
            return $max_price->price_part;
        } else {
            return 0;
        }
    }

    public function findByItemName(Request $request)
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

    public function fastinventorylist(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Data::get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('amount', function ($row) {
                        return '<input type="number" class="form-control  w-100" value=0>';
                    })
                    ->addColumn('partamount', function ($row) {
                        return '<input type="number" class="form-control  w-100" value=0>';
                    })
                    ->addColumn('price', function ($row) {
                        return '<input type="number" class="form-control  w-100" value=0>';
                    })
                    ->addColumn('part_price', function ($row) {
                        return '<input type="number" class="form-control  w-100" value=0>';
                    })
                    ->addColumn('action', function ($row) {
                        $btn = '<a href="' . route('store-fast-inventory-list') . '" class="btn btn-info btn-sm mt-2 btn_add">Add</a> &nbsp';

                        return $btn;
                    })
                    ->rawColumns(['action', 'price', 'part_price', 'amount', 'partamount'])
                    ->make(true);
            }
            return view('inventory.fast_inventory_list');
        } catch (Exception $th) {
            return $this->errors("HomeController@listitems", $th->getMessage());
        }
    }

    public function storefastinventorylist(Request $request)
    {
        $userId = Auth::user()->id;
        $merchantId = Auth::user()->role == 3 ? Auth::user()->merchant_id : Auth::user()->id;
        if (($request->quantity == 0 && $request->price != 0) || ($request->quantity != 0 && $request->price == 0)) {
            return response()->json(['success' => false, 'message' => 'Yous should select quantity and prices']);
        }

        if (($request->quantityP == 0 && $request->priceP != 0) || ($request->quantityP != 0 && $request->priceP == 0)) {
            return response()->json(['success' => false, 'message' => 'Yous should select parts quantity and prices']);
        }

        if ($request->quantity == 0 && $request->quantityP == 0 && $request->price == 0 && $request->priceP == 0) {
            return response()->json(['success' => false, 'message' => 'Yous should select quantity and prices']);
        }
        $data = Data::where('name', $request->name)->First();
        if ($data) {
            UserData::create([
                'user_id' => $userId,
                'merchant_id' => $merchantId,
                'data_id' => $data->id,
            ]);
            $amount = Amount::create([
                'data_id' => $data->id,
                'amount' => $request->quantity != null ? $request->quantity : 0,
                'amount_part' => $request->quantityP != null ? $request->quantityP : 0,
                'price' => $request->price != null ? $request->price : 0,
                'price_part' => $request->priceP != null ? $request->priceP : 0,
                'user_id' => $userId,
                'merchant_id' => $merchantId,
            ]);
            return response()->json(['success' => true, 'message' => 'Data addedd successfully']);
        }
    }

    public function listitems(Request $request)
    {
        try {
            if ($request->ajax()) {
                $merchantId = Auth::user()->role == 3 ? Auth::user()->merchant_id : Auth::user()->id;
                if(Auth::user()->role == 3){
                    $items = User::where('merchant_id', $merchantId)->first()->data()->select('data.id', 'merchant_id', 'data_id', 'code', 'name', 'description')->groupBy('data.id', 'merchant_id', 'data_id', 'code', 'name', 'description')->get(); 
                }
                else{
                    $items = User::where('id', $merchantId)->first()->data()->select('data.id', 'merchant_id', 'data_id', 'code', 'name', 'description')->groupBy('data.id', 'merchant_id', 'data_id', 'code', 'name', 'description')->get();
                }
                $final = [];
                foreach ($items as $key => $item) {
                    $temp_amount = $item->amounts()->sum('amount');
                    $temp_amount_parts = $item->amounts()->sum('amount_part');
                    $temp_price = $item->amountsForUser($merchantId)->orderBy('created_at', 'desc')->first();
                    $temp_price_part = $item->amountsForUser($merchantId)->orderBy('created_at', 'desc')->first();
                    $data = $item->toArray();
                    $data['quantity'] = $temp_amount != null ? $temp_amount : 0;
                    $data['quantity_parts'] = $temp_amount_parts != null ? $temp_amount_parts : 0;
                    $data['price'] = $temp_price != null ? $temp_price->price : 0;
                    $data['part_price'] = $temp_price_part != null ? $temp_price_part->price_part : 0;
                    $data['has_greater_price'] = $this->hasGreaterPriceFromAnotherUser($item->id, $merchantId);
                    $data['has_greater_part_price'] = $this->hasGreaterPartPriceFromAnotherUser($item->id, $merchantId);
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
                    ->editcolumn('part_price', function ($row) {
                        if ($row['has_greater_part_price']) {
                            return '<div style="color:red">' . $row['part_price'] . '<div>';
                        } else {
                            return '<div style="color:black">' . $row['part_price'] . '<div>';
                        }
                    })
                    ->addColumn('action', function ($row) use ($merchantId) {
                        $btn = '<a href="' . route('view-item-index', $row['id']) . '" class="view btn btn-info btn-sm mt-2">View</a> &nbsp';
                        $btn .= ' <a href="' . route('edit-item-index', $row['id']) . '" class="edit btn btn-primary btn-sm mt-2">Edit</a> &nbsp';
                        $btn .= ' <a id=' . $row['id'] . ' class="delete btn btn-danger btn-sm mt-2">Delete</a>';
                        if ($row['has_greater_price']) {
                            $btn .= ' <a id=' . $row['id'] . '_' . $merchantId . ' class="show_max_price btn btn-info btn-sm mt-2">Max Price</a>';
                        }
                        if ($row['has_greater_part_price']) {
                            $btn .= ' <a id=' . $row['id'] . '_' . $merchantId . ' class="show_max_part_price btn btn-info btn-sm mt-2">Max Part Price</a>';
                        }
                        $btn .= '&nbsp&nbsp <a href="' . route('list-inventory-item-amounts', ['dataId' => $row['id'], 'merchId' => $merchantId]) . '" class="listinventoryitemamounts btn btn-primary btn-sm mt-2">List Amounts</a> &nbsp';
                        $btn .= '&nbsp&nbsp <a href="' . route('create-inventory-item-amount-index', $row['id']) . '" class="createinventoryitemamount btn btn-primary btn-sm mt-2">Add Amounts</a> &nbsp';

                        return $btn;
                    })
                    ->rawColumns(['action', 'price', 'part_price'])
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

    public function createinventoryitemamountindex($itemId)
    {
        $data = Data::find($itemId);
        return view('inventory.create_inventory_item_amount', ['element' => $data]);
    }

    public function createinventoryitemamount(Request $request)
    {
        if ($request->quantity == 0 && $request->quantityparts == 0)
            throw ValidationException::withMessages(['amount' => 'You should enter amount']);

        if ($request->quantity != 0 && $request->price == 0)
            throw ValidationException::withMessages(['amount' => 'You should enter price']);

        if ($request->quantity == 0 && $request->price != 0)
            throw ValidationException::withMessages(['amount' => 'You should enter amount']);

        if ($request->quantityparts != 0 && $request->partprice == 0)
            throw ValidationException::withMessages(['amount' => 'You should enter part price']);

        if ($request->quantityparts == 0 && $request->partprice != 0)
            throw ValidationException::withMessages(['amount' => 'You should enter part part amount']);

        if ($request->price == 0 && $request->partprice == 0)
            throw ValidationException::withMessages(['price' => 'You should enter price']);

        if ($request->start_date != null && $request->expiry_date != null) {
            if (Carbon::parse($request->start_date)->greaterThan(Carbon::parse($request->expiry_date)))
                throw ValidationException::withMessages(['price' => 'Expiry date should be greater than start date']);
        }

        try {
            $user = Auth::user();
            $data = Data::find($request->dataId);

            UserData::create([
                'user_id' => $user->id,
                'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id,
                'data_id' => $data->id
            ]);

            $amount = Amount::create([
                'data_id' => $data->id,
                'amount' => $request->quantity,
                'amount_part' => $request->quantityparts,
                'price' => $request->price,
                'price_part' => $request->partprice,
                'start_date' => $request->start_date,
                'expiry_date' => $request->expiry_date,
                'user_id' => $user->id,
                'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id
            ]);

            if ($amount) {
                return response()->json(['success' => true, 'message' => 'Amount addedd successfully']);
            }
        } catch (Exception $th) {
            return $this->errors("HomeController@createinventoryitemamount", $th->getMessage());
        }
    }

    public function viewitemindex($itemId)
    {
        $data = Data::find($itemId);
        return view('inventory.view_element', ['element' => $data]);
    }

    public function deleteitemamount(Request $request)
    {
        try {
            $data = Amount::find($request->id);
            if ($data) {
                $data->delete();
                return response()->json(['success' => true, 'message' => 'Amount deleted successfully']);
            }
            return redirect()->route('list-items')->withErrors('Amount not found');
        } catch (Exception $th) {
            return $this->errors("HomeController@deleteitem", $th->getMessage());
        }
    }

    public function deleteitem(Request $request)
    {
        try {
            $data = Data::find($request->id);
            if ($data) {
                $data->delete();
                Amount::where('data_id', $data->id)->delete();
                UserData::where('data_id', $data->id)->delete();
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
            $data->has_parts = $request->hasparts;
            $data->has_parts = $request->hasparts;
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

    public function listinventoryitemamounts(Request $request)
    {
        $data = Data::find($request->query('dataId'));
        if ($request->ajax()) {
            $amounts = $data->amountsForUser($request->query('merchantId'))->get();
            $final_data = [];
            foreach ($amounts as $key => $item) {
                $data_temp = [
                    'id' => $item->id,
                    'code' => $data->code,
                    'name' => $data->name,
                    'quantity' => $item->amount,
                    'price' => $item->price,
                    'quantity_parts' => $item->amount_part,
                    'price_part' => $item->price_part,
                    'start_date' => $item->start_date,
                    'expiry_date' => $item->expiry_date
                ];
                $final_data[] = $data_temp;
            }
            return Datatables::of($final_data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a id=' . $row['id'] . ' class="delete btn btn-danger btn-sm mt-2">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('inventory.list_inventory_item_amounts', ['data' => $data]);
    }
}
