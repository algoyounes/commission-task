<?php

namespace AlgoYounes\CommissionTask\Tests\Unit\Service\Currency;

use AlgoYounes\CommissionTask\Services\Currency\CurrencyConversionService;
use AlgoYounes\CommissionTask\Services\ExchangeRate\ExchangeRatesService;
use AlgoYounes\CommissionTask\Services\ExchangeRate\Response\ExchangeRates;
use AlgoYounes\CommissionTask\Tests\Helpers\ReflectionHelper;
use AlgoYounes\CommissionTask\Tests\TestCase;
use AlgoYounes\CommissionTask\ValueObjects\Currency;
use AlgoYounes\CommissionTask\ValueObjects\Money;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class CurrencyConversionServiceTest extends TestCase
{
    use ReflectionHelper;

    #[DataProvider('currencyConversionProvider')]
    public function test_convert(
        Money $amount,
        Currency $toCurrency,
        ExchangeRates $rates,
        string $expected
    ) {
        $mockExchangeRatesService = Mockery::mock(ExchangeRatesService::class);
        $service = Mockery::mock(CurrencyConversionService::class)->makePartial();
        $this->setProtectedProperty($service, 'exchangeRatesService', $mockExchangeRatesService);

        $mockExchangeRatesService->allows('getRates')->andReturns($rates);

        $this->assertMoneyEquals($expected, $service->convert($amount, $toCurrency));
    }

    public static function currencyConversionProvider(): array
    {
        return [
            'same currency' => [
                Money::parse('100.00', Currency::fromString('EUR')),
                Currency::fromString('EUR'),
                ExchangeRates::fromArray([
                    'base' => 'EUR',
                    'rates' => ['EUR' => 1, 'USD' => 1.1, 'JPY' => 120.10],
                    'timestamp' => 1237954220,
                    'date' => '2025-05-02',
                ]),
                '100.00'
            ],
            'EUR to USD' => [
                Money::parse('100.00', Currency::fromString('EUR')),
                Currency::fromString('USD'),
                ExchangeRates::fromArray([
                    'base' => 'EUR',
                    'rates' => ['EUR' => 1, 'USD' => 1.1, 'JPY' => 120.10],
                    'timestamp' => 1237954220,
                    'date' => '2025-05-02',
                ]),
                '110.0000000000',
            ],
            'USD to EUR' => [
                Money::parse('110.00', Currency::fromString('USD')),
                Currency::fromString('EUR'),
                ExchangeRates::fromArray([
                    'base' => 'EUR',
                    'rates' => ['EUR' => 1, 'USD' => 1.1, 'JPY' => 120.10],
                    'timestamp' => 1237954220,
                    'date' => '2025-05-02',
                ]),
                '100.0000000000',
            ],
            'USD to JPY' => [
                Money::parse('110.00', Currency::fromString('USD')),
                Currency::fromString('JPY'),
                ExchangeRates::fromArray([
                    'base' => 'EUR',
                    'rates' => ['EUR' => 1, 'USD' => 1.1, 'JPY' => 120.10],
                    'timestamp' => 1237954220,
                    'date' => '2025-05-02',
                ]),
                '12010.0000000000',
            ],
            'JPY to EUR' => [
                Money::parse('13000.00', Currency::fromString('JPY')),
                Currency::fromString('EUR'),
                ExchangeRates::fromArray([
                    'base' => 'EUR',
                    'rates' => ['EUR' => 1, 'USD' => 1.1, 'JPY' => 120.10],
                    'timestamp' => 1237954220,
                    'date' => '2025-05-02',
                ]),
                '108.2431307243',
            ],
        ];
    }
}
