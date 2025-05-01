<?php

namespace AlgoYounes\CommissionTask\Tests\Unit\Service\ExchangeRates;

use AlgoYounes\CommissionTask\Services\ExchangeRate\Exceptions\HttpRequestException;
use AlgoYounes\CommissionTask\Services\ExchangeRate\Exceptions\InvalidResponseException;
use AlgoYounes\CommissionTask\Services\ExchangeRate\ExchangeRatesService;
use AlgoYounes\CommissionTask\Services\ExchangeRate\Response\ExchangeRates;
use AlgoYounes\CommissionTask\Tests\Helpers\ReflectionHelper;
use AlgoYounes\CommissionTask\Tests\TestCase;
use GuzzleHttp\Client;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class ExchangeRatesServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    use ReflectionHelper;

    protected ExchangeRatesService $service;
    protected Client $mockClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockClient = Mockery::mock(Client::class);
        $this->service = Mockery::mock(ExchangeRatesService::class)->makePartial();

        $this->setProtectedProperty($this->service, 'client', $this->mockClient);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->setProtectedProperty($this->service, 'exchangeRates', null);
    }


    public function test_returns_exchange_rates(): void
    {
        $responseMock = [
            'success' => true,
            'base' => 'EUR',
            'rates' => ['USD' => 1.18, 'JPY' => 130.15],
            'timestamp' => 1627654321,
            'date' => '2022-07-30'
        ];

        $this->service->shouldAllowMockingProtectedMethods()->allows('get')->andReturns($responseMock);

        $rates = $this->service->getRates();

        $this->assertEquals('EUR', $rates->getBase()->getCode());
        $this->assertArrayHasKey('USD', $rates->getRates());
        $this->assertEquals(1.18, $rates->getRates()['USD']);
    }

    public function test_returns_cached_rates(): void
    {
        $cachedRates = ExchangeRates::fromArray([
            'base' => 'EUR',
            'rates' => ['USD' => 1.18, 'JPY' => 130.15],
            'timestamp' => 1627654321,
            'date' => '2022-07-30',
        ]);

        $this->setProtectedProperty($this->service, 'exchangeRates', $cachedRates);
        $rates = $this->service->getRates();

        $this->assertSame($cachedRates, $rates);
    }

    public function test_throws_exception_when_invalid_response(): void
    {
        $this->expectException(InvalidResponseException::class);

        $this->service
            ->shouldAllowMockingProtectedMethods()
            ->allows('get')
            ->andThrows(new InvalidResponseException(200, 'invalid body'));

        $this->service->getRates();
    }

    public function test_throws_http_exception_on_non_200_status(): void
    {
        $this->expectException(HttpRequestException::class);

        $this->service
            ->shouldAllowMockingProtectedMethods()
            ->expects('get')
            ->andThrows(new HttpRequestException(500, 'Failed to fetch exchange rates'));

        $this->service->getRates();
    }
}
