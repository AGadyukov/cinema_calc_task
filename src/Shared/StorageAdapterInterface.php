<?php

namespace App\Shared;

interface StorageAdapterInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function insert(array $data): void;

    /**
     * @param int $id
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): void;

    /**
     * @param array<string, int|string|null> $condition
     *
     * @return array<int, mixed>
     */
    public function select(array $condition): array;
}