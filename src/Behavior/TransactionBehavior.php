<?php

declare(strict_types=1);

namespace IA\Mediator\Behavior;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Throwable;

final class TransactionBehavior implements PipelineBehaviorInterface
{
    /**
     * TransactionBehavior constructor.
     * @param Connection $connection
     * @param LoggerInterface $logger
     */
    public function __construct(private Connection $connection, private LoggerInterface $logger)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(object $request, callable $next): mixed
    {
        $this->logger->info('----- Starting dbal transaction for request: {request}.', ['request' => $request::class]);
        $this->connection->beginTransaction();

        try {
            $response = $next($request);

            $this->logger->info(
                '----- Committing dbal transaction for request: {request}.',
                ['request' => $request::class]
            );
            $this->connection->commit();

            return $response;
        } catch (Throwable $e) {
            $this->logger->info(
                '----- Rolling back dbal transaction for request: {request}.',
                ['request' => $request::class]
            );
            $this->connection->rollBack();

            throw $e;
        }
    }
}