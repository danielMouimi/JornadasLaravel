<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PonenteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'experiencia' => 'nullable|string',
            'redes_sociales' => 'nullable|string',
            'foto' => 'nullable|image|max:2048'
        ];

        return $rules;
    }

}

