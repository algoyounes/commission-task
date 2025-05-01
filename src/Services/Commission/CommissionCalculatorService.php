<?php

namespace AlgoYounes\CommissionTask\Services\Commission;

use AlgoYounes\CommissionTask\Entity\Operation;
use AlgoYounes\CommissionTask\Services\Commission\Rules\CommissionRulesRegistry;
use AlgoYounes\CommissionTask\ValueObjects\Money;

class CommissionCalculatorService
{
    public function __construct(private readonly CommissionRulesRegistry $commissionRulesRegistry)
    {
    }

    public function calculateCommission(Operation $operation): Money
    {
        $rule = $this->commissionRulesRegistry->getSupportedRule($operation);

        $precision = config('app.commission.currency_decimals')[$operation->getCurrency()->getCode()] ?? null;
        if ($precision === null) {
            throw new \InvalidArgumentException(
                sprintf('No decimal defined for currency %s', $operation->getCurrency()->getCode())
            );
        }

        return $rule->calculate($operation)->roundUp($precision);
    }
}
