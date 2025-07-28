<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final class AccountBalance
{
    private const int MAX_VALUE = 50000; // 500 EUR

    public function __construct(
        private int $value = 0
    ) {
        if ($value < 0) {
            throw new InvalidArgumentException('Balance cannot be negative');
        }
    }

    public static function fromInt(int $value): self
    {
        return new AccountBalance($value);
    }

    public function add(int $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be positive');
        }

        $newValue = $this->value + $amount;
        if (self::MAX_VALUE < $newValue) {
            throw new InvalidArgumentException(sprintf('Maximum balance %d exceeded', self::MAX_VALUE));
        }

        $this->value = $newValue;
    }

    public function subtract(int $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be positive');
        }

        $newValue = $this->value - $amount;
        if (0 > $newValue) {
            throw new InvalidArgumentException(sprintf('Minimum balance %d exceeded', 0));
        }

        $this->value = $newValue;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function __tostring(): string
    {
        return number_format($this->value / 100, 2, '.', ' ') . ' EUR';
    }
}
