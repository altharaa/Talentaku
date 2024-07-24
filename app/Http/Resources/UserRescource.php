<?php

namespace App\Http\Resources;

use App\Models\Grade;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRescource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $roles = $this->roles()->pluck('name')->toArray();
        $grades = $this->getGrades($roles);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'identification_number' => $this->identification_number,
            'address' => $this->address,
            'photo' => $this->photo,
            'birth_information' => $this->getBirthInformation(),
            'roles' => $roles,
            'grades' => $grades ?: 'User didn\'t have any class',
        ];
    }

    private function getBirthInformation()
    {
        // Use Carbon to parse the date string
        $birthDate = $this->birth_date ? Carbon::parse($this->birth_date)->format('Y-m-d') : null;
        return $this->place_of_birth . ($birthDate ? ', ' . $birthDate : '');
    }

    private function getGrades($roles)
    {
        if (in_array('Murid SD', $roles) || in_array('Murid KB', $roles)) {
            return $this->members()->with('grade')->get()->pluck('grade.name')->toArray();
        } elseif (in_array('Guru SD', $roles) || in_array('Guru KB', $roles)) {
            return Grade::where('teacher_id', $this->id)->pluck('name')->toArray();
        }
        return [];
    }
}
