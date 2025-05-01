<?php

namespace AlgoYounes\CommissionTask\Services\Commission\Rules;

use AlgoYounes\CommissionTask\Entity\Operation;
use AlgoYounes\CommissionTask\Services\Commission\Contracts\CommissionRuleContract;
use AlgoYounes\CommissionTask\Support\Math;
use AlgoYounes\CommissionTask\ValueObjects\Money;

class WithdrawBusinessCommissionRule implements CommissionRuleContract
{
    private const COMMISSION_RATE = 0.5;

    public function isSupported(Operation $operation): bool
    {
        return $operation->isWithdrawal() && $operation->isBusinessUser();
    }

    public function calculate(Operation $operation): Money
    {
        return $operation->getAmount()->multiply(Math::divide($this->getCommissionRate(), 100));
    }

    private function getCommissionRate(): string
    {
        return config('app.commission.rules.withdraw.business.commission', self::COMMISSION_RATE);
    }
}
