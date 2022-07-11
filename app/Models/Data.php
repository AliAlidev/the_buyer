<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function amounts()
    {
        return $this->hasMany(Amount::class, "data_id","id");
    }

    public function amountsForUser($user_id)
    {
        return $this->hasMany(Amount::class, "data_id","id")->where('merchant_id', $user_id);
    }

    public function prices()
    {
        return $this->hasMany(Price::class, "data_id","id");
    }

    public function pricesForUser($user_id)
    {
        return $this->hasMany(Price::class, "data_id","id")->where('merchant_id', $user_id);
    }

    public function itemdates()
    {
        return $this->hasMany(ItemDate::class, "data_id","id");
    }
}
