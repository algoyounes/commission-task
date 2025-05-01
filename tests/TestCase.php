<?php

namespace AlgoYounes\CommissionTask\Tests;

use AlgoYounes\CommissionTask\Entity\Operation;
use AlgoYounes\CommissionTask\Enums\OperationType;
use AlgoYounes\CommissionTask\Enums\UserType;
use AlgoYounes\CommissionTask\ValueObjects\Money;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private int $currentBcScale;
    private const DEFAULT_PRECISION = 10;

    protected function setUp(): void
    {
        parent::setUp();

        $this->saveBcScale();
        $this->setDefaultPrecision();
    }

    protected function tearDown(): void
    {
        $this->revertBcScale();

        parent::tearDown();
    }

    #[Before]
    protected function saveBcScale(): void
    {
        $this->currentBcScale = bcscale();
    }

    #[After]
    protected function revertBcScale(): void
    {
        bcscale($this->currentBcScale);
    }

    protected function setDefaultPrecision(): void
    {
        bcscale(self::DEFAULT_PRECISION);
    }

    protected function assertMoneyEquals(string $expected, Money $actual, string $message = ''): void
    {
        $this->assertEquals($expected, $actual->getAmount(), $message);
    }

    protected function createOperation(
        string $date,
        int $userId,
        UserType $userType,
        OperationType $operationType,
        string $amount,
        string $currency
    ): Operation {
        return Operation::fromArray([
            $date, $userId, $userType->value, $operationType->value, $amount, $currency
        ]);
    }

    protected function createFakeOperation(): Operation
    {
        return $this->createOperation(
            '2024-01-01',
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            '100.00',
            'EUR'
        );
    }

    protected function createPrivateWithdrawOperation(string $amount = '100.00'): Operation
    {
        return $this->createOperation(
            '2024-01-01',
            1,
            UserType::PRIVATE,
            OperationType::WITHDRAW,
            $amount,
            'EUR'
        );
    }

    protected function createBusinessWithdrawOperation(string $amount = '100.00'): Operation
    {
        return $this->createOperation(
            '2024-01-01',
            1,
            UserType::BUSINESS,
            OperationType::WITHDRAW,
            $amount,
            'EUR'
        );
    }

    protected function createDepositOperation(string $amount = '100.00'): Operation
    {
        return $this->createOperation(
            '2024-01-01',
            1,
            UserType::PRIVATE,
            OperationType::DEPOSIT,
            $amount,
            'EUR'
        );
    }
}
