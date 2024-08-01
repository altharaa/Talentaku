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
            'unique_code' => $this->unique_code,
            'is_active' => $this->isactive ? 1 : 0,
            'is_active_status' => $this->isactive ? 'active' : 'inactive',
            'teacher' => new UserResource($this->teacher),
            'level' => $this->level->only(['id', 'name']),
            'members' => $this->members->map(function ($member) {
                return new UserResource($member);
            })
        ];
    }
}
