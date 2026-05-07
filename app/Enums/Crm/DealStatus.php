<?php

namespace App\Enums\Crm;

enum DealStatus: string
{
    case Open   = 'open';
    case Won    = 'won';
    case Lost   = 'lost';
    case OnHold = 'on_hold';

    public function label(): string
    {
        return match($this) {
            self::Open   => 'Open',
            self::Won    => 'Won',
            self::Lost   => 'Lost',
            self::OnHold => 'On Hold',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Open   => 'info',
            self::Won    => 'success',
            self::Lost   => 'danger',
            self::OnHold => 'warning',
        };
    }
}
