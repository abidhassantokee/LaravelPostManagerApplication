<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommentStoreRequest extends FormRequest
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
            'comment' => [
                'required',
                'string',
                'max:255'
            ],
            'post_id' => [
                'bail',
                'required',
                'integer',
                'min:1',
                Rule::exists('posts', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                })
            ]
        ];
    }
}
