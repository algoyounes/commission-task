<?php

namespace AlgoYounes\CommissionTask\ValueObjects;

class Currency
{
    private string $code;
    private const ALLOWED_CURRENCY_CODES = ['EUR', 'USD', 'JPY'];
    private const DEFAULT_CURRENCY = 'EUR';

    public function __construct(string $code)
    {
        $this->assertValidCurrency($code);

        $this->code = $code;
    }

    public static function parse(string $code): self
    {
        return new self(strtoupper($code));
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function decimalPlaces(): int
    {
        $map = config('app.commission.currency_decimals');
        if (! isset($map[$this->code])) {
            throw new \LogicException("Decimal places for {$this->code} not configured.");
        }

        return $map[$this->code];
    }

    public static function toBase(): self
    {
        return self::parse(config('app.commission.base_currency', self::DEFAULT_CURRENCY));
    }

    private function assertValidCurrency(string $code): void
    {
        if (! in_array($code, self::ALLOWED_CURRENCY_CODES, true)) {
            throw new \InvalidArgumentException("Invalid currency code: $code");
        }
    }

    public function equals(Currency $other): bool
    {
        return $this->code === $other->getCode();
    }
}
