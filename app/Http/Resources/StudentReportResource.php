<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'created' => $this->created,
            'semester_id' => $this->semester_id,
            'semester_name' => optional($this->semester)->name,
            'kegiatan_awal' => explode(PHP_EOL, $this->kegiatan_awal),
            'awal_point' => $this->awal_point,
            'kegiatan_inti' => explode(PHP_EOL, $this->kegiatan_inti),
            'inti_point' => $this->inti_point,
            'snack' => explode(PHP_EOL, $this->snack),
            'snack_point' => $this->snack_point,
            'inklusi' => explode(PHP_EOL, $this->inklusi),
            'inklusi_point' => $this->inklusi_point,
            'catatan' => explode(PHP_EOL, $this->catatan),
            'student_id' => $this->student_id,
            'teacher_id' => $this->teacher_id,
            'grade_id' => $this->grade_id,
            'media' => StudentReportMediaResource::collection($this->media),
        ];
    }
}
