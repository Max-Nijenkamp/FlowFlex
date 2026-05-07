<?php

namespace App\Filament\Workspace\Pages\Settings;

use App\Enums\Currency;
use App\Enums\Language;
use App\Models\Company;
use App\Services\FileStorageService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class ManageCompany extends Page
{

    protected static ?string $navigationLabel = 'Company Settings';

    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.workspace.pages.settings.manage-company';

    public ?array $data = [];

    public function mount(): void
    {
        $this->authorizeAccess();

        $company = $this->getCompany();

        $this->form->fill([
            'name'     => $company->name,
            'email'    => $company->email,
            'phone'    => $company->phone,
            'website'  => $company->website,
            'timezone' => $company->timezone,
            'locale'   => $company->locale?->value,
            'currency' => $company->currency?->value,
        ]);
    }

    protected function authorizeAccess(): void
    {
        abort_unless(
            auth('tenant')->user()?->can('workspace.settings.view'),
            403
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Company Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Company name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Phone number')
                            ->tel()
                            ->maxLength(50),

                        TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->placeholder('https://')
                            ->maxLength(255),
                    ]),

                Section::make('Localisation')
                    ->columns(3)
                    ->schema([
                        Select::make('timezone')
                            ->label('Timezone')
                            ->options(
                                collect(timezone_identifiers_list())
                                    ->mapWithKeys(fn (string $tz) => [$tz => $tz])
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),

                        Select::make('locale')
                            ->label('Language')
                            ->options(
                                collect(Language::cases())
                                    ->mapWithKeys(fn (Language $l) => [$l->value => $l->flag() . ' ' . $l->nativeLabel()])
                                    ->toArray()
                            )
                            ->required(),

                        Select::make('currency')
                            ->label('Currency')
                            ->options(
                                collect(Currency::cases())
                                    ->mapWithKeys(fn (Currency $c) => [$c->value => "{$c->symbol()} {$c->label()} ({$c->value})"])
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Branding')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Company logo')
                            ->image()
                            ->imageResizeMode('contain')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('320')
                            ->imageResizeTargetHeight('180')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml', 'image/webp'])
                            ->maxSize(2048)
                            ->helperText('PNG, JPG, SVG or WebP. Max 2MB.')
                            ->disk(config('filesystems.default', 'local'))
                            ->directory(function () {
                                $slug = auth('tenant')->user()?->company?->slug ?? 'general';
                                return "companies/{$slug}/logos";
                            })
                            ->visibility('private'),
                    ]),
            ]);
    }

    public function save(): void
    {
        abort_unless(
            auth('tenant')->user()?->can('workspace.settings.edit'),
            403
        );

        $data = $this->form->getState();

        $company = $this->getCompany();

        $company->update([
            'name'     => $data['name'],
            'email'    => $data['email'] ?? $company->email,
            'phone'    => $data['phone'] ?? null,
            'website'  => $data['website'] ?? null,
            'timezone' => $data['timezone'],
            'locale'   => $data['locale'],
            'currency' => $data['currency'],
        ]);

        // Handle logo upload
        if (! empty($data['logo'])) {
            $company->update(['logo_file_id' => $data['logo']]);
        }

        Cache::forget("company:{$company->id}:settings");

        Notification::make()
            ->success()
            ->title('Company settings saved')
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save changes')
                ->submit('save'),
        ];
    }

    private function getCompany(): Company
    {
        return auth('tenant')->user()->company;
    }
}
