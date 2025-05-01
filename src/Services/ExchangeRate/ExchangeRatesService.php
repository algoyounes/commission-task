<?php

namespace AlgoYounes\CommissionTask\Services\ExchangeRate;

use AlgoYounes\CommissionTask\Services\ExchangeRate\Exceptions\ExchangeRatesApiException;
use AlgoYounes\CommissionTask\Services\ExchangeRate\Response\ExchangeRates;
use GuzzleHttp\RequestOptions;

class ExchangeRatesService extends AbstractHttpService
{
    public const GET_RATE_ENDPOINT = '/latest';
    public const RETRY_COUNT = 3;
    public const TIMEOUT = 30;

    public static ExchangeRates|null $exchangeRates = null;

    public function __construct()
    {
        parent::__construct($this->getBaseUri(), self::RETRY_COUNT, self::TIMEOUT);
    }

    public function getRates(): ExchangeRates
    {
        if (static::$exchangeRates instanceof ExchangeRates) {
            return static::$exchangeRates;
        }

        $response = $this->get(self::GET_RATE_ENDPOINT);

        $isSuccess = $response['success'] === true ?? false;
        if (! $isSuccess) {
            $error   = $response['error'] ?? [];
            $message = $error['info']  ?? 'unknown exchange rates api error';

            throw new ExchangeRatesApiException(
                $error['code'] ?? 0,
                $message,
                $error
            );
        }

        return static::$exchangeRates = ExchangeRates::fromArray($response);
    }

    protected function buildRequest(): array
    {
        return [
            RequestOptions::QUERY => [
                'access_key' => $this->getApiKey(),
            ],
        ];
    }

    private function getApiKey(): string
    {
        return config('app.exchange_rates.api_key');
    }

    private function getBaseUri(): string
    {
        return config('app.exchange_rates.base_uri');
    }
}
