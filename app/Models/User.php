<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function data()
    {
        return $this->belongsToMany(Data::class, UserData::class, "merchant_id", "data_id", "merchant_id", "id");
        // return $this->belongsToMany(Data::class, UserData::class, "merchant_id", "data_id", "merchant_id", "id")->wherePivot('merchant_id', Auth::guard('api')->user()->merchant_id);
    }

    public function assigned_data()
    {
        // return $this->belongsToMany(Data::class, UserData::class, "merchant_id", "data_id", "merchant_id", "id");
        return $this->belongsToMany(Data::class, UserData::class, "merchant_id", "data_id", "merchant_id", "id")->wherePivot('merchant_id', Auth::guard('web')->user()->merchant_id);
    }

    public function is_en()
    {
        if (Auth::user()->language == 'en')
            return true;
        else
            return false;
    }

    public function isAdmin()
    {
        return Auth::user()->role == '0' ? true : false;
    }

    public function isMerchant()
    {
        return Auth::user()->role == '1' ? true : false;
    }

    public function isEmployee()
    {
        return Auth::user()->role == '2' ? true : false;
    }
}
