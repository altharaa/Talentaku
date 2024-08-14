<?php

namespace App\Http\Resources;

use App\Models\Announcement;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof Task) {
            return [
                'id' => $this->id,
                'type' => 'task',
                'preview' => $this->title,
                'grade' => $this->grade ? [
                    'id' => $this->grade->id,
                    'name' => $this->grade->name,
                ] : null,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        } elseif ($this->resource instanceof Announcement) {
            $announcementResource = new AnnouncementResource($this->resource);
            $data = $announcementResource->toArray($request);
            $data['type'] = 'announcement';
            $data['preview'] = explode(PHP_EOL,  $this->announcements ? substr($this->announcements, 0, 75) . '...' : '');
            return $data;
        }

        return [];
    }
}
