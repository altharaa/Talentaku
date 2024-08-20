<?php

namespace App\Http\Resources;

use App\Models\Grade;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'status' => $this->status,
            'username' => $this->username,
            'name' => $this->name,
            'nomor_induk' => $this->nomor_induk,
            'address' => $this->address,
            'photo' => $this->photo,
            'birth_information' => $this->getBirthInformation(),
            'roles' => $roles,
            'grades' => $grades ?: 'User didn\'t have any class',
        ];
    }

    private function getBirthInformation()
    {
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
