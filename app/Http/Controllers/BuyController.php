<?php

namespace App\Http\Controllers;

use App\Models\Amount;
use App\Models\BuyInvoice;
use App\Models\Data;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class BuyController extends Controller
{
    public function index()
    {
        return view('buy.create_buy_invoice');
    }

    public function store(Request $request)
    {
        try {
            $uerId = 1;

            $quantity  = $request->quantity != null ? $request->quantity : 0;
            $price = $request->price != null ? $request->price : 0;
            $total = $quantity * $price;

            $quantityParts  = $request->quantity_parts != null ? $request->quantity_parts : 0;
            $pricePart = $request->price_part != null ? $request->price_part : 0;
            $totalParts = $quantityParts * $pricePart;

            $totalFinal = $total + $totalParts;

            $user = User::find($uerId);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found']);
            }
            $merchantId = $user->merchant_id;

            $buyInvoice = BuyInvoice::create([
                "data_id" => $request->data_id,
                "quantity" =>  $quantity,
                "quantity_parts" => $quantityParts,
                "price" => $price,
                "price_part" => $pricePart,
                "total" => $total,
                "total_parts" => $totalParts,
                "total_final" =>  $totalFinal,
                "merchant_id" => $merchantId,
                "user_id" => $uerId
            ]);
            if ($buyInvoice) {
                return response()->json(['success' => true, 'message' => 'Invoice addedd successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Invoice not stored']);
            }
        } catch (Exception $th) {
            return $this->errors("BuyController@store", $th->getMessage());
        }
    }

    public function findByItemName(Request $request)
    {
        $merchantId = 1;
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
            } else {
                $price = $this->getCurrentPriceForElement($data->id, $merchantId);
            }
            if ($hasGreaterThanPartPrice) {
                $max_part_price = $this->getMaxPartPriceForElement($data->id);
            } else {
                $partprice = $this->getCurrentPartPriceForElement($data->id, $merchantId);
            }
            return response()->json(['success' => true, 'data' => $data, 'prices' => ['price' => $price , 'partprice' => $partprice], 'amounts' => $this->getElementAmounts($data->id, $merchantId), 'has_greater_price' => $hasGreaterThanPrice, 'has_greater_part_price' => $hasGreaterThanPartPrice, 'max_price' => $max_price, 'max_part_price' => $max_part_price], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found'], 400);
        }
    }

    public function hasGreaterPriceFromAnotherUser($dataId, $merchantId)
    {
        // get max price for product
        $max_price = Amount::where('data_id', $dataId)->orderBy('price', 'desc')->where('merchant_id', '!=', $merchantId)->first();

        // get price for prouct for specifc user
        $user_price = Amount::where('data_id', $dataId)->where('merchant_id', $merchantId)->orderBy('price', 'desc')->first();

        if ($max_price != null && $user_price != null) {
            if ($user_price->price < $max_price->price) {
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
        $max_price = Amount::where('data_id', $dataId)->orderBy('price_part', 'desc')->where('merchant_id', '!=', $merchantId)->first();

        // get price for prouct for specifc user
        $user_price = Amount::where('data_id', $dataId)->where('merchant_id', $merchantId)->first();

        if ($max_price != null && $user_price != null) {
            if ($user_price->price_part < $max_price->price_part) {
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
        return ['amounts' => $amounts, 'part_amounts' => $partAmounts];
    }

    public function getMaxPartPriceForElement($dataId)
    {
        // get max price for product
        $max_price = Amount::where('data_id', $dataId)->orderBy('price_part', 'desc')->first();

        if ($max_price != null) {
            return $max_price->price_part;
        } else {
            return 0;
        }
    }

    public function getMaxPriceForElement($dataId)
    {
        // get max price for product
        $max_price = Amount::where('data_id', $dataId)->orderBy('price', 'desc')->first();

        if ($max_price != null) {
            return $max_price->price;
        } else {
            return 0;
        }
    }

    public function getCurrentPartPriceForElement($dataId, $userId)
    {
        // get max price for product
        $max_price = Amount::where('data_id', $dataId)->where('merchant_id', $userId)->orderBy('price_part', 'desc')->first();

        if ($max_price != null) {
            return $max_price->price_part;
        } else {
            return 0;
        }
    }
}
