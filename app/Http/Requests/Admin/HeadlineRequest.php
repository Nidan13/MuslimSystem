<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HeadlineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tag'         => 'required|string|max:255',
            'category_id' => 'nullable',
            'title'       => 'required|string|max:255',
            'summary'     => 'nullable|string',
            'content'     => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'   => 'nullable',
            'is_for_user' => 'nullable',
            'is_for_landing_page' => 'nullable',
        ];
    }
}
