<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SerieListRequest extends FormRequest
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
            'seriesList.*.id_serie' => ['required', 'integer'],
            'seriesList.*.name' => ['required', 'string'],
            'seriesList.*.synopsis' => ['required', 'string'],
            'seriesList.*.url_img' => ['required', 'string'],
            'seriesList.*.genres.*.id_genre' => ['required', 'integer'],
            'seriesList.*.genres.*.name' => ['required', 'string'],
        ];
    }
}
