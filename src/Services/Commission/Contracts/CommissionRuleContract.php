<?php

namespace AlgoYounes\CommissionTask\Services\Commission\Contracts;

use AlgoYounes\CommissionTask\Entity\Operation;
use AlgoYounes\CommissionTask\ValueObjects\Money;

interface CommissionRuleContract
{
    public function isSupported(Operation $operation): bool;
    public function calculate(Operation $operation): Money;
}
