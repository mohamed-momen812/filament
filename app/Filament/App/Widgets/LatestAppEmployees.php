<?php

namespace App\Filament\App\Widgets;

use App\Models\Employee;
use Filament\Facades\Filament;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestAppEmployees extends BaseWidget
{
    protected static ?int $sort = 3;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Employee::query()->whereBelongsTo(Filament::getTenant())
                    ->latest()
                    ->take(5)
            )
            ->columns([
                TextColumn::make('first_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
