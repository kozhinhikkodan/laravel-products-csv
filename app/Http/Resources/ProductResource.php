<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->resource->id,
            'product_name' => $this->resource->name,
            'desctiption' => $this->resource->description,
            'sku' => $this->resource->sku,
            'price' => $this->resource->price,
            'created_at' => $this->resource->created_at,
        ];
    }
}
