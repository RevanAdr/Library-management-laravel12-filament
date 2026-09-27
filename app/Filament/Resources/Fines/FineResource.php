<?php

namespace App\Filament\Resources\Fines;

use App\Filament\Resources\Fines\Pages\ManageFines;
use App\Models\Fine;
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

class FineResource extends Resource
{
    protected static ?string $model = Fine::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Banknotes;

    protected static string | UnitEnum | null $navigationGroup = 'Loan Management';

    protected static ?string $recordTitleAttribute = 'amount';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('loan_id')
                    ->relationship('loan', 'loan_id')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->prefix('Rp')
                    ->numeric(),
                Select::make('status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                    ])
                    ->required(),
                DateTimePicker::make('paid_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                TextColumn::make('loan.loan_id')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => ManageFines::route('/'),
        ];
    }
}
