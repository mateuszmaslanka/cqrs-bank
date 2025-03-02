<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use DateTime;
use DateTimeImmutable;
use Psr\Cache\CacheItemInterface;

class InSessionCacheItem implements CacheItemInterface
{
    private const int DEFAULT_EXPIRATION_SECONDS = 10;

    private mixed $value = null;
    private bool $isHit = false;
    private ?DateTimeImmutable $expiredAt = null;

    public function __construct(private readonly string $key)
    {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function get(): mixed
    {
        return $this->isHit ? unserialize($this->value) : null;
    }

    public function isHit(): bool
    {
        if (false === $this->isHit) {
            return false;
        }

        if (null === $this->expiredAt) {
            return true;
        }

        return $this->expiredAt > new DateTime();
    }

    public function set(mixed $value): static
    {
        $this->value = serialize($value);
        $this->isHit = true;

        return $this;
    }

    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        $this->expiredAt = DateTimeImmutable::createFromMutable($expiration) ?: (new DateTimeImmutable())->add(new \DateInterval('PT' . self::DEFAULT_EXPIRATION_SECONDS . 'S'));

        return $this;
    }

    public function expiresAfter(int|\DateInterval|null $time): static
    {
        if (null === $time) {
            $this->expiredAt = (new DateTimeImmutable())->add(new \DateInterval('PT' . self::DEFAULT_EXPIRATION_SECONDS . 'S'));
        }

        if ($time instanceof \DateInterval) {
            $this->expiredAt = (new DateTimeImmutable())->add($time);
        }

        if (is_int($time)) {
            $this->expiredAt = (new DateTimeImmutable())->add(new \DateInterval('PT' . $time . 'S'));
        }

        return $this;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiredAt;
    }
}
