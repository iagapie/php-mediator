<?php

declare(strict_types=1);

namespace IA\Mediator\Behavior;

use IA\Mediator\Exception\ValidatorException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function sprintf;

final class ValidatorBehavior implements PipelineBehaviorInterface
{
    /**
     * ValidatorBehavior constructor.
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     */
    public function __construct(private ValidatorInterface $validator, private LoggerInterface $logger)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(object $request, callable $next): mixed
    {
        $this->logger->info('----- Validating request {request}.', ['request' => $request::class]);

        $constraintViolationList = $this->validator->validate($request);

        if (count($constraintViolationList) === 0) {
            return $next($request);
        }

        $this->logger->info('----- Validation errors - {request}.', ['request' => $request::class]);

        $errors = [];

        /** @var ConstraintViolationInterface $error */
        foreach ($constraintViolationList as $error) {
            $errors[$error->getPropertyPath()] = $error->getMessage();
        }

        throw new ValidatorException($errors, sprintf('Request "%s" not passed validation.', $request::class));
    }
}