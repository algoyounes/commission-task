<?php

namespace AlgoYounes\CommissionTask\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;

readonly class DateImmutable
{
    public function __construct(private string $date)
    {
    }

    public static function fromFormat(string $format, string $date): self
    {
        $dateTime = DateTimeImmutable::createFromFormat($format, $date);
        if ($dateTime === false) {
            throw new InvalidArgumentException("Invalid date format: {$date}");
        }

        return new self($dateTime->format('Y-m-d'));
    }

    public static function fromString(string $date): self
    {
        return static::fromFormat('Y-m-d', $date);
    }

    public function toYearWeekKey(): string
    {
        return (new DateTimeImmutable($this->date))->format('o-W'); // e.g., "2024-17" (ISO Year-Week)
    }

    public function getStartOfWeek(): DateTimeImmutable
    {
        return (new DateTimeImmutable($this->date))->modify('monday this week');
    }

    public function format(string $format): string
    {
        return DateTimeImmutable::createFromFormat('Y-m-d', $this->date)->format($format);
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function isEqualTo(self $other): bool
    {
        return $this->date === $other->getDate();
    }

    public function toString(): string
    {
        return $this->getDate();
    }
}
