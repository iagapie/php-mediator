<?php

declare(strict_types=1);

namespace IA\Mediator\Exception;

final class ValidatorException extends MediatorException
{
    /**
     * ValidatorException constructor.
     * @param array<string, string> $errors
     */
    public function __construct(private array $errors, string $message)
    {
        parent::__construct($message);
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}