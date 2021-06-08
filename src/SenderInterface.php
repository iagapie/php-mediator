<?php

declare(strict_types=1);

namespace IA\Mediator;

interface SenderInterface
{
    /**
     * @param object $request
     * @return mixed
     */
    public function send(object $request): mixed;
}