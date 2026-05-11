<?php

declare(strict_types=1);

namespace App\Notifications\Concerns;

trait HasResolvedChannels
{
    private array $resolvedChannels = [];

    public function setChannels(array $channels): static
    {
        $this->resolvedChannels = $channels;

        return $this;
    }

    public function via(object $notifiable): array
    {
        return ! empty($this->resolvedChannels)
            ? $this->resolvedChannels
            : ['database'];
    }
}
