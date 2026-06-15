<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ContactStatus: string implements HasLabel, HasColor, HasIcon
{
    case Lead      = 'lead';
    case Prospect  = 'prospect';
    case Customer  = 'customer';
    case Inactive  = 'inactive';

    public function getLabel(): string
    {
        return match($this) {
            self::Lead     => 'Lead',
            self::Prospect => 'Prospect',
            self::Customer => 'Customer',
            self::Inactive => 'Inactive',
        };
    }

    public function getColor(): string|array|null
    {
        return match($this) {
            self::Lead     => 'info',    // blue
            self::Prospect => 'warning', // yellow
            self::Customer => 'success', // green
            self::Inactive => 'gray',    // gray
        };
    }

    public function getIcon(): ?string
    {
        return match($this) {
            self::Lead     => 'heroicon-m-star',
            self::Prospect => 'heroicon-m-eye',
            self::Customer => 'heroicon-m-check-circle',
            self::Inactive => 'heroicon-m-x-circle',
        };
    }
}