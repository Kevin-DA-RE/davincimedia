<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieRequest extends FormRequest
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
           'id_movie' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'synopsis' => ['required', 'string'],
            'url_img' => ['required', 'string'],
            'genre.*.id_genre' => ['required', 'integer'],
            'genre.*.name' => ['required', 'string'],
        ];
    }
}
