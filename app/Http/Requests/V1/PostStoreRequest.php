<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:255'
            ],
            'channel_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'upload_image' => [
                'bail',
                'nullable',
                'file',
                'max:5000',
                'mimes:png,jpg,jpeg,bmp'
            ],
            'upload_video' => [
                'bail',
                'nullable',
                'file',
                'max:10000',
                'mimes:video/x-flv,video/mp4'
            ],
            'upload_files' => [
                'array',
                'nullable',
                'max:5'
            ],
            'upload_files.*' => [
                'file',
                'max:5000',
                'mimes:application/pdf'
            ],
        ];
    }
}
