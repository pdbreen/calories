<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $role = auth()->user()->role;
        if ($role === User::ROLE_ADMIN) {
            $rules = [
                'role' => 'nullable|in:0,1,2',
            ];
        } else if ($role === User::ROLE_USER_MANAGER) {
            $rules = [
                'role' => 'nullable|in:0,1',
            ];
        }

        return array_merge([
            'name' => 'nullable|min:3',
            'email' => 'nullable|email|unique:users,'.$this->route('user')->id,
            'password' => 'nullable|min:6',
            'expected_calories' => 'nullable|integer|min:1',
        ], $rules ?? []);
    }
}
