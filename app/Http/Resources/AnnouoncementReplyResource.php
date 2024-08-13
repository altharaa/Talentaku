<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouoncementReplyResource extends JsonResource
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
            'announcement_id' => $this->announce_id,
            'user' => [
                'id' => optional($this->user)->id,
                'name' => optional($this->user)->name,
                'photo' => optional($this->user)->photo,
            ],
            'replies' => explode(PHP_EOL, $this->replies),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
