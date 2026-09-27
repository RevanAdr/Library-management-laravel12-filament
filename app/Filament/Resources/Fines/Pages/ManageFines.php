<?php

namespace App\Filament\Resources\Fines\Pages;

use App\Filament\Resources\Fines\FineResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageFines extends ManageRecords
{
    protected static string $resource = FineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
