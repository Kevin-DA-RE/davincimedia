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
            'moviesList.*.id_movie' => ['required', 'integer'],
            'moviesList.*.name' => ['required', 'string'],
            'moviesList.*.synopsis' => ['required', 'string'],
            'moviesList.*.url_img' => ['required', 'string'],
            'moviesList.*.genre' => ['required', 'array'],
                'moviesList.*.genre.*.id_genre' => ['required', 'integer'],
                'moviesList.*.genre.*.name' => ['required', 'string'],
        ];
    }
}
