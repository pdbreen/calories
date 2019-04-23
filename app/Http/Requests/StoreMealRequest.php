<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = auth()->user();
        return $user && ($user->is_admin || ($this->get('user_id') == $user->id));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'description' => 'required',
            'calories' => 'required|integer|min:1',
            'eaten_at' => 'required|date',
            'user_id' => 'required|exists:users,id'
        ];
    }
}
