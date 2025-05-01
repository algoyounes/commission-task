<?php

namespace AlgoYounes\CommissionTask\Services\ExchangeRate\Exceptions;

use Exception;

class HttpRequestException extends Exception
{
    public function __construct(
        private readonly int $statusCode,
        string $message = 'Failed to fetch exchange rates',
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
