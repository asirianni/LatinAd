<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisplayRequest extends FormRequest
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
        // Para updates usamos 'sometimes|required', para store solo 'required'
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $requiredRule = $isUpdate ? 'sometimes|required' : 'required';
        
        $rules = [
            'name' => $requiredRule . '|string|max:255',
            'description' => 'nullable|string',
            'price_per_day' => $requiredRule . '|numeric|min:0',
            'resolution_height' => $requiredRule . '|integer|min:1',
            'resolution_width' => $requiredRule . '|integer|min:1',
            'type' => $requiredRule . '|in:indoor,outdoor',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB max
        ];

        // Solo requerir user_id en updates, no en store (se asigna automáticamente)
        if ($isUpdate) {
            $rules['user_id'] = 'sometimes|required|exists:users,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del display es obligatorio.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'price_per_day.required' => 'El precio por día es obligatorio.',
            'price_per_day.numeric' => 'El precio debe ser un número.',
            'price_per_day.min' => 'El precio no puede ser negativo.',
            'resolution_height.required' => 'La altura de resolución es obligatoria.',
            'resolution_height.integer' => 'La altura debe ser un número entero.',
            'resolution_height.min' => 'La altura debe ser mayor a 0.',
            'resolution_width.required' => 'El ancho de resolución es obligatorio.',
            'resolution_width.integer' => 'El ancho debe ser un número entero.',
            'resolution_width.min' => 'El ancho debe ser mayor a 0.',
            'type.required' => 'El tipo de display es obligatorio.',
            'type.in' => 'El tipo debe ser "indoor" o "outdoor".',
            'user_id.required' => 'El ID del usuario es obligatorio.',
            'user_id.exists' => 'El usuario especificado no existe.',
            'photo.image' => 'El archivo debe ser una imagen válida.',
            'photo.mimes' => 'La imagen debe ser JPG, PNG o WEBP.',
            'photo.max' => 'La imagen no puede ser mayor a 5MB.',
        ];
    }
}
