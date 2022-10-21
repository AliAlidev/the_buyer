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

class InvoiceController extends Controller
{

    public function sell_index()
    {
        return view('sell.create_sell_invoice');
    }

    public function buy_index()
    {
        return view('buy.create_buy_invoice');
    }

    public function sell(Request $request)
    {
        $result = app()->call('App\Http\Controllers\Apis\ApiOrderController@sell', ['source' => 'web']);
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
