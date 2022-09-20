<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Amount;
use App\Models\Data;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isJson;

class ApiOrderController extends Controller
{
    public function buy(Request $request)
    {
        $items = null;
        if (!is_array($request->data)) {
            $items = json_decode($request->data, true);
        } else {
            $items = $request->data;
        }
        if (!$items) {
            return $this->sendErrorResponse("Validation error", "You should add order items");
        }
        foreach ($items as $key => $item) {
            $validator = Validator::make($item, [
                'code' => 'required_if:name,!=,null',
                'name' => 'required_if:code,!=,null',
                'amount' => 'required_without:part_amount|integer',
                'part_amount' => 'required_without:amount|integer',
                'price' => 'required|numeric|gt:0',
                'real_price' => 'required|numeric|gt:0'
            ]);
            if ($validator->fails()) {
                return $this->sendErrorResponse("Validation error", $validator->getMessageBag());
            }
            if ($item['code'] && !$this->itemExists('code', $item['code'])) {
                return $this->sendErrorResponse("Validation error", "You should enter valid code for item " . $key + 1);
            }
            if ($item['name'] && !$this->itemExists('name', $item['name'])) {
                return $this->sendErrorResponse("Validation error", "You should enter valid name for item " . $key + 1);
            }
            if ($item['code'] && $this->missingMedPartCount('code', $item['code'], $item['part_amount'])) {
                return $this->sendErrorResponse("Validation error", "You should add element parts count for item " . $key + 1);
            }
            if ($item['name'] && $this->missingMedPartCount('name', $item['name'], $item['part_amount'])) {
                return $this->sendErrorResponse("Validation error", "You should add element parts count for item " . $key + 1);
            }
        }

        $user = Auth::guard('api')->user();
        $invoiceItemsList = [];
        $amounts_list = [];
        $total_invoice = 0;

        // insert into invoice
        $invoice = Invoice::create([
            'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id,
            'user_id' => $user->id,
            'total_amount' => $total_invoice,
            'discount_type' => $request->discount_type,
            'discount' => $request->discount,
            'paid_amount' => $request->paid_amount,
            'invoice_type' => "1",
            'notes' => $request->notes
        ]);

        foreach ($items as $key => $item) {
            $data = "";
            if ($item['code']) {
                $data = Data::where('code', $item['code'])->first();
            } else if ($item['name']) {
                $data = Data::where('name', $item['name'])->first();
            }

            $part_price = 0;
            $real_part_price = 0;
            if ($data['num_of_parts'] && intval($data['num_of_parts']) > 0) {
                $part_price = intval($item['price']) / intval($data['num_of_parts']);
                $real_part_price = intval($item['real_price']) / intval($data['num_of_parts']);
            }
            $startDate = "";
            $endDate = "";
            if ($item['expiry_type'] == 1) {
                $startDate = $item['start_date'];
                $endDate = $item['expiry_value'];
            } else if ($item['expiry_type'] == 2) {
                $startDate = $item['start_date'];
                $endDate = Carbon::parse($item['start_date'])->addMonths($item['expiry_value'])->toDateString();
            } else if ($item['expiry_type'] == 3) {
                $endDate = $item['expiry_value'];
            }

            $amounts_list[] = [
                'data_id' => $data->id,
                'amount' => $item['amount'],
                'amount_part' => $item['part_amount'],
                'price' => $item['price'],
                'real_price' => $item['real_price'],
                'price_part' => $part_price,
                'real_part_price' => $real_part_price,
                'expiry_type' => $item['expiry_type'],
                'start_date' => $startDate,
                'expiry_date' => $endDate,
                'merchant_id' => $user->role == 3 ? $user->merchant_id : $user->id,
                'user_id' => $user->id,
                'amount_type' => "1",
                'amount_type_id' => $invoice->id,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ];

            $total_quantity_price = floatval($item['amount']) * floatval($item['real_price']);
            $total_parts_price = floatval($item['part_amount']) * floatval($real_part_price);
            $total_price = $total_quantity_price + $total_parts_price;
            $total_invoice += $total_price;
            $invoiceItemsList[] = new InvoiceItems([
                'data_id' => $data->id,
                'quantity' => $item['amount'],
                'quantity_parts' => $item['part_amount'],
                'price' => $item['real_price'],
                'price_part' => $real_part_price,
                'total_quantity_price' => $total_quantity_price,
                'total_parts_price' =>  $total_parts_price,
                'total_price' => $total_price
            ]);
        }

        $invoice->total_amount = $total_invoice;

        $paid_amount = 0;
        if ($request->discount_type == 1) {
            $paid_amount = $total_invoice - $request->discount;
        } else if ($request->discount_type == 2) {
            $paid_amount = $total_invoice - $total_invoice * $request->discount / 100;
        }
        $invoice->paid_amount = $paid_amount;
        $invoice->Save();

        $invoice->invoiceItems()->saveMany($invoiceItemsList);
        Amount::insert($amounts_list);

        return $this->sendResponse("Invoice created successfully");
    }

    public function itemExists($col, $val)
    {
        if (Data::where($col, $val)->exists())
            return true;
        else
            return false;
    }

    public function missingMedPartCount($col, $data, $partAmount)
    {
        $data = Data::where($col, $data)->first();
        if ($data && $partAmount > 0 && $data->num_of_parts == 0) {
            return true;
        } else {
            return false;
        }
    }
}
