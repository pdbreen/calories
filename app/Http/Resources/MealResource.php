<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'description' => $this->description,
            'calories' => $this->calories,
            'total_calories' => $this->total_calories,
            'expected_calories' => $this->expected_calories,
            'eaten_at' => $this->eaten_at->format('Y-m-d\TH:i:s\.v\Z'),
            'eaten_at_day' => $this->eaten_at->format('M j, Y'),
            'eaten_at_time' => $this->eaten_at->format('g:ia'),
            'is_breakfast' => $this->is_breakfast,
            'is_lunch' => $this->is_lunch,
            'is_dinner' => $this->is_dinner,
            'is_snack' => $this->is_snack,
        ];
    }
}
