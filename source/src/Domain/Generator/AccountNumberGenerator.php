<?php

declare(strict_types=1);

namespace App\Domain\Generator;

use App\Domain\ValueObject\AccountNumber;

final class AccountNumberGenerator
{
    private const int LENGTH_PL = 26;

    public function generate(): AccountNumber
    {
        $number = 'PL';
        for ($i = 0; $i < self::LENGTH_PL; $i++) {
            $number .= rand(0, 9);
        }

        return AccountNumber::fromString($number);
    }
}
