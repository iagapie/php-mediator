<?php

declare(strict_types=1);

namespace IA\Mediator\Behavior;

use IA\Mediator\Exception\MediatorException;
use Psr\Container\ContainerInterface;

use Psr\Log\LoggerInterface;

use function gettype;
use function is_object;
use function method_exists;
use function sprintf;

final class RequestHandlerBehavior implements PipelineBehaviorInterface
{
    /**
     * RequestHandlerBehavior constructor.
     * @param ContainerInterface $container
     * @param LoggerInterface $logger
     */
    public function __construct(private ContainerInterface $container, private LoggerInterface $logger)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(object $request, callable $next): mixed
    {
        $class = $request::class;

        $this->logger->info('----- Handling request {request}.', ['request' => $class]);

        if (!$this->container->has($class)) {
            throw new MediatorException(sprintf('Service "%s" does not exists.', $class));
        }

        $service = $this->container->get($class);

        if (!method_exists($service, 'handle')) {
            throw new MediatorException(sprintf('Handler method %s::handle does not exists.', $service::class));
        }

        $response = $service->handle($request);

        $type = is_object($response) ? get_class($response) : gettype($response);

        $this->logger->info(
            '----- Request {request} handled - response: {response}.',
            ['request' => $class, 'response' => $type]
        );

        return $response;
    }
}