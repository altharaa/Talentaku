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
            'kegiatan_awal_dihalaman' => explode(PHP_EOL, $this->kegiatan_awal_dihalaman),
            'dihalaman_hasil' => $this->dihalaman_hasil,
            'kegiatan_awal_berdoa' => explode(PHP_EOL, $this->kegiatan_awal_berdoa),
            'berdoa_hasil' => $this->berdoa_hasil,
            'kegiatan_inti_satu' => explode(PHP_EOL, $this->kegiatan_inti_satu),
            'inti_satu_hasil' => $this->inti_satu_hasil,
            'kegiatan_inti_dua' => explode(PHP_EOL, $this->kegiatan_inti_dua),
            'inti_dua_hasil' => $this->inti_dua_hasil,
            'kegiatan_inti_tiga' => explode(PHP_EOL, $this->kegiatan_inti_tiga),
            'inti_tiga_hasil' => $this->inti_tiga_hasil,
            'snack' => explode(PHP_EOL, $this->snack),
            'inklusi' => explode(PHP_EOL, $this->inklusi),
            'inklusi_hasil' => $this->inklusi_hasil,
            'inklusi_penutup' =>explode(PHP_EOL,  $this->inklusi_penutup),
            'inklusi_penutup_hasil' => $this->inklusi_penutup_hasil,
            'inklusi_doa' => explode(PHP_EOL, $this->inklusi_doa),
            'inklusi_doa_hasil' => $this->inklusi_doa_hasil,
            'catatan' => explode(PHP_EOL, $this->catatan),
            'student_id' => $this->student_id,
            'teacher_id' => $this->teacher_id,
            'grade_id' => $this->grade_id,
            'media' => StudentReportMediaResource::collection($this->media),
        ];
    }
}
