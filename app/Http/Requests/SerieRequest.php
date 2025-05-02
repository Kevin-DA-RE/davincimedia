<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SerieRequest extends FormRequest
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
           'id_serie' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'synopsis' => ['required', 'string'],
            'url_img' => ['required', 'string'],
            'genres.*.id_genre' => ['required', 'integer'],
            'genres.*.name' => ['required', 'string'],
        ];
    }
}
