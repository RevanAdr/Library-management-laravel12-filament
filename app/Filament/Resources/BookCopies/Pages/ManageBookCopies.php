<?php

namespace App\Filament\Resources\BookCopies\Pages;

use App\Filament\Resources\BookCopies\BookCopyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBookCopies extends ManageRecords
{
    protected static string $resource = BookCopyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
