<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = "invoices";
    protected $guarded = [];

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItems::class, 'invoice_id', 'id');
    }

    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id', 'merchant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function Store()
    {
       return  $this->belongsTo(DrugStore::class, 'store_id');
    }

    public function customer()
    {
       return  $this->belongsTo(Customer::class, 'customer_id');
    }
}
