<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Amount extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeCurrentMerchant($query, $merchantId)
    {
        $query->where('merchant_id', $merchantId);
    }

    public function data()
    {
        return $this->belongsTo(Data::class, 'data_id');
    }
}
