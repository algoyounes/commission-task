<?php

namespace AlgoYounes\CommissionTask\Services\Commission\Tracker;

use AlgoYounes\CommissionTask\Services\Commission\Tracker\DTO\PeriodStats;
use AlgoYounes\CommissionTask\ValueObjects\Money;
use DateTimeImmutable;

final class WithdrawalTrackerService
{
    /**
     * @var array<string,PeriodStats>
     */
    private array $buckets = [];

    public function getWeeklyOperationCount(int $userId, DateTimeImmutable $weekStart): int
    {
        return $this->bucket($userId, $weekStart)->getOperationCount();
    }

    public function getTotalWithdrawn(int $userId, DateTimeImmutable $weekStart): Money
    {
        $amount = $this->bucket($userId, $weekStart)->getTotalWithdrawn();

        return Money::parse((string)$amount);
    }

    public function setWithdrawAmount(
        int $userId,
        DateTimeImmutable $weekStart,
        float $amountEur
    ): void {
        $this->bucket($userId, $weekStart)->addWithdrawal($amountEur);
    }

    private function bucket(int $userId, DateTimeImmutable $weekStart): PeriodStats
    {
        return $this->buckets[$this->getWeeklyKey($userId, $weekStart)] ??= PeriodStats::make();
    }

    /**
     * Generates a unique key for the user and week start date.
     *
     * @example key = "3-2016-01"
     */
    private function getWeeklyKey(int $userId, DateTimeImmutable $weekStart): string
    {
        return sprintf('%d-%s', $userId, $weekStart->format('o-W'));
    }
}
