<?php

namespace App\Http\Controllers;

use App\Models\BuyInvoice;
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
            return $this->errors("BuyController@store", $th->getMessage() );
        }
    }
}
