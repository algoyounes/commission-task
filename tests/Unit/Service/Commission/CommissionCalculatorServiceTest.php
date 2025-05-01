<?php

namespace AlgoYounes\CommissionTask\Tests\Unit\Service\Commission;

use AlgoYounes\CommissionTask\Enums\OperationType;
use AlgoYounes\CommissionTask\Enums\UserType;
use AlgoYounes\CommissionTask\Services\Commission\CommissionCalculatorService;
use AlgoYounes\CommissionTask\Services\Commission\Rules\CommissionRulesRegistry;
use AlgoYounes\CommissionTask\Tests\TestCase;

class CommissionCalculatorServiceTest extends TestCase
{
    private CommissionCalculatorService $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $registry = new CommissionRulesRegistry();
        $this->calculator = new CommissionCalculatorService($registry);
    }

    public function test_calculate_commission_for_deposit(): void
    {
        bcscale(10);

        $operation = $this->createOperation(
            '2024-01-01',
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            '100.00',
            'EUR'
        );

        $commission = $this->calculator->calculateCommission($operation);

        $this->assertMoneyEquals('0.03', $commission);
    }

    public function test_calculate_commission_for_withdraw_business(): void
    {
        $operation = $this->createOperation(
            '2024-01-01',
            1,
            UserType::BUSINESS,
            OperationType::WITHDRAW,
            '100.00',
            'EUR'
        );

        $commission = $this->calculator->calculateCommission($operation);

        $this->assertMoneyEquals('0.50', $commission);
    }

    public function test_calculate_commission_for_withdraw_private(): void
    {
        $operation = $this->createOperation(
            '2024-01-01',
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            '2000.00',
            'EUR'
        );

        $commission = $this->calculator->calculateCommission($operation);

        $this->assertMoneyEquals('3.00', $commission);
    }
}
