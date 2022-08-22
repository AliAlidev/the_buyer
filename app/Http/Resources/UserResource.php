<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $role = null;
        if ($this->role == 0)
            $role = "Admin";
        if ($this->role == 1)
            $role = "Merchant";
        if ($this->role == 2)
            $role = "Employee";

        $merchant_type = null;
        if ($this->merchant_type == 0)
            $merchant_type = "Admin";
        else if ($this->merchant_type == 1)
            $merchant_type = "Pharmacist";
        else if ($this->merchant_type == 2)
            $merchant_type = "Market";

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'merchant_id' => $this->merchant_id,
            'role' => $role,
            'phone' => $this->phone,
            'tel_phone' => $this->tel_phone,
            'country' => $this->country,
            'city' => $this->city,
            'address' => $this->address,
            'merchant_type' => $merchant_type,
            'notes' => $this->notes
        ];
    }
}
