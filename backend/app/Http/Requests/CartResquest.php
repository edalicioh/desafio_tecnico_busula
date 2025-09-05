<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartResquest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'nullable|numeric',
            'userId' => 'required|string',
            'status' => 'required|in:OPEN,CHECKOUT,CANCELED',
            'items' => 'required|array',
            'items.*.id' => 'required|numeric|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ];
    }
}
