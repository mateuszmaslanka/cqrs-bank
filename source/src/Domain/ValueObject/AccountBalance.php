<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class AccountBalance
{
    private int $value = 0;

    public function add(int $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }

        $this->value += $amount;
    }

    public function format(): string
    {
        return number_format($this->value / 100, 2, '.', ' ') . ' EUR';
    }
}
