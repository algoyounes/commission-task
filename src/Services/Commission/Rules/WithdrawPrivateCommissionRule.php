<?php

namespace AlgoYounes\CommissionTask\Services\Commission\Rules;

use AlgoYounes\CommissionTask\Entity\Operation;
use AlgoYounes\CommissionTask\Services\Commission\Contracts\CommissionRuleContract;
use AlgoYounes\CommissionTask\Services\Commission\Tracker\WithdrawalTrackerService;
use AlgoYounes\CommissionTask\Services\Currency\CurrencyConversionService;
use AlgoYounes\CommissionTask\Support\Math;
use AlgoYounes\CommissionTask\ValueObjects\Currency;
use AlgoYounes\CommissionTask\ValueObjects\Money;

class WithdrawPrivateCommissionRule implements CommissionRuleContract
{
    private const COMMISSION_RATE = 0.3;
    private const WEEKLY_FREE_AMOUNT = 1000.00;
    private const WEEKLY_FREE_OPERATIONS = 3;

    private CurrencyConversionService $currencyService;
    private WithdrawalTrackerService $withdrawalTrackerService;
    private Currency $baseCurrency;

    public function __construct()
    {
        $this->currencyService = new CurrencyConversionService();
        $this->withdrawalTrackerService = new WithdrawalTrackerService();
        $this->baseCurrency = Currency::fromString(config('app.commission.base_currency'));
    }

    public function isSupported(Operation $operation): bool
    {
        return $operation->isWithdrawal() && $operation->isPrivateUser();
    }

    /**
     * Calculate the commission for a withdrawal operation.
     */
    public function calculate(Operation $operation): Money
    {
        $weekStart = $operation->getDate()->getStartOfWeek();

        $weeklyWithdrawalCount = $this->withdrawalTrackerService->getWeeklyOperationCount($operation->getUserId(), $weekStart);
        $weeklyWithdrawnBaseCurrencyAmount = $this->withdrawalTrackerService->getTotalWithdrawn($operation->getUserId(), $weekStart);

        // Current amount converted to base currency for quota calculation
        $amountBaseCurrency = $this->currencyService->convert(
            $operation->getAmount(),
            $this->getBaseCurrency()
        );

        // Determine how much of the withdrawal is taxable (in base currency)
        $taxableBase = $this->calculateTaxableAmount($weeklyWithdrawalCount, $weeklyWithdrawnBaseCurrencyAmount, $amountBaseCurrency);

        // Record the withdrawal for future operations
        $this->withdrawalTrackerService->setWithdrawAmount($operation->getUserId(), $weekStart, $amountBaseCurrency->getAmount());

        return $this->calculateFee($taxableBase, $operation->getAmount()->getCurrency());
    }

    private function calculateTaxableAmount(int $weeklyWithdrawalCount, Money $weeklyWithdrawnAmount, Money $currentAmount): Money
    {
        // 1) free ops exhausted
        if ($weeklyWithdrawalCount >= $this->getWeeklyFreeOperations()) {
            return $currentAmount;
        }

        $weeklyFreeAmount = Money::parse($this->getWeeklyFreeAmount(), $this->getBaseCurrency());

        // 2) €1 000 already used
        if ($weeklyWithdrawnAmount->compare($weeklyFreeAmount) >= 0) {
            return $currentAmount;
        }

        // 3) stays within free quota
        $newTotal = $weeklyWithdrawnAmount->add($currentAmount);
        if ($newTotal->compare($weeklyFreeAmount) <= 0) {
            return Money::toZero();
        }

        // 4) crosses limit → excess only
        return $newTotal->subtract($weeklyFreeAmount);
    }

    private function calculateFee(Money $taxableBase, Currency $currency): Money
    {
        if ($taxableBase->isZero()) {
            return Money::toZero();
        }

        // Convert taxable part back to the original currency
        $taxableOriginal = $this->currencyService->convert($taxableBase, $currency);

        $fee = Math::multiply($taxableOriginal->getAmount(), Math::divide($this->getCommissionRate(), 100));
        $roundedFee = Math::roundUp($fee, $currency->decimalPlaces());

        return Money::parse($roundedFee, $currency);
    }

    public function getBaseCurrency(): Currency
    {
        return $this->baseCurrency;
    }

    public function getWeeklyFreeAmount(): float
    {
        return config('app.commission.rules.withdraw.private.weekly_free_amount', self::WEEKLY_FREE_AMOUNT);
    }

    public function getWeeklyFreeOperations(): int
    {
        return config('app.commission.rules.withdraw.private.weekly_free_operations', self::WEEKLY_FREE_OPERATIONS);
    }

    public function getCommissionRate(): float
    {
        return config('app.commission.rules.withdraw.private.commission', self::COMMISSION_RATE);
    }
}
