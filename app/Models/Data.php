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
        $amounts = Amount::where('data_id', $this->id)->where('merchant_id', $this->merchant_id)->get();
        return $amounts;
        // return $this->hasMany(Amount::class, "id","data_id");
    }

    public function amountsForUser($user_id)
    {
        $amounts = Amount::where('data_id', $this->id)->where('merchant_id', $user_id);
        return $amounts;
        // return $this->hasMany(Amount::class, "id","data_id")->where('merchant_id', $user_id);
    }
}
