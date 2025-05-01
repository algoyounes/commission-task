<?php

namespace AlgoYounes\CommissionTask\Tests\Unit\Service\Commission\Rules;

use AlgoYounes\CommissionTask\Services\Commission\Rules\WithdrawPrivateCommissionRule;
use AlgoYounes\CommissionTask\Tests\TestCase;

class WithdrawPrivateCommissionRuleTest extends TestCase
{
    private WithdrawPrivateCommissionRule $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new WithdrawPrivateCommissionRule();
    }

    public function test_is_supported_for_private_withdraw(): void
    {
        $operation = $this->createPrivateWithdrawOperation();

        $this->assertTrue($this->rule->isSupported($operation));
    }

    public function test_is_not_supported_for_business_withdraw(): void
    {
        $operation = $this->createBusinessWithdrawOperation();

        $this->assertFalse($this->rule->isSupported($operation));
    }

    public function test_is_not_supported_for_deposit(): void
    {
        $operation = $this->createDepositOperation();

        $this->assertFalse($this->rule->isSupported($operation));
    }

    public function test_calculate_commission_for_single_operation(): void
    {
        $operation = $this->createPrivateWithdrawOperation('1100.00');
        $commission = $this->rule->calculate($operation);

        $this->assertMoneyEquals('0.30', $commission);
    }

    public function test_calculate_commission_for_very_large_amount(): void
    {
        $operation = $this->createPrivateWithdrawOperation('1000000.00');
        $commission = $this->rule->calculate($operation);

        $this->assertMoneyEquals('2997.00', $commission);
    }
}
