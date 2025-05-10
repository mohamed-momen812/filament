<?php

namespace App\Filament\App\Resources\EmployeeResource\Pages;

use App\Filament\App\Resources\EmployeeResource;
use App\Models\Employee;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make(), // Default tab showing all employees
            'This Week' => Tab::make() // Tab for employees hired in the last week
                ->modifyQueryUsing(fn(Builder $query) => $query->where('date_of_hireds', '>=', now()->subWeek())) // Filter employees hired in the last week
                ->badge(Employee::where('date_of_hireds', '>=', now()->subWeek())->count()), // Count employees hired in the last week in the badge
                'This Month' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('date_of_hireds', '>=', now()->subMonth())) // Filter employees hired in the last month
                ->badge(Employee::where('date_of_hireds', '>=', now()->subMonth())->count()), // Count employees hired in the last month in the badge
                'This Year' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('date_of_hireds', '>=', now()->subYear())) // Filter employees hired in the last year
                ->badge(Employee::where('date_of_hireds', '>=', now()->subYear())->count()), // Count employees hired in the last year in the badge
        ];
    }
}
