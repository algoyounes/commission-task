<?php

namespace AlgoYounes\CommissionTask\Tests\Unit\Service\Commission;

use AlgoYounes\CommissionTask\Services\Commission\Tracker\WithdrawalTrackerService;
use AlgoYounes\CommissionTask\Tests\TestCase;
use AlgoYounes\CommissionTask\ValueObjects\Money;
use DateTimeImmutable;

class WithdrawalTrackerServiceTest extends TestCase
{
    private WithdrawalTrackerService $tracker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tracker = new WithdrawalTrackerService();
    }

    public function test_get_weekly_operation_count_returns_zero_for_new_user(): void
    {
        $userId = 1;
        $weekStart = new DateTimeImmutable('2024-01-01');

        $count = $this->tracker->getWeeklyOperationCount($userId, $weekStart);

        $this->assertEquals(0, $count);
    }

    public function test_get_weekly_operation_count_increments_with_each_withdrawal(): void
    {
        $userId = 1;
        $weekStart = new DateTimeImmutable('2024-01-01');

        $this->tracker->setWithdrawAmount($userId, $weekStart, 100.0);
        $this->tracker->setWithdrawAmount($userId, $weekStart, 200.0);

        $count = $this->tracker->getWeeklyOperationCount($userId, $weekStart);

        $this->assertEquals(2, $count);
    }

    public function test_get_total_withdrawn_returns_zero_for_new_user(): void
    {
        $userId = 1;
        $weekStart = new DateTimeImmutable('2024-01-01');

        $total = $this->tracker->getTotalWithdrawn($userId, $weekStart);

        $this->assertEquals(Money::parse(0.0), $total);
    }

    public function test_get_total_withdrawn_sums_all_withdrawals_in_week(): void
    {
        $userId = 1;
        $weekStart = new DateTimeImmutable('2024-01-01');

        $this->tracker->setWithdrawAmount($userId, $weekStart, 100.0);
        $this->tracker->setWithdrawAmount($userId, $weekStart, 200.0);

        $total = $this->tracker->getTotalWithdrawn($userId, $weekStart);

        $this->assertEquals(Money::parse(300.0), $total);
    }

    public function test_withdrawals_are_tracked_separately_for_different_weeks()
    {
        $userId = 1;
        $week1Start = new DateTimeImmutable('2024-01-01');
        $week2Start = new DateTimeImmutable('2024-01-08');

        $this->tracker->setWithdrawAmount($userId, $week1Start, 100.0);
        $this->tracker->setWithdrawAmount($userId, $week2Start, 200.0);

        $week1Total = $this->tracker->getTotalWithdrawn($userId, $week1Start);
        $week2Total = $this->tracker->getTotalWithdrawn($userId, $week2Start);

        $this->assertEquals(Money::parse(100.0), $week1Total);
        $this->assertEquals(Money::parse(200.0), $week2Total);
    }

    public function test_withdrawals_are_tracked_separately_for_different_users(): void
    {
        $userId1 = 1;
        $userId2 = 2;
        $weekStart = new DateTimeImmutable('2024-01-01');

        $this->tracker->setWithdrawAmount($userId1, $weekStart, 100.0);
        $this->tracker->setWithdrawAmount($userId2, $weekStart, 200.0);

        $user1Total = $this->tracker->getTotalWithdrawn($userId1, $weekStart);
        $user2Total = $this->tracker->getTotalWithdrawn($userId2, $weekStart);

        $this->assertEquals(Money::parse(100.0), $user1Total);
        $this->assertEquals(Money::parse(200.0), $user2Total);
    }
}
