<?php

namespace App\Policies\Crm;

use App\Models\Crm\ChatbotRule;
use App\Models\Tenant;

class ChatbotRulePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('crm.chatbot-rules.view');
    }

    public function view(Tenant $tenant, ChatbotRule $chatbotRule): bool
    {
        return $tenant->company_id === $chatbotRule->company_id
            && $tenant->can('crm.chatbot-rules.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('crm.chatbot-rules.create');
    }

    public function update(Tenant $tenant, ChatbotRule $chatbotRule): bool
    {
        return $tenant->company_id === $chatbotRule->company_id
            && $tenant->can('crm.chatbot-rules.edit');
    }

    public function delete(Tenant $tenant, ChatbotRule $chatbotRule): bool
    {
        return $tenant->company_id === $chatbotRule->company_id
            && $tenant->can('crm.chatbot-rules.delete');
    }

    public function restore(Tenant $tenant, ChatbotRule $chatbotRule): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, ChatbotRule $chatbotRule): bool
    {
        return false;
    }
}
