<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\LeavePolicyResource\Pages\CreateLeavePolicy;
use App\Filament\Hr\Resources\LeavePolicyResource\Pages\EditLeavePolicy;
use App\Filament\Hr\Resources\LeavePolicyResource\Pages\ListLeavePolicies;
use App\Models\HR\LeavePolicy;
use App\Services\Core\BillingService;
use App\Support\Services\CompanyContext;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeavePolicyResource extends Resource
{
    protected static ?string $model = LeavePolicy::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Leave';
    }

    public static function getNavigationLabel(): string
    {
        return 'Leave Policies';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
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

        return app(BillingService::class)
            ->enforceModuleAccess($ctx->current(), 'hr.leave');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Policy Details')->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                Select::make('leave_type')
                    ->options([
                        'annual'    => 'Annual',
                        'sick'      => 'Sick',
                        'maternity' => 'Maternity',
                        'paternity' => 'Paternity',
                        'unpaid'    => 'Unpaid',
                        'other'     => 'Other',
                    ])
                    ->required(),
                TextInput::make('days_per_year')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(365),
                TextInput::make('carry_over_days')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                TextInput::make('min_notice_days')
                    ->numeric()
                    ->default(1)
                    ->minValue(0),
                Toggle::make('is_paid')->default(true),
                Toggle::make('requires_approval')->default(true),
                Toggle::make('is_active')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('leave_type')->badge()->sortable(),
                TextColumn::make('days_per_year')->label('Days/Year')->sortable(),
                TextColumn::make('carry_over_days')->label('Carry Over'),
                IconColumn::make('is_paid')->boolean()->label('Paid'),
                IconColumn::make('requires_approval')->boolean()->label('Approval Required'),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLeavePolicies::route('/'),
            'create' => CreateLeavePolicy::route('/create'),
            'edit'   => EditLeavePolicy::route('/{record}/edit'),
        ];
    }
}
