<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTodoRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom attributes for validator errors (Vietnamese).
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'tiêu đề',
        ];
    }

    /**
     * Get the error messages for the defined validation rules (Vietnamese).
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập :attribute.',
            'title.string' => ':attribute phải là chuỗi ký tự.',
            'title.max' => ':attribute không được vượt quá :max ký tự.',
        ];
    }
}
