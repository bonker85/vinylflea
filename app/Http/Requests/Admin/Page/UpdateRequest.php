<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'name' => 'required|string',
            'url' => 'required|string|unique:pages,url,' . $this->id,
            'content' => 'required|string',
            'parent_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'nullable',
            'keywords' => 'nullable',
            'header' => 'required|string',
            'status' => 'in:0,1'
        ];
    }
}
