<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StateResource\RelationManagers\EmployeesRelationManager;
use App\Filament\Resources\StateResource\Pages;
use App\Filament\Resources\StateResource\RelationManagers\CitiesRelationManager;
use App\Filament\Resources\StateResource\RelationManagers\CountryRelationManager;
use App\Models\State;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StateResource extends Resource
{
    protected static ?string $model = State::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2'; // Optional: Icon for the navigation item
    protected static ?string $navigationGroup = 'System Managment'; // Optional: Grouping in the navigation
    protected static ?string $navigationLabel = 'States'; // Optional: Label for the navigation item
    protected static ?string $modelLabel = 'States'; // Optional: Tooltip for the navigation badge
    protected static ?int $navigationSort = 2; // Optional: Sort order in the navigation

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    // ->hidden(auth()->user()->email === 'momen@gmail.com') // Optional: Hide this column for a specific user
                    ->visible(auth()->user()->email === 'momen@gmail.com') // Optional: show this column for a specific user
                    ->label('State Name') // Optional: Custom label for the column
                    ->searchable() // Enable searching on this column
                    ->sortable(), // Optional: Enable searching and sorting
                TextColumn::make('country.name') // Assuming you have a relationship named 'country' in your State model
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('name') // Optional: Default sorting
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(), // Optional: View action for each row
                EditAction::make(), // Optional: Edit action for each row
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make('State Information')
                    ->description('Fill in the state information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('State Name'),
                        TextEntry::make('country.name')
                            ->label('Country Name')

                    ])
            ]);
    }

    public static function getRelations(): array // Optional: Define the relations that can be managed from this resource
    {
        return [
            CitiesRelationManager::class,
            EmployeesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStates::route('/'),
            'create' => Pages\CreateState::route('/create'),
            'view' => Pages\ViewState::route('/{record}'),
            'edit' => Pages\EditState::route('/{record}/edit'),
        ];
    }

    // public static function getNavigationBadge(): ?string // Optional: Display a badge with the count of records in the navigation item
    // {
    //     return static::getModel()::count(); // getModel() is a static method that returns the model class name
    // }

    // public static function getNavigationBadgeColor(): ?string // Optional: Set the color of the badge
    // {
    //     return 'success'; // You can use any color from Tailwind CSS
    // }
}