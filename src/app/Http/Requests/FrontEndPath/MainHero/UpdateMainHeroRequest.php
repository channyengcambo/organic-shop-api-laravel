<?php

namespace App\Http\Requests\FrontEndPath\MainHero;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMainHeroRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:255'],
            'has_discount' => ['nullable', 'boolean'],

            'discount_label' => ['nullable', 'string', 'max:255'],
            'discount_content' => ['nullable', 'string'],
            'discount_value' => ['nullable', 'numeric'],
            'discount_subtitle' => ['nullable', 'string', 'max:255'],

            'order_index' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
            'target' => ['nullable', 'string', 'max:20'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'button_action' => ['nullable', 'string'],
            'total_items' => ['nullable', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has_discount) {
            $this->merge([
                'discount_label' => null,
                'discount_content' => null,
                'discount_value' => null,
                'discount_subtitle' => null,
            ]);
        }
    }

//    public function messages(): array
//    {
//        return [
//            'title.required' => 'Main hero title is required.',
//            'order_index.required' => 'Order index is required.',
//            'is_active.required' => 'Active status is required.',
//            'has_discount.required' => 'Does it is discount or not?',
//            'role_ids.required' => 'You must select at least one role.',
//            'role_ids.*.exists' => 'Selected role does not exist.',
//        ];
//    }
}
