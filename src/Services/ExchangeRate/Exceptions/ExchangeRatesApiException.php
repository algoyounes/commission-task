<?php

namespace AlgoYounes\CommissionTask\Services\ExchangeRate\Exceptions;

use Exception;

class ExchangeRatesApiException extends Exception
{
    public function __construct(
        private readonly int $errorCode,
        private readonly string $errorMessageCode,
        private readonly array $errorDetails = []
    ) {
        parent::__construct("an error occurred in the exchange rates api response");
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getErrorCodeMessage(): string
    {
        return $this->errorMessageCode;
    }

    public function getErrorDetails(): array
    {
        return $this->errorDetails;
    }
}
