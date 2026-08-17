<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('user') instanceof \App\Models\User 
            ? $this->route('user')->id 
            : $this->route('user');

        return [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password'   => ['nullable', 'string', 'min:6'],
            'role'       => ['required', 'string', 'in:admin,user'],
            'package_id' => ['nullable', 'exists:packages,id'],
        ];
    }
}
