<?php

declare(strict_types=1);

namespace IA\Mediator;

use Closure;
use IA\Mediator\Behavior\PipelineBehaviorInterface;

class Mediator implements MediatorInterface
{
    private Closure $behaviorChain;

    /**
     * Mediator constructor.
     * @param iterable<PipelineBehaviorInterface> $behaviors
     */
    public function __construct(iterable $behaviors)
    {
        $this->behaviorChain = $this->createBehaviorChain($behaviors);
    }

    /**
     * {@inheritDoc}
     */
    public function send(object $request): mixed
    {
        return ($this->behaviorChain)($request);
    }

    /**
     * @param iterable<PipelineBehaviorInterface> $behaviors
     * @return Closure
     */
    private function createBehaviorChain(iterable $behaviors): Closure
    {
        $lastCallable = static fn () => null;

        foreach ($behaviors as $behavior) {
            $lastCallable = static fn (object $request) => $behavior->handle($request, $lastCallable);
        }

        return $lastCallable;
    }
}