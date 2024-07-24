<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'desc' => $this->desc,
            'code' => $this->unique_code,
            'is_active' => $this->isactive,
            'teacher' => new UserRescource($this->teacher),
            'level' => $this->level->only(['id', 'name']),
        ];
    }
}
