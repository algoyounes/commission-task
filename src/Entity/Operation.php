<?php

namespace AlgoYounes\CommissionTask\Entity;

use AlgoYounes\CommissionTask\Enums\UserType;
use AlgoYounes\CommissionTask\Enums\OperationType;
use AlgoYounes\CommissionTask\ValueObjects\Currency;
use AlgoYounes\CommissionTask\ValueObjects\DateImmutable;
use AlgoYounes\CommissionTask\ValueObjects\Money;

class Operation
{
    public function __construct(
        private readonly int $userId,
        private readonly OperationType $operationType,
        private readonly UserType $userType,
        private readonly Money $amount,
        private readonly DateImmutable $date,
    ) {
    }

    public static function fromArray(array $attributes): self
    {
        [$date, $userId, $userType, $operationType, $amount, $currency] = $attributes;

        return new self(
            userId: (int) $userId,
            operationType: OperationType::from($operationType),
            userType: UserType::from($userType),
            amount: Money::parse($amount, $currency),
            date: DateImmutable::fromString($date),
        );
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getOperationType(): OperationType
    {
        return $this->operationType;
    }

    public function getUserType(): UserType
    {
        return $this->userType;
    }

    public function isPrivateUser(): bool
    {
        return $this->userType->isPrivate();
    }

    public function isBusinessUser(): bool
    {
        return $this->userType->isBusiness();
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->amount->getCurrency();
    }

    public function getDate(): DateImmutable
    {
        return $this->date;
    }

    public function isDeposit(): bool
    {
        return $this->operationType->isDeposit();
    }

    public function isWithdrawal(): bool
    {
        return $this->operationType->isWithdrawl();
    }
}
