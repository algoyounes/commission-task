<?php

namespace AlgoYounes\CommissionTask\Tests\Unit\Service\Commission\Rules;

use AlgoYounes\CommissionTask\Enums\OperationType;
use AlgoYounes\CommissionTask\Enums\UserType;
use AlgoYounes\CommissionTask\Services\Commission\Rules\DepositCommissionRule;
use AlgoYounes\CommissionTask\Tests\TestCase;

class DepositCommissionRuleTest extends TestCase
{
    private DepositCommissionRule $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new DepositCommissionRule();
    }

    public function test_is_supported_for_deposit(): void
    {
        $operation = $this->createOperation(
            '2024-01-01',
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            '100.00',
            'EUR'
        );

        $this->assertTrue($this->rule->isSupported($operation));
        $this->assertMoneyEquals('0.0300000000', $this->rule->calculate($operation));
    }

    public function test_is_not_supported_for_withdraw(): void
    {
        $operation = $this->createOperation(
            '2024-01-01',
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            '100.00',
            'EUR'
        );

        $this->assertFalse($this->rule->isSupported($operation));
    }
}
