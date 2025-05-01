<?php

namespace AlgoYounes\CommissionTask\Services\ExchangeRate\Response;

use AlgoYounes\CommissionTask\ValueObjects\Currency;
use AlgoYounes\CommissionTask\ValueObjects\DateImmutable;

class ExchangeRates
{
    public function __construct(
        private readonly int $timestamp,
        private readonly Currency $base,
        private readonly DateImmutable $date,
        private readonly array $rates,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            timestamp: $data['timestamp'] ?? 0,
            base: Currency::fromString($data['base']),
            date: DateImmutable::fromString($data['date']),
            rates: $data['rates'] ?? []
        );
    }

    public function getBase(): Currency
    {
        return $this->base;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getDate(): DateImmutable
    {
        return $this->date;
    }

    public function getRates(): array
    {
        return $this->rates;
    }
}
