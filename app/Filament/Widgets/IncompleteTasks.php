<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class IncompleteTasks extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(Task::where('completed_at', null))
            ->emptyStateHeading('No incomplete tasks')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->columns([
                TextColumn::make('task')->searchable(),
                TextColumn::make('project.project')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('project_id')
                    ->options(fn () => Project::all()->pluck('project', 'id'))
            ])
            ->headerActions([
                
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => route('filament.admin.resources.tasks.edit', [
                        'record' => $record->id,
                    ]))
                    ->openUrlInNewTab(false), 
                ])
            ->toolbarActions([
                Action::make('Add task')
                    ->label('Add task')
                    ->icon('heroicon-o-plus')
                    ->url(route('filament.admin.resources.tasks.create'))
                    ->openUrlInNewTab(false),
            ]);
    }

    public function getTableHeading(): string
    {
        return 'Tasks';
    }
}
