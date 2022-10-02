<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Data;
use App\Models\DrugStore;
use App\Models\Invoice;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Random;
use PDF;

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

        $user = Auth::guard('api')->user();
        $total_invoice = 0;

        DB::beginTransaction();
        $order_number = $this->generateOrderNumber();
        $invoiceId = DB::table('invoices')->insertGetId([
            'merchant_id' => $user->role == 2 ? $user->merchant_id : $user->id,
            'user_id' => $user->id,
            'total_amount' => $total_invoice,
            'discount_type' => $request->discount_type,
            'discount' => $request->discount,
            'invoice_type' => "1",
            'payment_type' => $request->payment_type,
            "drug_store_id" => $this->getDrugStoreId($request->drug_store),
            'notes' => $request->notes,
            'order_number' => $order_number,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString()
        ]);

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

            DB::table('amounts')->insert([
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
                'merchant_id' => $user->role == 2 ? $user->merchant_id : $user->id,
                'user_id' => $user->id,
                'amount_type' => "1",
                'amount_type_id' => $invoiceId,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ]);

            $total_quantity_price = floatval($item['amount']) * floatval($item['real_price']);
            $total_parts_price = floatval($item['part_amount']) * floatval($real_part_price);
            $total_price = $total_quantity_price + $total_parts_price;
            $total_invoice += $total_price;

            DB::table('invoice_items')->insert([
                'invoice_id' => $invoiceId,
                'data_id' => $data->id,
                'quantity' => $item['amount'],
                'quantity_parts' => $item['part_amount'],
                'price' => $item['real_price'],
                'price_part' => $real_part_price,
                'total_quantity_price' => $total_quantity_price,
                'total_parts_price' =>  $total_parts_price,
                'total_price' => $total_price,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ]);
        }

        $paid_amount = 0;
        if ($request->discount_type == 1) {
            $paid_amount = $total_invoice - $request->discount;
        } else if ($request->discount_type == 2) {
            $paid_amount = $total_invoice - $total_invoice * $request->discount / 100;
        } else {
            $paid_amount = $total_invoice;
        }

        DB::table('invoices')->where('id', $invoiceId)->update(['paid_amount' => $paid_amount, 'total_amount' => $total_invoice]);
        DB::commit();

        return $this->sendResponse("Invoice created successfully", [
            'order_number' => $order_number,
            'pdf_link' => $this->saveBuyInvoice($order_number), 'view_link' => route('view.invoice', $order_number)
        ]);
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

    public function getDrugStoreId($name)
    {
        if ($name == null) {
            $store = DrugStore::firstOrCreate(['name' => 'general store'], [
                'name' => 'general store'
            ]);
        } else {
            $store = DrugStore::where('name', $name)->first();
            if (!$store) {
                $store = DrugStore::create([
                    'name' => $name
                ]);
            }
        }
        return $store->id;
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

    public static function saveBuyInvoice($order_number)
    {
        $invoice = Invoice::where('order_number', $order_number)->first();
        if ($invoice) {
            $invoice_type = $invoice->invoice_type == 1 ? 'BUY' : ($invoice->invoice_type == 2 ? 'SELL' : '');
            $from = $invoice->merchant->name;
            $customer = $invoice->drugStore->name;
            $pdf = PDF::loadView('Invoice.invoice', ['invoice' => $invoice, 'invoice_type' => $invoice_type, 'from' => $from, 'customer' => $customer]);
            $user = Auth::guard('api')->user();
            $invoiceName = 'Invoice_' . $user->id . '.pdf';
            /** Here you can use the path you want to save */
            $pdf->save(public_path('uploads/invoices/' . $invoiceName));
            return asset('uploads/invoices/' . $invoiceName);
        }
        return abort(404);
    }

    public function sell(Request $request)
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

        $apiProductController = new ApiProductController();

        $user = Auth::guard('api')->user();

        $total_invoice = 0;

        DB::beginTransaction();
        $order_number = $this->generateOrderNumber();
        $invoiceId = DB::table('invoices')->insertGetId([
            'merchant_id' => $user->role == 2 ? $user->merchant_id : $user->id,
            'user_id' => $user->id,
            'discount_type' => $request->discount_type,
            'discount' => $request->discount,
            'invoice_type' => "2",
            'payment_type' => $request->payment_type,
            "customer_id" => $this->getCustomerId($request->customer_name),
            'notes' => $request->notes,
            'order_number' => $order_number,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString()
        ]);

        foreach ($items as $key => $item) {

            $validator = Validator::make($item, [
                'data_id' => 'required|exists:data,id',
                'amount' => 'required_without:part_amount|integer',
                'part_amount' => 'required_without:amount|integer',
                'price' => 'required|numeric|gt:0'
            ]);
            if ($validator->fails()) {
                return $this->sendErrorResponse("Validation error", $validator->getMessageBag());
            }

            if ($item['data_id'] && $this->missingMedPartCount('id', $item['data_id'], $item['part_amount'])) {
                return $this->sendErrorResponse("Validation error", "You should add element parts count for item " . $key + 1);
            }

            // check amounts
            $currVal = $apiProductController->getProductAmounts($item['data_id']);

            if (!$this->enoughAmounts($currVal['amounts'], $currVal['part_amounts'], $currVal['num_of_parts'], $item['amount'], $item['part_amount'])) {
                return $this->sendErrorResponse("Validation error", "Amounts not enough for item " . $key + 1);
            }

            DB::table('amounts')->insert([
                'data_id' => $item['data_id'],
                'amount' => $item['amount'],
                'amount_part' => $item['part_amount'],
                'price' => $item['price'],
                'price_part' => $item['part_price'],
                'merchant_id' => $user->role == 2 ? $user->merchant_id : $user->id,
                'user_id' => $user->id,
                'amount_type' => "2",
                'amount_type_id' => $invoiceId,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ]);

            $total_quantity_price = floatval($item['amount']) * floatval($item['price']);
            $total_parts_price = floatval($item['part_amount']) * floatval($item['part_price']);
            $total_price = $total_quantity_price + $total_parts_price;
            $total_invoice += $total_price;

            DB::table('invoice_items')->insert([
                'invoice_id' => $invoiceId,
                'data_id' => $item['data_id'],
                'quantity' => $item['amount'],
                'quantity_parts' => $item['part_amount'],
                'price' => $item['price'],
                'price_part' => $item['part_price'],
                'total_quantity_price' => $total_quantity_price,
                'total_parts_price' =>  $total_parts_price,
                'total_price' => $total_price,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ]);
        }

        $paid_amount = 0;
        if ($request->discount_type == 1) {
            $paid_amount = $total_invoice - $request->discount;
        } else if ($request->discount_type == 2) {
            $paid_amount = $total_invoice - $total_invoice * $request->discount / 100;
        } else {
            $paid_amount = $total_invoice;
        }

        dB::table('invoices')->where('id', $invoiceId)->update(['total_amount' => $total_invoice, 'paid_amount' => $paid_amount]);
        DB::commit();

        return $this->sendResponse("Invoice created successfully", [
            'order_number' => $order_number,
            'pdf_link' => $this->saveSellInvoice($order_number), 'view_link' => route('view.invoice', $order_number)
        ]);
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

    public static function saveSellInvoice($order_number)
    {
        $invoice = Invoice::where('order_number', $order_number)->first();
        if ($invoice) {
            $invoice_type = $invoice->invoice_type == 1 ? 'BUY' : ($invoice->invoice_type == 2 ? 'SELL' : '');
            $from = $invoice->merchant->name;
            $customer = $invoice->customer->name;
            $pdf = PDF::loadView('Invoice.invoice', ['invoice' => $invoice, 'invoice_type' => $invoice_type, 'from' => $from, 'customer' => $customer]);
            $user = Auth::guard('api')->user();
            $invoiceName = 'Invoice_' . $user->id . '.pdf';
            /** Here you can use the path you want to save */
            $pdf->save(public_path('uploads/invoices/' . $invoiceName));
            return asset('uploads/invoices/' . $invoiceName);
        }
        return abort(404);
    }

    public function enoughAmounts($currAmount, $currPartAmount, $itemPartCounts, $val, $pVal)
    {
        if ($itemPartCounts > 0) {
            $total = $currAmount * $itemPartCounts + $currPartAmount;
            $totalNeed = $val * $itemPartCounts + $pVal;
        } else {
            $total = $currAmount;
            $totalNeed = $val;
        }

        if ($total >= $totalNeed)
            return true;
        else
            return false;
    }

    public function inventoryAmounts(Request $request)
    {
        try {
            if ($request->start_date && !$this->is_date($request->start_date))
                return $this->sendErrorResponse("Start date should be valid date!");

            if (!$this->isPositiveInt($request->amount))
                return $this->sendErrorResponse("You should enter valid amount value!");

            if (!$this->isPositiveInt($request->price))
                return $this->sendErrorResponse("You should enter valid price value!");

            if (!$this->isPositiveInt($request->part_amount))
                return $this->sendErrorResponse("You should enter valid part amount value!");

            $user = Auth::guard('api')->user();
            $data = Data::find($request->data_id);
            $price = $request->price;
            $partPrice = 0;
            if ($data->num_of_parts > 0) {
                $partPrice = $price / $data->num_of_parts;
            }
            $startDate = $request->start_date;
            $startDate = "";
            $endDate = "";
            if ($request->expiry_type == 1) {
                $startDate = $request->start_date;
                if ($request->expiry_value && !$this->is_date($request->expiry_value))
                    return $this->sendErrorResponse("Expiry date should bew valid date!");

                $endDate = $request->expiry_value;
            } else if ($request->expiry_type == 2) {
                $startDate = $request->start_date;
                $endDate = Carbon::parse($request->start_date)->addMonths($request->expiry_value)->toDateString();
            } else if ($request->expiry_type == 3) {
                if ($request->expiry_value && !$this->is_date($request->expiry_value))
                    return $this->sendErrorResponse("Expiry date should bew valid date!");
                $endDate = $request->expiry_value;
            }

            DB::table('amounts')->insert([
                'data_id' => $data->id,
                'amount' => $request->amount,
                'amount_part' => $request->part_amount,
                'price' => $price,
                'price_part' => $partPrice,
                'merchant_id' => $user->role == 2 ? $user->merchant_id : $user->id,
                'user_id' => $user->id,
                'amount_type' => "0",
                'start_date' => $startDate,
                'expiry_date' => $endDate,
                'amount_type_id' => '',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ]);

            return $this->sendResponse("Proccess Completed successfully");
        } catch (Exception $th) {
            return $this->errors("ApiOrderController@inventoryAmounts", $th->getMessage());
        }
    }

    public function productReturn(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_number' => 'required|exists:invoices,order_number'
            ]);
            if ($validator->fails()) {
                return $this->sendErrorResponse("Validation errors", $validator->getMessageBag());
            }

            DB::beginTransaction();
            $invoiceData = DB::table('invoices')->where('order_number', $request->order_number)->first();
            $amounts = DB::table('amounts')->where('amount_type_id', $invoiceData->id)->get();
            foreach ($amounts as $key => $amount) {
                DB::table('product_returns')->insert([
                    "data_id" => $amount->data_id,
                    "amount" => $amount->amount,
                    "amount_part" => $amount->amount_part,
                    "price" => $amount->price,
                    "price_part" => $amount->price_part,
                    "start_date" => $amount->start_date,
                    "expiry_date" => $amount->expiry_date,
                    "merchant_id" => $amount->merchant_id,
                    "user_id" => $amount->user_id,
                    "return_type" => $amount->amount_type,
                    "return_side_id" => $invoiceData->invoice_type == 1 ? $invoiceData->drug_store_id : ($invoiceData->invoice_type == 2 ? $invoiceData->customer_id : ''),
                    "created_at" => now()->toDateTimeString(),
                    "updated_at" => now()->toDateTimeString()
                ]);
                DB::table('amounts')->where('id', $amount->id)->delete();
            }
            DB::table('invoice_items')->where('invoice_id', $invoiceData->id)->delete();
            DB::table('invoices')->where('id', $invoiceData->id)->delete();
            DB::commit();
            return $this->sendResponse("Proccess completed successfully");
        } catch (Exception $th) {
            return $this->errors("ApiProductController@productReturn", $th->getMessage());
        }
    }

}
