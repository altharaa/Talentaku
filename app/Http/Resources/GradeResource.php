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
            'is_active' => $this->isactive ? true : false,
            'teacher' => $this->teacher->only(['id', 'fullname']),
            'level' => $this->level->only(['id', 'name']),
            'members' =>$this->members->isNotEmpty() 
            ? $this->members->map(function ($member) {
                return [
                    'id' => $member->id,
                    'fullname' => $member->fullname,
                ];
            }) 
            : 'This grade does not have any members yet.',
        ];
    }
}
