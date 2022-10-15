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
                'max:100',
                Rule::unique('adverts', 'name')
                    ->where('user_id', auth()->user()->id)
                    ->where(function ($query) {
                        return $query->where('id', '!=', $this->id);
                    })
            ],
            'year' => 'nullable|integer',
            'author' => 'nullable|max:40',
            'deal' => 'required|in:sale,exchange,free',
            'edition_id' => 'nullable|exists:editions,id',
            'style_id' => 'required|exists:styles,id',
            'description' => 'required|string|max:1000',
            'price' => 'required|regex:/^[0-9]{0,4}\.?[0-9]{0,2}$/i',
            'state' =>  'required|in:1,2',
            'vinyl' => 'nullable|array',
            'vinyl.*' => 'nullable|string',
            'condition' => 'nullable|string|max:100'
        ];
    }
}
