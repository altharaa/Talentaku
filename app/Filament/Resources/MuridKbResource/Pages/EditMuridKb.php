<?php

namespace App\Filament\Resources\MuridKbResource\Pages;

use App\Filament\Resources\MuridKbResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMuridKb extends EditRecord
{
    protected static string $resource = MuridKbResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
