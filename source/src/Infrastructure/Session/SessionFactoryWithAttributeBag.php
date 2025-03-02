<?php

declare(strict_types=1);

namespace App\Infrastructure\Session;

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\SessionFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionFactoryWithAttributeBag implements SessionFactoryInterface
{
    public function __construct(private readonly SessionFactoryInterface $sessionFactory)
    {
    }

    public function createSession(): SessionInterface
    {
        $bag = new AttributeBag('in_session_cache');
        $bag->setName('in_session_cache');

        $session = $this->sessionFactory->createSession();
        $session->registerBag($bag);

        return $session;
    }
}
