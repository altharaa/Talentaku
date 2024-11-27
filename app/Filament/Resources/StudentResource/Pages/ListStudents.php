<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url(fn($record) => StudentResource::getUrl('create', [
                    'role' => request('role'),
                ])),
        ];
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $role = request()->query('role');

        return $role;
    }
}
