<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // No authorization needed as per requirements
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'price' => 'required|numeric|min:0',
            'city' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
        ];

        if ($this->isMethod('POST')) {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        } else {
            $rules['image'] = 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The property title is required.',
            'description.required' => 'Please provide a description.',
            'price.required' => 'The price is required.',
            'city.required' => 'The city is required.',
            'category_id.required' => 'Please select a category.',
            'image.image' => 'The file must be an image.',
            'image.max' => 'The image size should not exceed 2MB.',
        ];
    }
}
