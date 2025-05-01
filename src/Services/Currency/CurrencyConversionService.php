<?php

namespace AlgoYounes\CommissionTask\Services\Currency;

use AlgoYounes\CommissionTask\Services\ExchangeRate\ExchangeRatesService;
use AlgoYounes\CommissionTask\Support\Math;
use AlgoYounes\CommissionTask\ValueObjects\Currency;
use AlgoYounes\CommissionTask\ValueObjects\Money;
use GuzzleHttp\Exception\GuzzleException;

class CurrencyConversionService
{
    protected ExchangeRatesService $exchangeRatesService;

    public function __construct()
    {
        $this->exchangeRatesService = new ExchangeRatesService();
    }

    /**
     * @throws GuzzleException
     */
    public function convert(Money $amount, Currency $toCurrency): Money
    {
        if ($amount->getCurrency()->equals($toCurrency)) {
            return $amount;
        }

        /** Convert the amount to the requested currency if it is in the base currency. */
        $rates = $this->exchangeRatesService->getRates();
        if ($amount->getCurrency()->equals($rates->getBase())) {
            return $amount->multiply($rates->getRates()[$toCurrency->getCode()]);
        }

        /** Convert the amount to the base currency and return it if the requested currency is the base currency. */
        $baseCurrencyAmount = Money::parse(
            Math::divide($amount->getAmount(), $rates->getRates()[$amount->getCurrency()->getCode()]),
            $rates->getBase()
        );

        if ($toCurrency->equals($rates->getBase())) {
            return $baseCurrencyAmount;
        }

        /** Convert the amount to the requested currency. */
        return $baseCurrencyAmount->multiply($rates->getRates()[$toCurrency->getCode()]);
    }
}
