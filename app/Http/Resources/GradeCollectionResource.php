<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->map(function ($grade) {
                return [
                    'id' => $grade->id,
                    'name' => $grade->name,
                    'desc' => $grade->desc,
                    'isactive' => $grade->isactive,
                    'teacher' => $grade->teacher ? [
                        'id' => $grade->teacher->id,
                        'name' => $grade->teacher->name,
                    ] : null,
                    'members' => $grade->members->map(function ($member) {
                        return new UserResource($member);
                    }),
                ];
            }),
        ];
    }
}
