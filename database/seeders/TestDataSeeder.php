<?php

namespace Database\Seeders;

use App\Models\Amount;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Data;
use App\Models\DrugStore;
use App\Models\EffMaterial;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use App\Models\Shape;
use App\Models\TreatementGroup;
use App\Models\User;
use App\Models\UserData;
use Database\Factories\DataFactory;
use Illuminate\Database\Seeder;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
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
        // User::truncate();
        // UserData::truncate();
        // Data::truncate();
        // DB::table('data_effmaterials')->truncate();
        Invoice::truncate();
        InvoiceItems::truncate();
        Amount::truncate();
        DrugStore::truncate();
        Customer::truncate();

        // $this->call(MerchantsSeeder::class); // 100 merchant and 3 user for each one
        $this->call(DataSeeder::class); // 9000 product
        // $this->call(AttachDataToMerchantSeeder::class);
        // $this->call(BuyInvoiceSeeder::class); // daily 1000 invoice for 30 day
        // $this->call(SellInvoiceSeeder::class); // daily 1000 invoice for 30 day
    }
}
