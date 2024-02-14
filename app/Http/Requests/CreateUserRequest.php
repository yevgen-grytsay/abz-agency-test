<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

/**
 * @property UploadedFile|null $photo_raw
 */
class CreateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:60'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'regex:/^\+380[0-9]{9}$/', 'unique:users,phone'],
            'position_id' => [
                'required',
                Rule::exists('positions', 'id'),
            ],
            'photo_raw' => [
                'file',
                'required',
                'mimetypes:image/jpeg',
                File::image()
                    ->max('5mb')
                    ->dimensions(Rule::dimensions()->minWidth(70)->minHeight(70)),
            ],
        ];
    }
}
