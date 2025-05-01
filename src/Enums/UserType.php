<?php

namespace AlgoYounes\CommissionTask\Enums;

enum UserType: string
{
    case PRIVATE = 'private';
    case BUSINESS = 'business';

    public function isPrivate(): bool
    {
        return $this === self::PRIVATE;
    }

    public function isBusiness(): bool
    {
        return $this === self::BUSINESS;
    }
}
