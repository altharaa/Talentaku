<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (is_null($this->resource)) {
            return [];
        }
        
        return [
            'id' => $this->id,
            'desc' => explode(PHP_EOL, $this->desc),
            'grade_id' => $this->grade_id,
            'teacher_id' => $this->teacher_id,
            'date' => $this->date,
            'media' => collect($this->media)->map(function ($media) {
                return [
                    'id' => $media->id,
                    'file_name' => $media->file_name,
                ];
            }),
        ];
    }
}
