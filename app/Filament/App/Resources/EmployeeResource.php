<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\EmployeeResource\Pages;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group'; // Optional: Icon for the navigation item
    protected static ?string $navigationGroup = 'Employees Managment'; // Optional: Grouping in the navigation
    protected static ?string $activeNavigationIcon = 'heroicon-o-document-text'; // Optional: Icon for the active navigation item
    protected static ?int $navigationSort = 1; // Optional: Sort order in the navigation

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Department Information')
                    ->description('Fill in the department information of the employee')
                    ->schema([
                        Select::make('country_id')
                            ->relationship('country', 'name') // Assuming you have a Country model with a 'name' field
                            ->searchable() // Optional: Enable search
                            ->preload() // Optional: Preload the options
                            ->live() // Optional: Enable live search
                            ->afterStateUpdated(function (Set $set) {
                                $set('state_id', null);
                                $set('city_id', null);
                            }) // Reset state_id and city_id when country_id changes
                            ->required(), // Optional: Make it required
                        Select::make('state_id')
                            ->options(fn(Get $get): Collection => State::query()
                                ->where('country_id', $get('country_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Select::make('city_id')
                                ->options(fn(Get $get): Collection => City::query()
                                ->where('state_id', $get('state_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('department_id')
                            ->relationship(
                                'department',
                                'name',
                                fn (Builder $query) => $query->whereBelongsTo( Filament::getTenant()) )
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),
            Section::make('Personal Information')
                ->description('Fill in the personal information of the employee')
                ->schema([
                    TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('middle_name')
                        ->required()
                        ->maxLength(255),
                ])
                ->columns(3),
            Section::make('Address Information')
                ->description('Fill in the address information of the employee')
                ->schema([
                    TextInput::make('address')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('zip_code')
                        ->required()
                        ->maxLength(10),
                ])
                ->columns(2),
            Section::make('Date Information')
                ->description('Fill in the date information of the employee')
                ->schema([
                    DatePicker::make('date_of_birth')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->required(),
                    DatePicker::make('date_of_hireds')
                        ->required()
                ])
                ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('department.name')
                    ->sortable(),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('middle_name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('address')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('zip_code')
                    ->searchable(),
                TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date_of_hireds')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([ // Optional: Add filters to the table
                SelectFilter::make('Department')
                    ->relationship('department', 'name')
                    // ->multiple() // Optional: Allow multiple selections
                    ->placeholder('Select Department')
                    ->searchable()
                    ->preload()
                    ->indicator('Department') // Optional: Custom label for the filter which will appear in the filter list
                    ->label('filter by department'), // Optional: Custom label for the filter
                Filter::make('created_at') // Optional: Custom filter for created_at (from and until)
                    ->form([
                        DatePicker::make('created_from'), // Optional: Date picker for the start date
                        DatePicker::make('created_until'), // Optional: Date picker for the end date
                    ])
                    ->query(function (Builder $query, array $data): Builder { // Optional: Custom query for the filter depending on the selected dates
                        return $query
                            ->when( // كلمة when في Laravel (وفي Filament كذلك) هي ميثود تُستخدم داخل الكويري (Query Builder) علشان تضيف شرط بشكل ديناميكي إذا تحقق شرط معين.
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array { // Optional: Custom indicator for the filter
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('created_from ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('created_until ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
                        }

                        return $indicators;
                    })
                    ->columnSpan(2) // Optional: Column span for the filter form
                    ->columns(2), // Optional: Number of columns for the filter form
            ], FiltersLayout::Dropdown) // Optional: Layout for the filters
            ->filtersFormColumns(3) // Optional: Number of columns for the filters form
            ->actions([
                ViewAction::make(), // Optional: View action for each row
                EditAction::make(), // Optional: Edit action for each row
                DeleteAction::make(), // Optional: Delete action for each row
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(), // Optional: Bulk delete action when selecte
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make('Relationships Information')
                    ->description('employee Relationships information')
                    ->schema([
                        TextEntry::make('country.name')
                            ->label('Country Name'),
                        TextEntry::make('state.name')
                            ->label('State Name'),
                        TextEntry::make('city.name')
                            ->label('City Name'),
                        TextEntry::make('department.name')
                            ->label('Department Name'),
                    ])
                    ->columns(2),
                ComponentsSection::make('Personal Information')
                    ->description('employee Personal information')
                    ->schema([
                        TextEntry::make('first_name')
                            ->label('First Name'),
                        TextEntry::make('last_name')
                            ->label('Last Name'),
                        TextEntry::make('middle_name')
                            ->label('Middle Name'),
                    ])
                    ->columns(3),
                ComponentsSection::make('Address Information')
                    ->description('employee Address information')
                    ->schema([
                        TextEntry::make('address')
                            ->label('Address'),
                        TextEntry::make('zip_code')
                            ->label('Zip Code'),
                    ])
                    ->columns(2),
                ComponentsSection::make('Date Information')
                    ->description('employee Date information')
                    ->schema([
                        TextEntry::make('date_of_birth')
                            ->label('Date of Birth'),
                        TextEntry::make('date_of_hireds')
                            ->label('Date of Hireds'),
                    ])
                    ->columns(2)
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string // Optional: Display a badge with the count of records in the navigation item
    {
        return static::getModel()::where('team_id', Filament::getTenant()->id)->count(); // getModel() is a static method that returns the model class name
    }

        public static function getGlobalSearchResultTitle(Model $record): string // Optional: Title for the global search result
    {
        return "{$record->first_name} {$record->last_name} {$record->middle_name}";
    }

    public static function getGloballySearchableAttributes(): array // Optional: Attributes to be searchable in the global search
    {
        return [
            'first_name',
            'last_name',
            'middle_name',
            'address',
            'zip_code',
            'date_of_birth',
            'date_of_hireds',
        ];
    }


    public static function getGlobalSearchResultUrl(Model $record): string // Optional: URL for the global search result
    {
        return static::getUrl('view', ['record' => $record]);
    }


    public static function getGlobalSearchResultDetails(Model $record): array // Optional: Details for the global search result
    {
        return [
            'Department' => $record->department->name,
            'Country' => $record->country->name,
        ];
    }

    public static function getGloblaSearchElquentQuery(): Builder // Optional: Custom query for the global search for performance
    {
        return static::getModel()::query()
            ->with(['department', 'country']);
    }


    // public static function getNavigationBadgeColor(): ?string // Optional: Set the color of the badge
    // {
    //         return static::getModel()::count() > 10 ? 'warning' : 'primary';
    // }
}
