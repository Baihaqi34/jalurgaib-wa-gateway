<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['sometimes', 'required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:25'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Nama perangkat tidak boleh kosong.',
            'name.string'         => 'Nama perangkat harus berupa teks.',
            'name.max'            => 'Nama perangkat maksimal 255 karakter.',
            'phone_number.string' => 'Nomor WhatsApp harus berupa teks.',
            'phone_number.max'    => 'Nomor WhatsApp maksimal 25 karakter.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal. Periksa kembali format data yang dikirim.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}

