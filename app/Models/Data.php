<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    public function amountsForUser()
    {
        // $amounts = Amount::where('data_id', $this->id)->where('merchant_id', $user_id);
        // return $amounts;
        return $this->hasMany(Amount::class, "data_id","id");
    }

    public function User()
    {
        return $this->belongsToMany(User::class, UserData::class, "data_id", "merchant_id", "merchant_id", "id")->wherePivot('merchant_id', Auth::guard('api')->user()->merchant_id);
    }

    public function effMaterial()
    {
        return $this->belongsToMany(EffMaterial::class, 'data_effmaterials', 'data_id', 'effict_matterials_id', 'id', 'id');
    }

    public function shapes()
    {
        return $this->belongsTo(Shape::class, 'shape_id', 'shape_id');
    }

    public function companies()
    {
        return $this->belongsTo(Company::class, 'comp_id', 'comp_id');
    }

}
