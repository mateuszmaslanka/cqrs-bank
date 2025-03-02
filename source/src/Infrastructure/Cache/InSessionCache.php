<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

class InSessionCache implements CacheItemPoolInterface
{
    private AttributeBag $sessionBag;
    private array $itemsDeferred = [];


    public function __construct(RequestStack $requestStack)
    {
        /** @var AttributeBag $sessionBag */
        $sessionBag = $requestStack->getSession()->getBag('in_session_cache');
        $this->sessionBag = $sessionBag;
    }

    public function getItem(string $key): InSessionCacheItem
    {
        $item = $this->itemsDeferred[$key]
            ?? $this->sessionBag->get($key, null)
            ?? null;

        if (null === $item) {
            return new InSessionCacheItem($key);
        }

        return $item;
    }

    public function getItems(array $keys = []): iterable
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }

        return $items;
    }

    public function hasItem(string $key): bool
    {
        return $this->sessionBag->has($key);
    }

    public function clear(): bool
    {
        $this->sessionBag->clear();
        $this->itemsDeferred = [];

        return true;
    }

    public function deleteItem(string $key): bool
    {
        $this->sessionBag->remove($key);
        unset($this->itemsDeferred[$key]);

        return true;
    }

    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }

        return true;
    }

    public function save(CacheItemInterface $item): bool
    {
        $this->sessionBag->set($item->getKey(), $item);

        return true;
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->itemsDeferred[$item->getKey()] = $item;

        return true;
    }

    public function commit(): bool
    {
        foreach ($this->itemsDeferred as $item) {
            $this->save($item);
        }
        $this->itemsDeferred = [];

        return true;
    }
}
