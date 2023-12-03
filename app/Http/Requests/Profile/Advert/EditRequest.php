<?php

namespace App\Http\Requests\Profile\Advert;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:100'
            ],
            'year' => 'nullable|integer',
            'author' => 'nullable|max:60',
            'deal' => 'required|in:sale,exchange,free',
            'edition' => 'nullable|string',
            'style_id' => 'required|exists:styles,id',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|regex:/^[0-9]{0,4}\.?[0-9]{0,2}$/i',
            'state' =>  'required|in:1,2',
            'vinyl' => 'nullable|array',
            'vinyl.*' => 'nullable|string',
            'condition' => 'nullable|string|max:100',
            'relation_release' => 'nullable'
        ];
    }
}
