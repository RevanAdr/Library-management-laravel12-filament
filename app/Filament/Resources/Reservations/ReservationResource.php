<?php

namespace App\Filament\Resources\Reservations;

use App\Filament\Resources\Reservations\Pages\ManageReservations;
use App\Models\Reservation;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentCheck;

    protected static string | UnitEnum | null $navigationGroup = 'Loan Management';

    protected static ?string $recordTitleAttribute = 'reserved_at';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('book_id')
                    ->relationship('book', 'title')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),
                DateTimePicker::make('reservation_date')
                    ->default(now())
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reserved_at')
            ->columns([
                TextColumn::make('book.title')
                    ->label('book')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('member')
                    ->searchable(),
                TextColumn::make('reservation_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageReservations::route('/'),
        ];
    }
}
