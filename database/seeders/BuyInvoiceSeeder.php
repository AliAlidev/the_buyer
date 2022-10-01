<?php

namespace Database\Seeders;

use App\Models\Amount;
use App\Models\Data;
use App\Models\DrugStore;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Nette\Utils\Random;

class BuyInvoiceSeeder extends Seeder
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
        $discount = $discount_type == 2 ? $this->faker->numberBetween(0, 20) : 0;
        $drug_store_array = [];
        for ($j = 1; $j <= 50; $j++) {
            $drug_store_array[] =  $this->faker->name;;
        }

        $merchants_array = User::where('role', 1)->pluck('id')->toArray();

        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->addDays($i)->toDateTimeString();
            for ($k = 0; $k < 1000; $k++) {
                $this->generateInvoice($drug_store_array, $merchants_array, $dataIds, $discount_type, $discount, $date);
            }
        }
    }

    public function generateInvoice($drug_store_array, $merchants_array, $dataIds, $discount_type, $discount, $date)
    {
        $drug_store = $this->faker->randomElement($drug_store_array);
        $start_date = $this->faker->dateTimeThisYear($max = 'now', $timezone = null);
        $expiry_date = Carbon::parse($start_date)->addYear();
        $merchant_id = $this->faker->randomElement($merchants_array);
        $user = User::where('merchant_id', $merchant_id)->where('role', '2')->first();
        $user_id = $user->id;

        DB::beginTransaction();

        $invoiceId = DB::table('invoices')->insertGetId([
            'merchant_id' => $merchant_id,
            'user_id' => $user_id,
            'discount_type' => $discount_type,
            'discount' => $discount,
            'invoice_type' => "1",
            'payment_type' => "1",
            "drug_store_id" => $this->getDrugStoreId($drug_store),
            'notes' => '',
            'order_number' => $this->generateOrderNumber(),
            'created_at' =>  $date,
            'updated_at' =>  $date
        ]);

        $total_invoice = 0;
        for ($k = 0; $k < $this->faker->randomDigitNotNull; $k++) {
            $data_id = $this->faker->randomElement($dataIds);
            $currData = Data::where('id', $data_id)->first();
            $amount = $this->faker->randomDigitNotNull;
            $partAmount = $this->faker->numberBetween(0, $currData->num_of_parts);
            $price = $this->faker->randomNumber(3);
            $partPrice = $this->faker->randomNumber(2);
            $real_price = $price - ($price * 10 / 100);

            DB::table('amounts')->insert([
                'data_id' => $data_id,
                'amount' => $amount,
                'amount_part' => $currData->num_of_parts > 0 ? $partAmount : 0,
                'price' => $price,
                'real_price' =>  $real_price,
                'price_part' => $currData->num_of_parts > 0 ? $partPrice : 0,
                'real_part_price' => $currData->num_of_parts > 0 ? ($real_price / $currData->num_of_parts) : 0,
                'expiry_type' => 2,
                'start_date' =>  $start_date,
                'expiry_date' => $expiry_date,
                'merchant_id' => $merchant_id,
                'user_id' => $user_id,
                'amount_type' => '1',
                'amount_type_id' => $invoiceId,
                'created_at' =>  $date,
                'updated_at' =>  $date
            ]);

            $total_quantity_price = floatval($amount) * floatval($real_price);
            $total_parts_price = floatval($currData->num_of_parts > 0 ? $partAmount : 0) * floatval($currData->num_of_parts > 0 ? ($real_price / $currData->num_of_parts) : 0);
            $total_price = $total_quantity_price + $total_parts_price;
            $total_invoice += $total_price;

            DB::table('invoice_items')->insert([
                'invoice_id' => $invoiceId,
                'data_id' => $data_id,
                'quantity' => $amount,
                'quantity_parts' => $currData->num_of_parts > 0 ? $partAmount : 0,
                'price' => $real_price,
                'price_part' => $currData->num_of_parts > 0 ? $partPrice : 0,
                'total_quantity_price' => $total_quantity_price,
                'total_parts_price' =>  $total_parts_price,
                'total_price' => $total_price,
                'created_at' =>  $date,
                'updated_at' =>  $date
            ]);
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

        DB::table('invoices')->where('id', $invoiceId)->update(['paid_amount' => $paid_amount, 'total_amount' => $total_invoice, 'discount' => $discount]);
        DB::commit();
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
}
