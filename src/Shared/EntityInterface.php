<?php

namespace App\Shared;

interface EntityInterface
{
    /**
     * @return array<string, float|int|string|null>
     */
    public function toArray(): array;
}