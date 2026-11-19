<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'caption' => ['required', 'string', 'max:255'],
            'photo'   => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'caption.required' => 'Please write a caption for the photo.',
            'photo.required'   => 'Please choose an image to upload.',
            'photo.image'      => 'The file must be a real image, not just a file with an image name.',
            'photo.mimes'      => 'Only JPG, PNG, GIF, or WEBP images are allowed.',
            'photo.max'        => 'The image is too large. The maximum size is 2 MB.',
            'photo.uploaded'   => 'The image is too large or the upload failed. The maximum size is 2 MB.',
        ];
    }
}