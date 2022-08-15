<?php

namespace App\Http\Controllers;

use App\Models\Amount;
use App\Models\BuyInvoice;
use App\Models\Data;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found']);
            }


            $discount = $request->discount;
            $total_amount = trim(str_replace("sp", "", $request->total_invoice_value));
            $paid_amount = $request->paid_amount;

            if ($paid_amount == 0) {
                $paid_amount = $total_amount;
            }

            if ($discount > 0) {
                $paid_amount =  $paid_amount - (($paid_amount * $discount) / 100);
            }
            $invoice = Invoice::create([
                'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id,
                'user_id' => $user->id,
                'total_amount' => $total_amount,
                'discount' => $discount,
                'paid_amount' => $paid_amount,
                'invoice_type' => $request->invoice_type,
                'notes' => $request->notes
            ]);

            $invoiceItems = json_decode($request->items);
            $items = [];
            foreach ($invoiceItems as $key => $item) {
                $totalQuantityPrice = $item->quantity * $item->price;
                $totalQuantityPartPrice = $item->quantityP * $item->priceP;
                $total = $totalQuantityPartPrice + $totalQuantityPrice;

                $items[] = new InvoiceItems([
                    "data_id" => $item->data_id,
                    "quantity" =>  $item->quantity,
                    "quantity_parts" => $item->quantityP,
                    "price" => $item->price,
                    "price_part" => $item->priceP,
                    "total_quantity_price" => $totalQuantityPrice,
                    "total_parts_price" => $totalQuantityPartPrice,
                    "total_price" =>  $total
                ]);
            }

            $res =  $invoice->invoiceItems()->saveMany($items);

            if ($res) {
                return response()->json(['success' => true, 'message' => 'Invoice addedd successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Invoice not totaly stored']);
            }
        } catch (Exception $th) {
            dd($th->getMessage());
            return $this->errors("BuyController@store", $th->getMessage());
        }
    }

    public function findByItemCode(Request $request)
    {
        $merchantId = Auth::user()->role == 3 ? Auth::user()->merchant_id : Auth::user()->id;
        $data = Data::where('code', $request->code)->first();
        if ($data) {
            $hasGreaterThanPrice = $this->hasGreaterPriceFromAnotherUser($data->id, $merchantId);
            $hasGreaterThanPartPrice = $this->hasGreaterPartPriceFromAnotherUser($data->id, $merchantId);

            $max_price = 0;
            $price = 0;
            $max_part_price = 0;
            $partprice = 0;
            if ($hasGreaterThanPrice) {
                $max_price = $this->getMaxPriceForElement($data->id);
                $price = $this->getCurrentPriceForElement($data->id, $merchantId);
            } else {
                $price = $this->getCurrentPriceForElement($data->id, $merchantId);
            }
            if ($hasGreaterThanPartPrice) {
                $max_part_price = $this->getMaxPartPriceForElement($data->id);
                $partprice = $this->getCurrentPartPriceForElement($data->id, $merchantId);
            } else {
                $partprice = $this->getCurrentPartPriceForElement($data->id, $merchantId);
            }

            $expiry_date = Amount::where('data_id', $data->id)->where('merchant_id', $merchantId)->where('expiry_date', '!=', '')->orderBy('expiry_date')->first();
            $expiry_date = $expiry_date != null ? $expiry_date->expiry_date : '';

            $hasMultipleExpiryDate = $this->hasMultipleExpiryDate($data->id, $merchantId);

            return response()->json(
                [
                    'success' => true,
                    'hasMultipleExpiryDate' => $hasMultipleExpiryDate,
                    'data' => $data,
                    'expiry_date' => $expiry_date,
                    'prices' => ['price' => $price, 'partprice' => $partprice],
                    'amounts' => $this->getElementAmounts($data->id, $merchantId),
                    'has_greater_price' => $hasGreaterThanPrice,
                    'has_greater_part_price' => $hasGreaterThanPartPrice,
                    'max_price' => $max_price,
                    'max_part_price' => $max_part_price
                ],
                200
            );
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found'], 400);
        }
    }

    public function findByItemName(Request $request)
    {
        try {
            $merchantId = Auth::user()->role == 3 ? Auth::user()->merchant_id : Auth::user()->id;
            $data = Data::where('name', $request->name)->first();
            if ($data) {
                $hasGreaterThanPrice = $this->hasGreaterPriceFromAnotherUser($data->id, $merchantId);
                $hasGreaterThanPartPrice = $this->hasGreaterPartPriceFromAnotherUser($data->id, $merchantId);

                $max_price = 0;
                $price = 0;
                $max_part_price = 0;
                $partprice = 0;
                if ($hasGreaterThanPrice) {
                    $max_price = $this->getMaxPriceForElement($data->id);
                    $price = $this->getCurrentPriceForElement($data->id, $merchantId);
                } else {
                    $price = $this->getCurrentPriceForElement($data->id, $merchantId);
                }
                if ($hasGreaterThanPartPrice) {
                    $max_part_price = $this->getMaxPartPriceForElement($data->id);
                    $partprice = $this->getCurrentPartPriceForElement($data->id, $merchantId);
                } else {
                    $partprice = $this->getCurrentPartPriceForElement($data->id, $merchantId);
                }

                $expiry_date = Amount::where('data_id', $data->id)->where('merchant_id', $merchantId)->where('expiry_date', '!=', '')->orderBy('expiry_date')->first();
                $expiry_date = $expiry_date != null ? $expiry_date->expiry_date : '';

                $hasMultipleExpiryDate = $this->hasMultipleExpiryDate($data->id, $merchantId);

                return response()->json(
                    [
                        'success' => true,
                        'hasMultipleExpiryDate' => $hasMultipleExpiryDate,
                        'data' => $data,
                        'expiry_date' => $expiry_date,
                        'prices' => ['price' => $price, 'partprice' => $partprice],
                        'amounts' => $this->getElementAmounts($data->id, $merchantId),
                        'has_greater_price' => $hasGreaterThanPrice,
                        'has_greater_part_price' => $hasGreaterThanPartPrice,
                        'max_price' => $max_price,
                        'max_part_price' => $max_part_price
                    ],
                    200
                );
            } else {
                return response()->json(['success' => false, 'message' => 'Data not found'], 400);
            }
        } catch (Exception $th) {
            dd($th->getMessage());
        }
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
