<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Apis\ApiOrderController;
use App\Http\Controllers\Apis\ApiProductController;
use App\Http\Controllers\Controller;
use App\Models\Amount;
use App\Models\BuyInvoice;
use App\Models\Customer;
use App\Models\Data;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Random;
use DataTables;

class InvoiceController extends Controller
{
    public function list_invoices(Request $request)
    {
        if ($request->ajax()) {
            if (Auth::user()->isAdmin()) {
                $invoices = Invoice::with(['merchant', 'customer', 'user'])->orderBy('created_at', 'desc');
            } else {
                $invoices = Invoice::where('merchant_id', Auth::user()->merchant_id)->with(['merchant', 'customer', 'user'])->orderBy('created_at', 'desc');
            }
            // if ($request->merchant_type) {
            //     $users = $users->where('merchant_type', $request->merchant_type);
            // }
            // if ($request->province) {
            //     $users = $users->where('province', $request->province);
            // }
            // if ($request->city) {
            //     $users = $users->where('city', $request->city);
            // }
            // if ($request->role) {
            //     $users = $users->where('role', $request->role);
            // }
            // if ($request->language) {
            //     $users = $users->where('language', $request->language);
            // }
            // if ($request->merchant_type) {
            //     $users = $users->where('merchant_type', $request->merchant_type);
            // }
            return DataTables::of($invoices)
                ->addIndexColumn()
                ->editColumn('order_number', function ($row) {
                    return '<a target="_blank" href=' . route('view.invoice', $row->order_number) . '>' . $row->order_number . '</a>';
                })
                ->editColumn('merchant_name', function ($row) {
                    return $row->merchant != null ? $row->merchant->name : '';
                })
                ->editColumn('user_name', function ($row) {
                    return $row->user != null ? $row->user->name : '';
                })
                ->editColumn('store_name', function ($row) {
                    return $row->Store != null ? $row->Store->name : '';
                })
                ->editColumn('customer_name', function ($row) {
                    return $row->customer != null ? $row->customer->name : '';
                })
                ->addColumn('action', function ($row) {
                    if ($this->getCurrentLanguage() == "en") {
                        $btn = '<a href=' . route('invoice-details', $row->order_number) . ' class="btn btn-info btn-sm mt-2" style="margin-left:4%"><i class="far fa-eye"></i></a>';
                    } else if ($this->getCurrentLanguage() == "ar") {
                        $btn = '<a href=' . route('invoice-details', $row->order_number) . ' class="btn btn-info btn-sm mt-2" style="margin-right:4%"><i class="far fa-eye"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'order_number'])
                ->make(true);
        }
        return view('Invoice.list_invoices');
    }

    public function invoice_details(Request $request, $order_number)
    {
        if ($request->ajax()) {
            $invoice = Invoice::where('order_number', $order_number)->first();
            $invoiceItems = $invoice->invoiceItems()->with('data');
            // if ($request->merchant_type) {
            //     $users = $users->where('merchant_type', $request->merchant_type);
            // }
            // if ($request->province) {
            //     $users = $users->where('province', $request->province);
            // }
            // if ($request->city) {
            //     $users = $users->where('city', $request->city);
            // }
            // if ($request->role) {
            //     $users = $users->where('role', $request->role);
            // }
            // if ($request->language) {
            //     $users = $users->where('language', $request->language);
            // }
            // if ($request->merchant_type) {
            //     $users = $users->where('merchant_type', $request->merchant_type);
            // }
            return DataTables::of($invoiceItems)
                ->addIndexColumn()
                ->editColumn('code', function ($row) {
                    return $row->data != null ? $row->data->code : '';
                })
                ->editColumn('name', function ($row) {
                    return $row->data != null ? $row->data->name : '';
                })
                ->addColumn('action', function ($row) {
                    // if ($this->getCurrentLanguage() == "en") {
                    //     $btn = '<a href=' . route('show-user', $row->id) . ' class="btn btn-info btn-sm mt-2" style="margin-left:4%"><i class="far fa-eye"></i></a>';
                    // } else if ($this->getCurrentLanguage() == "ar") {
                    //     $btn = '<a href=' . route('show-user', $row->id) . ' class="btn btn-info btn-sm mt-2" style="margin-right:4%"><i class="far fa-eye"></i></a>';
                    // }
                    return '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Invoice.invoice_details');
    }

    public function sell_index()
    {
        return view('Invoice.create_sell_invoice');
    }

    public function sell(Request $request)
    {
        $result = app()->call('App\Http\Controllers\Apis\ApiOrderController@sell', ['source' => 'web']);
        return json_decode($result->content(), true);
    }

    public function generate_invoice_pdf($order_number)
    {
        $invoice = Invoice::where('order_number', $order_number)->first();
        $invoice_type = $invoice->invoice_type == 1 ? 'BUY' : ($invoice->invoice_type == 2 ? 'SELL' : '');
        $from = $invoice->merchant->name;
        $customer = $invoice->Store->name;
        $pdf = PDF::loadView('Invoice.invoice', ['invoice' => $invoice, 'invoice_type' => $invoice_type, 'from' => $from, 'customer' => $customer]);
        $user = Auth::guard('web')->user();
        $invoiceName = 'Invoice_' . $user->id . '.pdf';
        /** Here you can use the path you want to save */
        $pdf->save(public_path('uploads/invoices/' . $invoiceName));
        return asset('uploads/invoices/' . $invoiceName);
    }

    public function buy_index()
    {
        return view('Invoice.create_buy_invoice');
    }

    public function buy(Request $request)
    {
        $result = app()->call('App\Http\Controllers\Apis\ApiOrderController@buy', ['source' => 'web']);
        return json_decode($result->content(), true);
    }

    public function generateOrderNumber()
    {
        while (1) {
            $rand = Random::generate(6, '0-9');
            $year = now()->year;
            $month = now()->month;
            $day = now()->day;
            if (strlen($month) == 1)
                $month = '0' . $month;
            if (strlen($day) == 1)
                $day = '0' . $day;
            $final_rand = $year . $month . $day . $rand;
            $is_found = Invoice::where('order_number', $final_rand)->first();
            if (!$is_found) {
                return $final_rand;
            }
        }
    }

    public function getCustomerId($name)
    {
        if ($name == null) {
            $customer = Customer::firstOrCreate(['name' => 'general customer'], [
                'name' => 'general customer'
            ]);
        } else {
            $customer = Customer::where('name', $name)->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $name
                ]);
            }
        }
        return $customer->id;
    }

    public function findByItemCode(Request $request)
    {
        $merchantId = Auth::user()->role == 3 ? Auth::user()->merchant_id : Auth::user()->id;
        $data = Data::where('code', $request->code)->first();
        $amounts = app()->call('App\Http\Controllers\Apis\ApiProductController@getProductAmounts', ['dataId' => $data->id, 'source' => 'web']);
        $prices = app()->call('App\Http\Controllers\Apis\ApiProductController@getCurrentPriceForElement', ['dataId' => $data->id, 'source' => 'web']);
        $hasGreaterThanPrice = app()->call('App\Http\Controllers\Apis\ApiProductController@hasGreaterPriceFromAnotherUser', ['dataId' => $data->id, 'merchantId' => $merchantId]);
        $hasGreaterThanPartPrice = app()->call('App\Http\Controllers\Apis\ApiProductController@hasGreaterPartPriceFromAnotherUser', ['dataId' => $data->id, 'merchantId' => $merchantId]);

        $price = 0;
        $max_price = 0;
        $partprice = 0;
        $max_part_price = 0;

        if ($hasGreaterThanPrice) {
            $max_price = app()->call('App\Http\Controllers\Apis\ApiProductController@getMaxPriceForElement', ['dataId' => $data->id, 'source' => 'web']);
            $price = app()->call('App\Http\Controllers\Apis\ApiProductController@getCurrentPriceForElement', ['dataId' => $data->id, 'merchant_id' => $merchantId, 'source' => 'web']);
        } else {
            $price = app()->call('App\Http\Controllers\Apis\ApiProductController@getCurrentPriceForElement', ['dataId' => $data->id, 'merchant_id' => $merchantId, 'source' => 'web']);
        }
        if ($hasGreaterThanPartPrice) {
            $max_part_price = app()->call('App\Http\Controllers\Apis\ApiProductController@getMaxPartPriceForElement', ['dataId' => $data->id, 'source' => 'web']);
            $partprice = app()->call('App\Http\Controllers\Apis\ApiProductController@getCurrentPartPriceForElement', ['dataId' => $data->id, 'userId' => $merchantId, 'source' => 'web']);
        } else {
            $partprice = app()->call('App\Http\Controllers\Apis\ApiProductController@getCurrentPartPriceForElement', ['dataId' => $data->id, 'userId' => $merchantId, 'source' => 'web']);
        }

        $expiry_date = Amount::where('data_id', $data->id)->where('merchant_id', $merchantId)->where('expiry_date', '!=', '')->orderBy('expiry_date', 'desc')->first();
        $expiry_date = $expiry_date != null ? $expiry_date->expiry_date : '';
        $hasMultipleExpiryDate = app()->call('App\Http\Controllers\Apis\ApiProductController@hasMultipleExpiryDate', ['dataId' => $data->id, 'merchantId' => $merchantId, 'source' => 'web']);

        return $this->sendResponse(
            'Proccess completed successfully',
            [
                'amounts' => $amounts,
                'prices' => $prices,
                'data' => $data,
                'hasMultipleExpiryDate' => $hasMultipleExpiryDate,
                'expiry_date' => $expiry_date != '' ? Carbon::parse($expiry_date)->toDateString() : '',
                'has_greater_price' => $hasGreaterThanPrice,
                'has_greater_part_price' => $hasGreaterThanPartPrice,
                'max_price' => $max_price,
                'max_part_price' => $max_part_price
            ]
        );
    }

    public function findByItemName(Request $request)
    {
        $merchantId = Auth::user()->role == 3 ? Auth::user()->merchant_id : Auth::user()->id;
        $data = Data::where('name', $request->name)->first();
        $amounts = app()->call('App\Http\Controllers\Apis\ApiProductController@getProductAmounts', ['dataId' => $data->id, 'source' => 'web']);
        $prices = app()->call('App\Http\Controllers\Apis\ApiProductController@getCurrentPriceForElement', ['dataId' => $data->id, 'source' => 'web']);
        $hasGreaterThanPrice = app()->call('App\Http\Controllers\Apis\ApiProductController@hasGreaterPriceFromAnotherUser', ['dataId' => $data->id, 'merchantId' => $merchantId]);
        $hasGreaterThanPartPrice = app()->call('App\Http\Controllers\Apis\ApiProductController@hasGreaterPartPriceFromAnotherUser', ['dataId' => $data->id, 'merchantId' => $merchantId]);

        $price = 0;
        $max_price = 0;
        $partprice = 0;
        $max_part_price = 0;

        if ($hasGreaterThanPrice) {
            $max_price = app()->call('App\Http\Controllers\Apis\ApiProductController@getMaxPriceForElement', ['dataId' => $data->id, 'source' => 'web']);
            $price = app()->call('App\Http\Controllers\Apis\ApiProductController@getCurrentPriceForElement', ['dataId' => $data->id, 'merchant_id' => $merchantId, 'source' => 'web']);
        } else {
            $price = app()->call('App\Http\Controllers\Apis\ApiProductController@getCurrentPriceForElement', ['dataId' => $data->id, 'merchant_id' => $merchantId, 'source' => 'web']);
        }
        if ($hasGreaterThanPartPrice) {
            $max_part_price = app()->call('App\Http\Controllers\Apis\ApiProductController@getMaxPartPriceForElement', ['dataId' => $data->id, 'source' => 'web']);
            $partprice = app()->call('App\Http\Controllers\Apis\ApiProductController@getCurrentPartPriceForElement', ['dataId' => $data->id, 'userId' => $merchantId, 'source' => 'web']);
        } else {
            $partprice = app()->call('App\Http\Controllers\Apis\ApiProductController@getCurrentPartPriceForElement', ['dataId' => $data->id, 'userId' => $merchantId, 'source' => 'web']);
        }

        $expiry_date = Amount::where('data_id', $data->id)->where('merchant_id', $merchantId)->where('expiry_date', '!=', '')->orderBy('expiry_date')->first();
        $expiry_date = $expiry_date != null ? $expiry_date->expiry_date : '';

        $hasMultipleExpiryDate = app()->call('App\Http\Controllers\Apis\ApiProductController@hasMultipleExpiryDate', ['dataId' => $data->id, 'merchantId' => $merchantId, 'source' => 'web']);

        return $this->sendResponse(
            'Proccess completed successfully',
            [
                'amounts' => $amounts,
                'prices' => $prices,
                'data' => $data,
                'hasMultipleExpiryDate' => $hasMultipleExpiryDate,
                'expiry_date' => $expiry_date != '' ? Carbon::parse($expiry_date)->toDateString() : '',
                'has_greater_price' => $hasGreaterThanPrice,
                'has_greater_part_price' => $hasGreaterThanPartPrice,
                'max_price' => $max_price,
                'max_part_price' => $max_part_price
            ]
        );
    }

    public function hasMultipleExpiryDate($dataId, $merchantId)
    {
        $has_multpleExpiryDate = Amount::where('data_id', $dataId)->where('merchant_id', $merchantId)->where('expiry_date', '!=', '')->count();
        if ($has_multpleExpiryDate > 1)
            return true;
        else {
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

    public function getElementAmounts($itemId, $merchantId)
    {
        $amounts = Amount::where('data_id', $itemId)->where('merchant_id', $merchantId)->sum('amount');
        $partAmounts = Amount::where('data_id', $itemId)->where('merchant_id', $merchantId)->sum('amount_part');
        /// get buyed amounts
        $inoviceItemsCount = Invoice::where('merchant_id', $merchantId)->where('invoice_type', 1)->with(['invoiceItems' => function ($query) use ($itemId) {
            $query->where('data_id', $itemId);
        }])->get();

        $Selled_amounts = 0;
        $Selled_parts_amounts = 0;
        foreach ($inoviceItemsCount as $key => $value) {
            $Selled_amounts += $value->invoiceItems->sum('quantity');
            $Selled_parts_amounts += $value->invoiceItems->sum('quantity_parts');
        }

        $data = Data::find($itemId);
        if ($data->has_parts) {
            if ($data->num_of_parts > 0) {
                $temp = explode('.', ($Selled_amounts / $data->num_of_parts));
                $temp_parts = isset($temp[1]) ? ($temp[1] * $data->num_of_parts / 10) : 0;
                $temp_amount = $temp[0];
            }
        }

        // $buyed_amounts = Invoice::where('invoice_type',1)->where('merchant_id',$merchantId)->get()->invoiceItems()->where('data_id',$itemId)->get();
        return ['amounts' => $amounts, 'part_amounts' => $partAmounts];
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
}
