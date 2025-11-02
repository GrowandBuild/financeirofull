<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'goal_category' => 'required|in:fixed_expenses,professional_resources,emergency_reserves,leisure,debt_installments',
            'unit' => 'required|string|max:10',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants' => 'nullable|array|min:1',
            'variants.*.name' => 'required_with:variants|string|max:255',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.unit' => 'required_with:variants|string|max:10',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do produto é obrigatório.',
            'name.max' => 'O nome do produto não pode ter mais de 255 caracteres.',
            'unit.required' => 'A unidade é obrigatória.',
            'image_file.image' => 'O arquivo deve ser uma imagem válida.',
            'image_file.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif ou webp.',
            'image_file.max' => 'A imagem não pode ter mais de 2MB.',
            'variants.min' => 'Deve ter pelo menos uma variante quando especificado.',
            'variants.*.name.required_with' => 'O nome da variante é obrigatório.',
            'variants.*.price.numeric' => 'O preço da variante deve ser um número válido.',
            'variants.*.price.min' => 'O preço da variante deve ser maior ou igual a zero.',
            'variants.*.unit.required_with' => 'A unidade da variante é obrigatória.',
        ];
    }
}
