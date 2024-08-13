<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
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
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'photo' => $this->user->photo
            ] : null,
            'grade' => $this->grade ? [
                'id' => $this->grade->id,
                'name' => $this->grade->name,
            ] : null,
            'announcements' => $this->announcements ? explode(PHP_EOL, $this->announcements) : [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'media' => $this->media ? AnnouncementMediaResource::collection($this->media) : [],
            'replies' => $this->reply ? AnnouoncementReplyResource::collection($this->reply) : [],
        ];
    }
}
