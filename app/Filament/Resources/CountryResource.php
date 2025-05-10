<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\CountryResource\RelationManagers\EmployeesRelationManager;
use App\Filament\Resources\CountryResource\RelationManagers\StateRelationManager;
use App\Filament\Resources\CountryResource\RelationManagers\StatesRelationManager;
use App\Models\Country;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Console\View\Components\Info;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt'; // Optional: Icon for the navigation item
    protected static ?string $navigationGroup = 'System Managment'; // Optional: Grouping in the navigation
    protected static ?string $navigationLabel = 'Country'; // Optional: Label for the navigation item
    protected static ?string $modelLabel = 'Employees Country'; // Optional: Tooltip for the navigation badge
    protected static ?int $navigationSort = 1; // Optional: Sort order in the navigation


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               TextInput::make('name')
                    ->required()
                    ->maxLength(255),
               TextInput::make('code')
                    ->required()
                    ->maxLength(3),
               TextInput::make('phone_code')
                    ->required()
                    ->numeric()
                    ->maxLength(5)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phonecode')
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
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infoList): InfoList
    {
        return $infoList
            ->schema([
                Section::make('Country Information')
                    ->description('Fill in the country information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Country Name'),
                        TextEntry::make('code')
                            ->label('Country Code'),
                        TextEntry::make('phonecode')
                            ->label('Phone Code'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StatesRelationManager::class,
            EmployeesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'view' => Pages\ViewCountry::route('/{record}'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
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