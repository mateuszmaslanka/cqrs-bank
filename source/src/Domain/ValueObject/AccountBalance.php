<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final class AccountBalance
{
    private const int MAX_VALUE = 50000; // 500 EUR

    private int $value = 0;

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

    public function format(): string
    {
        return number_format($this->value / 100, 2, '.', ' ') . ' EUR';
    }
}
