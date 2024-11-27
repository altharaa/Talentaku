<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Role;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $roleName = request('role') ? '';
        Log::info($roleName);
        $role = Role::where('name', $roleName)->first();
        Log::info($role);
        $data['status'] = 'aktif';
       
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl;
    }
}
