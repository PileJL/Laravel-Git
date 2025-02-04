<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return request result
        return parent::toArray($request);
            // or customzie what data to return:
        // return [
        //     'id' => $this->id,
        //     'name' => $this->name,
        //     'description' => $this->description,
        //     'price' => $this->price,
        //     'created_at' => $this->created_at,
        // ];

    }
}
