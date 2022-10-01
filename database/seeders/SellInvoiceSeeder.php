<?php

namespace Database\Seeders;

use App\Http\Controllers\Apis\ApiProductController;
use App\Models\Amount;
use App\Models\Customer;
use App\Models\Data;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Nette\Utils\Random;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class SellInvoiceSeeder extends Seeder
{
    public function __construct()
    {
        $this->faker = $this->withFaker();
    }

    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataIds = Data::orderBy('id')->pluck('id')->toArray();
        $discount_type = $this->faker->randomElement(['0', '1', '2']);
        $discount = $discount_type == 2 ? $this->faker->numberBetween(0, 100) : 0;
        $customer_array = [];
        for ($j = 1; $j <= 50; $j++) {
            $customer_array[] =  $this->faker->name;
        }

        $merchants_array = User::where('role', 1)->pluck('id')->toArray();

        // generate (n) invoices for one month from now
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->addDays($i)->toDateTimeString();
            // generate (n) invoices for (n) random merchants
            for ($k = 0; $k < 1000; $k++) {
                $this->generateInvoice($merchants_array, $customer_array, $dataIds, $discount_type, $discount, $date);
            }
        }
    }

    public function generateInvoice($merchants_array, $customer_array, $dataIds, $discount_type, $discount, $date)
    {
        $merchant_id = $this->faker->randomElement($merchants_array);
        $user = User::where('merchant_id', $merchant_id)->where('role', '2')->first();
        $user_id = $user->id;
        $customer = $this->faker->randomElement($customer_array);

        DB::beginTransaction();
        $order_number = $this->generateOrderNumber();
        $invoiceId = DB::table('invoices')->insertGetId([
            'merchant_id' => $merchant_id,
            'user_id' => $user_id,
            'discount_type' => $discount_type,
            'discount' => $discount,
            'invoice_type' => "2",
            'payment_type' => "1",
            "customer_id" => $this->getCustomerId($customer),
            'notes' => '',
            'order_number' => $order_number,
            'created_at' => $date,
            'updated_at' => $date
        ]);

        $total_invoice = 0;
        for ($k = 0; $k < $this->faker->randomDigitNotNull; $k++) {
            // $amount = $this->faker->randomDigitNotNull;
            // $partAmount = $this->faker->randomDigitNotNull;
            $data_id = $this->faker->randomElement($dataIds);
            $currData = Data::where('id', $data_id)->first();
            $currAmounts = app()->call('App\Http\Controllers\Apis\ApiProductController@getProductAmounts', ['dataId' => $data_id, 'merchant_id' => $merchant_id]);

            $sellAmount = $this->faker->numberBetween(1, $currAmounts['num_of_parts']);
            if ($currAmounts['num_of_parts'] > 0)
                $sellPartAmount = $this->faker->numberBetween(1, $currAmounts['amounts']);
            else
                $sellPartAmount = 0;
            
            if (app()->call('App\Http\Controllers\Apis\ApiOrderController@enoughAmounts', ['currAmount' => $currAmounts['amounts'], 'currPartAmount' => $currAmounts['part_amounts'], 'itemPartCounts' => $currAmounts['num_of_parts'], 'val' => $sellAmount, 'pVal' => $sellPartAmount])) {
                $currPrice = app()->call('App\Http\Controllers\Apis\ApiProductController@getCurrentPriceForElement', ['dataId' => $data_id, 'merchant_id' => $merchant_id]);
                $price = $currPrice['price'];
                $partPrice = $currPrice['part_price'];

                DB::table('amounts')->insert([
                    'data_id' => $data_id,
                    'amount' => $sellAmount,
                    'amount_part' => $sellPartAmount,
                    'price' => $price,
                    'price_part' => $currData->num_of_parts > 0 ? $partPrice : 0,
                    'merchant_id' => $merchant_id,
                    'user_id' => $user_id,
                    'amount_type' => $this->faker->randomElement(['0', '1', '2']),
                    'amount_type_id' => $invoiceId,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);

                $total_quantity_price = floatval($sellAmount) * floatval($price);
                $total_parts_price = floatval($currData->num_of_parts > 0 ? $sellPartAmount : 0) * floatval($currData->num_of_parts > 0 ? ($price / $currData->num_of_parts) : 0);
                $total_price = $total_quantity_price + $total_parts_price;
                $total_invoice += $total_price;

                DB::table('invoice_items')->insert([
                    'invoice_id' => $invoiceId,
                    'data_id' => $data_id,
                    'quantity' => $sellAmount,
                    'quantity_parts' => $currData->num_of_parts > 0 ? $sellPartAmount : 0,
                    'price' => $price,
                    'price_part' => $currData->num_of_parts > 0 ? $partPrice : 0,
                    'total_quantity_price' => $total_quantity_price,
                    'total_parts_price' =>  $total_parts_price,
                    'total_price' => $total_price,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
            }

        }

        $paid_amount = 0;
        if ($discount_type == 1) {
            $discount =  $this->faker->numberBetween(0, 20) * $total_invoice / 100;
            $paid_amount = $total_invoice - $discount;
        } else if ($discount_type == 2) {
            $paid_amount = $total_invoice - $total_invoice * $discount / 100;
        } else {
            $paid_amount = $total_invoice;
        }

        dB::table('invoices')->where('id', $invoiceId)->update(['total_amount' => $total_invoice, 'paid_amount' => $paid_amount, 'discount' => $discount]);
        DB::commit();
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
}
