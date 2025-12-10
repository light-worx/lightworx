<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\Project;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('task')
                    ->required(),
                Select::make('project_id')
                    ->relationship('project', 'id')
                    ->options(Project::query()->pluck('project', 'id'))
                    ->required(),
                Select::make('status')
                    ->selectablePlaceholder(false)
                    ->options([
                        'todo' => 'To do',
                        'underway' => 'Underway',
                        'done' => 'Done',
                    ])
                    ->default('todo')
                    ->required(),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
