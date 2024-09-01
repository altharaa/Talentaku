<?php

namespace App\Filament\Resources\MuridSdResource\Pages;

use App\Filament\Resources\MuridSdResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMuridSd extends ListRecords
{
    protected static string $resource = MuridSdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
