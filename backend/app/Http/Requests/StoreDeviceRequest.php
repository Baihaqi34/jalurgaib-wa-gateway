<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:25'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'Nama atau label perangkat wajib diisi.',
            'name.string'           => 'Nama perangkat harus berupa teks.',
            'name.max'              => 'Nama perangkat maksimal 255 karakter.',
            'phone_number.required' => 'Nomor WhatsApp wajib diisi.',
            'phone_number.string'   => 'Nomor WhatsApp harus berupa teks valid.',
            'phone_number.max'      => 'Nomor WhatsApp maksimal 25 karakter.',
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

