<?php

namespace App\Filament\Resources\BookCopies;

use App\Filament\Resources\BookCopies\Pages\ManageBookCopies;
use App\Models\BookCopy;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class BookCopyResource extends Resource
{
    protected static ?string $model = BookCopy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocument;

    protected static string | UnitEnum | null $navigationGroup = 'Book Management';

    protected static ?string $recordTitleAttribute = 'barcode';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required(),
                TextInput::make('barcode')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('available'),
                TextInput::make('shelf_location'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('barcode')
            ->columns([
                TextColumn::make('book.title')
                    ->searchable(),
                TextColumn::make('barcode')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('shelf_location')
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
                DeleteAction::make()
                ->before(function ($record) {
                        if ($record->loans()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Cannot delete this book')
                                ->body('This book still has loans. Delete or retire the loans first.')
                                ->send();

                            throw new \Filament\Support\Exceptions\Halt;
                        }
                    }),
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
            'index' => ManageBookCopies::route('/'),
        ];
    }
}
