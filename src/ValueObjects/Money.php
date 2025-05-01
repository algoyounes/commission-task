<?php

declare(strict_types=1);

namespace AlgoYounes\CommissionTask\ValueObjects;

use AlgoYounes\CommissionTask\Support\Math;

final class Money
{
    private string $amount;
    private Currency $currency;

    public function __construct(string $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function parse(string $amount, null|string|Currency $currency = null): self
    {
        if ($currency === null) {
            $currency = Currency::toBase();
        }

        if (is_string($currency)) {
            $currency = Currency::parse($currency);
        }

        return new self($amount, $currency);
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getCurrencyCode(): string
    {
        return $this->currency->getCode();
    }

    public function add(Money $other): Money
    {
        $this->assertSameCurrency($other);

        return new self(Math::add($this->amount, $other->getAmount()), $this->currency);
    }

    public function subtract(Money $other): Money
    {
        $this->assertSameCurrency($other);

        return new self(Math::subtract($this->amount, $other->getAmount()), $this->currency);
    }

    public function multiply(string $multiplier): Money
    {
        return new self(Math::multiply($this->amount, $multiplier), $this->currency);
    }

    public function divide(string $divisor): Money
    {
        return new self(Math::divide($this->amount, $divisor), $this->currency);
    }

    public function roundUp(int $precision): Money
    {
        return new self(Math::roundUp($this->amount, $precision), $this->currency);
    }

    public function compare(Money $other): int
    {
        $this->assertSameCurrency($other);

        return Math::compare($this->amount, $other->getAmount());
    }

    public static function toZero(): Money
    {
        return new self('0', Currency::toBase());
    }

    public function isZero(): bool
    {
        return Math::compare($this->amount, '0') === 0;
    }

    public function isNotZero(): bool
    {
        return $this->isZero() === false;
    }

    private function assertSameCurrency(Money $other): void
    {
        if ($this->currency->getCode() !== $other->getCurrency()->getCode()) {
            throw new \LogicException('Cannot operate on different currencies.');
        }
    }

    public function __toString(): string
    {
        return "{$this->getAmount()} {$this->getCurrency()->getCode()}";
    }
}
