<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class AccountNumber
{
    private function __construct(
        private string $number,
    ) {
    }

    public static function fromString(string $number): self
    {
        return new self($number);
    }

    public function equals(AccountNumber $accountNumber): bool
    {
        return $this->number === $accountNumber->number;
    }

    public function __tostring(): string
    {
        return $this->number;
    }

    public function format(): string
    {
        return trim(chunk_split($this->number, 4, ' '));
    }
}
