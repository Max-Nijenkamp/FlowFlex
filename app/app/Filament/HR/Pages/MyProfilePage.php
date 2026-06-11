<?php

declare(strict_types=1);

namespace App\Filament\HR\Pages;

use App\Actions\HR\UpdateOwnProfileAction;
use App\Contracts\BillingServiceInterface;
use App\Data\HR\UpdateOwnProfileData;
use App\Models\HR\Employee;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Own-profile edit (ui-strategy row #7). Own-data rule enforced by
 * UpdateOwnProfileAction — HR-only fields are not even rendered.
 *
 * @property-read Schema $form
 */
class MyProfilePage extends Page
{
    protected string $view = 'filament.hr.pages.my-profile';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static string|UnitEnum|null $navigationGroup = 'My HR';

    protected static ?string $title = 'My Profile';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.self-service.update-own')
            && app(BillingServiceInterface::class)->hasModule('hr.self-service')
            && self::ownEmployee() !== null;
    }

    public function mount(): void
    {
        $employee = self::ownEmployee();

        $this->form->fill([
            'phone' => $employee?->phone,
            'personal_email' => $employee?->personal_email,
            'emergency_contacts' => $employee?->emergencyContacts()->get()
                ->map(fn ($c) => $c->only(['name', 'relationship', 'phone', 'email']))
                ->all() ?? [],
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('phone')->tel(),
            TextInput::make('personal_email')->email(),
            Repeater::make('emergency_contacts')
                ->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('relationship')->required(),
                    TextInput::make('phone')->tel()->required(),
                    TextInput::make('email')->email(),
                ])
                ->maxItems(3)
                ->defaultItems(0)
                ->addActionLabel('Add emergency contact'),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Save profile')->submit('save'),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        UpdateOwnProfileAction::run(UpdateOwnProfileData::from($this->form->getState()));
        Notification::make()->success()->title('Profile updated')->send();
    }

    private static function ownEmployee(): ?Employee
    {
        return Employee::query()->where('user_id', Auth::guard('web')->id())->first();
    }
}
