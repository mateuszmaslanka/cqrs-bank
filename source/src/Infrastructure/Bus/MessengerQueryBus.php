<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Application\Query\Query;
use App\Application\Bus\QueryBus;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerQueryBus implements QueryBus
{
    use HandleTrait;

    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function query(Query $query): mixed
    {
        return $this->handle($query);
    }
}
