<?php

use App\Models\Crm\ChatbotRule;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'crm', 'crm');
    givePermissions($this->tenant, [
        'crm.chatbot-rules.view',
        'crm.chatbot-rules.create',
        'crm.chatbot-rules.edit',
        'crm.chatbot-rules.delete',
    ]);

    $this->rule = ChatbotRule::withoutGlobalScopes()->create([
        'company_id'       => $this->company->id,
        'name'             => 'Greeting Rule',
        'trigger_keywords' => ['hello', 'hi'],
        'response_body'    => 'Hello! How can I help you today?',
        'is_active'        => true,
        'sort_order'       => 1,
    ]);
});

it('authenticated tenant with permission can list chatbot rules', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/crm/chatbot-rules')
        ->assertOk();
});

it('unauthenticated request redirects from chatbot rules list', function () {
    $this->get('/crm/chatbot-rules')->assertRedirect();
});

it('tenant without permission gets 403 on chatbot rules list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/crm/chatbot-rules')
        ->assertForbidden();
});

it('can create a chatbot rule via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Crm\Resources\ChatbotRuleResource\Pages\CreateChatbotRule::class)
        ->fillForm([
            'name'             => 'Hours Rule',
            'trigger_keywords' => 'hours, open',
            'response_body'    => 'We are open Mon-Fri 9am-5pm.',
            'is_active'        => true,
            'sort_order'       => 2,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(ChatbotRule::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('name', 'Hours Rule')
        ->exists()
    )->toBeTrue();
});

it('can update a chatbot rule via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Crm\Resources\ChatbotRuleResource\Pages\EditChatbotRule::class,
            ['record' => $this->rule->getRouteKey()]
        )
        ->fillForm(['response_body' => 'Updated response body.'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->rule->fresh()->response_body)->toBe('Updated response body.');
});

it('chatbot rule trigger_keywords casts to array', function () {
    expect($this->rule->trigger_keywords)->toBe(['hello', 'hi']);
});

it('tenant from another company cannot see chatbot rules from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'crm.chatbot-rules.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(ChatbotRule::all()->pluck('id'))->not->toContain($this->rule->id);
});
