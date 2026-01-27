<?php

namespace App\Http\Requests\FrontEndPath\NavigationMenuItemRequests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNavigationMenuItemRequest extends FormRequest
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
            'label' => 'required|string|max:255',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'order_index' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'parent_id' => 'nullable|exists:navigation_menu_items,id',
            'description' => 'nullable|string|max:500',
            'target' => 'nullable|in:_self,_blank',
            'image' => 'nullable|image',
            'image_action' => 'nullable|image',
            'total_items' => 'nullable|integer|min:0',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'label.required' => 'Navigation menu item label is required.',
            'order_index.required' => 'Order index is required.',
            'role_ids.required' => 'You must select at least one role.',
            'role_ids.*.exists' => 'Selected role does not exist.',
        ];
    }
}
