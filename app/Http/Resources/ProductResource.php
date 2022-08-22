<?php

namespace App\Http\Resources;

use App\Models\Company;
use App\Models\Shape;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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

        if ($this->merchant_type == 1 || $this->merchant_type  == 0) //  Pharmacist or admin
            return [
                'code' => $this->code,
                'name' => $this->name,
                'shape' => $this->shape_id == null ? 0 : new ShapeResource(Shape::find($this->shape_id)),
                'company' => $this->comp_id == null ? 0 : new CompResource(Company::find($this->comp_id)),
                'has_parts' => $this->has_parts == null? false: $this->has_parts,
                'num_of_parts' => $this->num_of_parts,
                'description' => $this->description,
                'minimum_amount' => $this->minimum_amount,
                'maximum_amount' => $this->maximum_amount,
                'dose' => $this->dose,
                'tab_count' => $this->tab_count,
                'treatements' => $this->treatements,
                'special_alarms' => $this->special_alarms,
                'interference' => $this->interference,
                'side_effects' => $this->side_effects,
                'treatement_group' => $this->treatement_group,
                'merchant_type' => $merchant_type,
                'created_by' => new UserResource(User::find($this->created_by))
            ];
        else    // Market
            return [
                'code' => $this->code,
                'name' => $this->name,
                'shape_id' => $this->shape_id == null ? 0 : $this->shape_id,
                'comp_id' => $this->comp_id == null ? 0 : $this->comp_id,
                'has_parts' => $this->has_parts,
                'num_of_parts' => $this->num_of_parts,
                'description' => $this->description,
                'minimum_amount' => $this->minimum_amount,
                'maximum_amount' => $this->maximum_amount,
                'merchant_type' => $merchant_type,
                'created_by' => new UserResource(User::find($this->created_by))
            ];
    }
}
