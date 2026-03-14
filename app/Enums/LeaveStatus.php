<?php

namespace App\Enums;

enum LeaveStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Expired = 'expired';

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {

            self::Pending => in_array($status, [
                self::Active,
                self::Expired
            ], true),

            self::Active => in_array($status, [
                self::Expired
            ], true),

            self::Expired => false,
        };
    }
}