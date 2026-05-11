<?php

declare(strict_types=1);

namespace App\Filament\Projects\Resources;

use App\Filament\Projects\Resources\DocumentResource\Pages\CreateDocument;
use App\Filament\Projects\Resources\DocumentResource\Pages\EditDocument;
use App\Filament\Projects\Resources\DocumentResource\Pages\ListDocuments;
use App\Models\Projects\Document;
use App\Models\Projects\Project;
use App\Models\Projects\Task;
use App\Support\Services\CompanyContext;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-document';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Projects';
    }

    public static function getNavigationLabel(): string
    {
        return 'Documents';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function canAccess(): bool
    {
        if (! auth()->check()) {
            return false;
        }
        $ctx = app(CompanyContext::class);
        if (! $ctx->hasCompany()) {
            return false;
        }

        return app(\App\Services\Core\BillingService::class)
            ->enforceModuleAccess($ctx->current(), 'projects.tasks');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Document Details')->columnSpanFull()->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('project_id')
                    ->label('Project')
                    ->options(fn () => Project::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Select::make('task_id')
                    ->label('Task')
                    ->options(fn () => Task::withoutGlobalScopes()
                        ->where('company_id', app(CompanyContext::class)->currentId())
                        ->pluck('title', 'id'))
                    ->searchable()
                    ->nullable(),
                FileUpload::make('file_path')
                    ->label('File')
                    ->disk('local')
                    ->directory('documents')
                    ->nullable(),
                Textarea::make('description')
                    ->nullable()
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('project.name')
                    ->label('Project')
                    ->placeholder('—'),
                TextColumn::make('task.title')
                    ->label('Task')
                    ->placeholder('—'),
                TextColumn::make('uploader.email')
                    ->label('Uploaded By'),
                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit'   => EditDocument::route('/{record}/edit'),
        ];
    }
}
