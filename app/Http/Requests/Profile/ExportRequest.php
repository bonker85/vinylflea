<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'users_ids' => 'nullable|array',
            'users_ids.*' => 'nullable|integer|exists:users,id',
            'styles_ids' => 'nullable|array',
            'styles_ids.*' => 'nullable|integer|exists:styles,id',
            'sep' => 'string'
        ];
    }
}
