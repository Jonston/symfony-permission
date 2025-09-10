<?php

declare(strict_types=1);

namespace Jonston\SymfonyPermission\Dto;

use ReflectionException;

abstract class AbstractDto
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @throws ReflectionException
     */
    public static function fromArray(array $data): static
    {
        $reflection = new \ReflectionClass(static::class);
        $args = [];
        foreach ($reflection->getConstructor()->getParameters() as $param) {
            $args[] = $data[$param->getName()] ?? null;
        }
        return $reflection->newInstanceArgs($args);
    }
}

