<?php

declare(strict_types=1);

namespace App\Application\Bus;

use App\Application\Command\Command;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
