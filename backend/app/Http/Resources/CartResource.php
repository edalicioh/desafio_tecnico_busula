<?php

namespace App\Http\Resources;

use App\Http\Resources\CartItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'session_id' => $this->session_id,
            'status' => $this->status,
            'total' => $this->total ?? 0,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
