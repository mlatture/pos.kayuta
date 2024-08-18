<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'taxType'   =>  $this->taxType,
            'name'=> $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'barcode' => $this->barcode,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount ?? 0,
            'tax_type' => $this->tax_type,
            'tax' => $this->tax ?? 0,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'image_url' => $this->image_url
        ];
    }
}
