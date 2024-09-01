<?php

namespace App\Filament\Resources\MuridKbResource\Pages;

use App\Filament\Resources\MuridKbResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMuridKb extends ListRecords
{
    protected static string $resource = MuridKbResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
