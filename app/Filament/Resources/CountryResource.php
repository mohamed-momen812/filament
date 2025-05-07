<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Models\Country;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


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
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
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
}
