<?php

namespace App\Http\Requests;

use App\Meal;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class MealIndexRequest extends FormRequest
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
        return [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
        ];
    }
}
