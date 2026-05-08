<?php

namespace App\Enums;

/**
 * États possibles pour une campagne
 */
enum CampaignStatus: string
{
    case INACTIVE = 'inactive';
    case ACTIVE = 'active';
    case COMPLETED = 'terminee';

    public function label(): string
    {
        return match($this) {
            self::INACTIVE => 'Inactive',
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Terminée',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::INACTIVE => 'warn',
            self::ACTIVE => 'success',
            self::COMPLETED => 'secondary',
        };
    }
}
