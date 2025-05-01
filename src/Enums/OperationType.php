<?php

namespace AlgoYounes\CommissionTask\Enums;

enum OperationType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';

    public function isDeposit(): bool
    {
        return $this === self::DEPOSIT;
    }

    public function isWithdrawl(): bool
    {
        return $this === self::WITHDRAW;
    }
}
