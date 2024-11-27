<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Daftar Guru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl;
    }
}
