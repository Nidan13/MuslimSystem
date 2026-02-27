<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class QuestRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quest_type_id' => 'required|exists:quest_types,id',
            'rank_tier_id' => 'nullable|exists:rank_tiers,id',
            'reward_exp' => 'nullable|integer|min:0',
            'reward_soul_points' => 'nullable|integer|min:0',
            'is_mandatory' => 'boolean',
            'penalty_fatigue' => 'nullable|integer|min:0',
            'req_keys' => 'nullable|array',
            'req_values' => 'nullable|array',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'time_limit' => 'nullable|integer|min:1',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_mandatory' => $this->has('is_mandatory'),
        ]);
    }
}
