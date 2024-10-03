<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadMovieRequest extends FormRequest
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
            'moviesList.*.file' => ['required', 'file'],
            'moviesList.*.name' => ['required', 'string'],
            'moviesList.*.synopsis' => ['required', 'string'],
            'moviesList.*.url_img' => ['required', 'string'],
            'moviesList.*.genre_id' => ['required', 'array'],
                'moviesList.*.genre_id.*.id' => ['required', 'integer'],
            'moviesList.*.genre_name' => ['required', 'array'],
                'moviesList.*.genre_name.*.name' => ['required', 'string'],

        ];
    }
}
