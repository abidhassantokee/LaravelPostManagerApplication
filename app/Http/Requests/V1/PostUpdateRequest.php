<?php

namespace App\Http\Requests\V1;

use App\Models\V1\Post;
use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $postToBeEdited = Post::where('id', request()->instance()->id)
            ->where('author_id', auth()->user()->id)
            ->first();
        if ($postToBeEdited) {
            return true;
        }
        return false;
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
            'image_url' => [
                'nullable',
                'string'
            ],
            'upload_image' => [
                'bail',
                'nullable',
                'file',
                'max:5000',
                'mimes:png,jpg,jpeg,bmp'
            ],
            'video_url' => [
                'nullable',
                'string'
            ],
            'upload_video' => [
                'bail',
                'nullable',
                'file',
                'max:10000',
                'mimes:video/x-flv,video/mp4'
            ],
            'attachment_urls' => [
                'array',
                'nullable'
            ],
            'attachment_urls.*' => [
                'string'
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
