<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ActivityType: string implements HasLabel, HasColor, HasIcon
{
    case CalledCustomer  = 'called_customer';
    case SentQuotation   = 'sent_quotation';
    case FollowUpEmail   = 'follow_up_email';
    case MeetingComplete = 'meeting_completed';

    public function getLabel(): string
    {
        return match($this) {
            self::CalledCustomer  => 'Called Customer',
            self::SentQuotation   => 'Sent Quotation',
            self::FollowUpEmail   => 'Follow-up Email',
            self::MeetingComplete => 'Meeting Completed',
        };
    }

    public function getColor(): string|array|null
    {
        return match($this) {
            self::CalledCustomer  => 'info',    // blue
            self::SentQuotation   => 'warning', // yellow
            self::FollowUpEmail   => 'primary', // indigo
            self::MeetingComplete => 'success', // green
        };
    }

    public function getIcon(): ?string
    {
        return match($this) {
            self::CalledCustomer  => 'heroicon-o-phone',
            self::SentQuotation   => 'heroicon-o-document-text',
            self::FollowUpEmail   => 'heroicon-o-envelope',
            self::MeetingComplete => 'heroicon-o-check-badge',
        };
    }
}