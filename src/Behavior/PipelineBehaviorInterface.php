<?php

declare(strict_types=1);

namespace IA\Mediator\Behavior;

interface PipelineBehaviorInterface
{
    /**
     * @param object $request
     * @param callable $next
     * @return mixed
     */
    public function handle(object $request, callable $next): mixed;
}