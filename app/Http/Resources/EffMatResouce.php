<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EffMatResouce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $merchant_type = null;
        if ($this->merchant_type == 0)
            $merchant_type = "Admin";
        else if ($this->merchant_type == 1)
            $merchant_type = "Pharmacist";
        else if ($this->merchant_type == 2)
            $merchant_type = "Market";

        return [
            'eff_mat_id' => $this->eff_mat_id,
            'en_name' => $this->en_name,
            'ar_name' => $this->ar_name,
            'merchant_type' => $merchant_type
        ];
    }
}
