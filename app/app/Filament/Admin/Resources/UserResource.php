<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\Company;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Cross-company user directory — read-only support view (core.staff-console).
 * Admin requests carry no CompanyContext, so CompanyScope no-ops.
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Customers';

    public static function canAccess(): bool
    {
        return Auth::guard('admin')->check();
    }

    public static function canCreate(): bool
    {
        return false; // users join via invitations only
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->modifyQueryUsing(fn ($query) => $query->with('company')->latest('last_login_at'))
            ->columns([
                TextColumn::make('full_name')->label('Name')
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('email')->searchable(),
                TextColumn::make('company.name')->sortable(),
                IconColumn::make('email_verified_at')->label('Verified')
                    ->boolean()
                    ->state(fn (User $r): bool => $r->email_verified_at !== null),
                IconColumn::make('two_factor_enabled')->label('2FA')->boolean(),
                TextColumn::make('last_login_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('company_id')->label('Company')
                    ->options(fn () => Company::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                TernaryFilter::make('email_verified_at')->label('Verified')
                    ->nullable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
