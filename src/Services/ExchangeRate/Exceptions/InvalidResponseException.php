<?php

namespace AlgoYounes\CommissionTask\Services\ExchangeRate\Exceptions;

use Exception;

class InvalidResponseException extends Exception
{
    public function __construct(
        private int $statusCode,
        private string $responseBody,
    ) {
        parent::__construct("invalid response from exchange rates api");
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
