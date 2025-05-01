<?php

namespace AlgoYounes\CommissionTask\Tests\Helpers;

use ReflectionClass;

trait ReflectionHelper
{
    protected function setProtectedProperty(object $object, string $property, mixed $value): void
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setValue($object, $value);
    }
}
