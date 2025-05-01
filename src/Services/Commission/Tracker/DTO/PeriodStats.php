<?php

namespace AlgoYounes\CommissionTask\Services\Commission\Tracker\DTO;

class PeriodStats
{
    public function __construct(
        private int $operationCount = 0,
        private float $totalWithdrawn = 0.0
    ) {
    }

    public static function make(): self
    {
        return new self();
    }

    public function addWithdrawal(float $amount): self
    {
        ++$this->operationCount;
        $this->totalWithdrawn += $amount;

        return $this;
    }

    public function getOperationCount(): int
    {
        return $this->operationCount;
    }

    public function getTotalWithdrawn(): float
    {
        return $this->totalWithdrawn;
    }
}
