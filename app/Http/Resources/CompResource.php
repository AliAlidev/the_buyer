<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompResource extends JsonResource
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
            'comp_id' => $this->comp_id,
            'ar_comp_name' => $this->ar_comp_name,
            'en_comp_name' => $this->en_comp_name,
            'merchant_type' => $merchant_type
        ];
    }
}
