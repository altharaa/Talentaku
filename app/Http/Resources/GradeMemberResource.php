<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeMemberResource extends JsonResource
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
            'is_active_status' => $this->isactive ? 'active' : 'inactive',
            'teacher' => optional($this->teacher)->name,
            'level' => $this->level->only(['id', 'name']),
            'members' => $this->members->map(function ($member) {
                return new UserResource($member);
            })
        ];
    }
}
