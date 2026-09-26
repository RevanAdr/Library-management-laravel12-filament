<?php

namespace App\Filament\Resources\Loans;

use App\Filament\Resources\Loans\Pages\ManageLoans;
use App\Models\Loan;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'borrowed_at';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('copy_id')
                    ->relationship('copy', 'copy_id')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                DateTimePicker::make('borrowed_date')
                    ->required(),
                DateTimePicker::make('due_date')
                    ->required(),
                DateTimePicker::make('returned_date'),
                TextInput::make('status')
                    ->required()
                    ->default('borrowed'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('borrowed_at')
            ->columns([
                TextColumn::make('copy.copy_id')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('borrowed_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('returned_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
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
                ViewAction::make(),
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
            'index' => ManageLoans::route('/'),
        ];
    }
}
