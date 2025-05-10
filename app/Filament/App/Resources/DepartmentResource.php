<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\DepartmentResource\Pages;
use App\Models\Department;
use Filament\Facades\Filament;
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

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap'; // Optional: Icon for the navigation item
    protected static ?string $navigationGroup = 'System Managment'; // Optional: Grouping in the navigation
    protected static ?string $navigationLabel = 'Department'; // Optional: Label for the navigation item
    protected static ?string $modelLabel = 'Department'; // Optional: Tooltip for the navigation badge

    protected static ?int $navigationSort = 1; // Optional: Sort order in the navigation
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->searchable(),
                TextColumn::make('employees_count')
                    ->counts('employees'),
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

    // This method is used to define the infolist for the resource // This is where you can customize the information displayed in the infolist // if not make this default will appear as coming from create form
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Department Details')
                    ->description('Department information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Department Name'),
                        TextEntry::make('employees_count')
                            ->state(fn($record):int => $record->employees()->count()) // Assuming you have a relationship named 'employees' in your Department model
                            ->label('Number of Employees'),
                    ])
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
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'view' => Pages\ViewDepartment::route('/{record}'), // Optional: View action for each row if committed the cart only show in the view page
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string // Optional: Display a badge with the count of records in the navigation item
    {
        return static::getModel()::where('team_id', Filament::getTenant()->id)->count(); // getModel() is a static method that returns the model class name
    }

    public static function getNavigationBadgeColor(): ?string // Optional: Set the color of the badge
    {
        return static::getModel()::count() <= 10 ? 'primary' : 'warn'; // You can use any color from Tailwind CSS
    }
}
