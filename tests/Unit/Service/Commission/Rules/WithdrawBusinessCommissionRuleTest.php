<?php

namespace AlgoYounes\CommissionTask\Tests\Unit\Service\Commission\Rules;

use AlgoYounes\CommissionTask\Enums\OperationType;
use AlgoYounes\CommissionTask\Enums\UserType;
use AlgoYounes\CommissionTask\Services\Commission\Rules\WithdrawBusinessCommissionRule;
use AlgoYounes\CommissionTask\Tests\TestCase;

class WithdrawBusinessCommissionRuleTest extends TestCase
{
    private WithdrawBusinessCommissionRule $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new WithdrawBusinessCommissionRule();
    }

    public function test_is_supported_for_business_withdraw(): void
    {
        $operation = $this->createOperation(
            '2024-01-01',
            1,
            UserType::BUSINESS,
            OperationType::WITHDRAW,
            '100.00',
            'EUR'
        );

        $this->assertTrue($this->rule->isSupported($operation));
    }

    public function test_is_not_supported_for_private_withdraw(): void
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

    public function test_is_not_supported_for_deposit()
    {
        $operation = $this->createOperation(
            '2024-01-01',
            1,
            UserType::BUSINESS,
            OperationType::DEPOSIT,
            '100.00',
            'EUR'
        );

        $this->assertFalse($this->rule->isSupported($operation));
    }

    public function test_calculate_commission(): void
    {
        $operation = $this->createOperation(
            '2024-01-01',
            1,
            UserType::BUSINESS,
            OperationType::WITHDRAW,
            '100.00',
            'EUR'
        );

        $commission = $this->rule->calculate($operation);

        $this->assertMoneyEquals('0.5000000000', $commission);
    }

    public function test_calculate_commission_for_large_business_withdrawal(): void
    {
        $operation = $this->createOperation(
            '2024-01-01',
            1,
            UserType::BUSINESS,
            OperationType::WITHDRAW,
            '10000.00',
            'EUR'
        );

        $commission = $this->rule->calculate($operation);

        $this->assertMoneyEquals('50.0000000000', $commission);
    }

    public function test_calculate_commission_for_small_business_withdrawal(): void
    {
        $operation = $this->createOperation(
            '2024-01-01',
            1,
            UserType::BUSINESS,
            OperationType::WITHDRAW,
            '1.00',
            'EUR'
        );

        $commission = $this->rule->calculate($operation);

        $this->assertMoneyEquals('0.0050000000', $commission);
    }
}
