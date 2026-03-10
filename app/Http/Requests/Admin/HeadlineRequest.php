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
            'tag' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'image_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
        ];
    }
}
