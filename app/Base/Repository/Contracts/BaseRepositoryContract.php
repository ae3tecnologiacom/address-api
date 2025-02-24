<?php

namespace App\Base\Repository\Contracts;

interface BaseRepositoryContract
{

    /**
     * @param $obj
     * @return void
     */
    public function setModel($obj): void;

    /**
     * @param string|null $order_by
     * @return mixed
     */
    public function all(?string $order_by = 'id'): mixed;

    /**
     * @param mixed $obj
     * @return mixed
     */
    public function reset(mixed $obj): mixed;

    /**
     * @param array<string|mixed> $per_page
     * @return mixed
     */
    public function getPaginated(
        $per_page
    ): mixed;

    /**
     * @param int|string|null $id
     * @param array $with
     * @return mixed
     */
    public function find(
        int|string|null $id,
        array           $with = [],
    ): mixed;

    /**
     * @param string $column
     * @param string $value
     * @param array $with
     * @return mixed
     */
    public function findByColumn(
        string $column,
        string $value,
        array  $with = [],
    ): mixed;

    /**
     * @param $column
     * @param array $value
     * @param array $with
     * @return mixed
     */
    public function getByColumn($column, array $value, array $with = []): mixed;

    /**
     * @param int|string $id
     * @return object|null
     */
    public function findOrFail(int|string $id): object|null;

    /**
     * @param array<string|mixed> $attributes
     * @return mixed
     */
    public function create(array $attributes): mixed;

    /**
     * @param string|int $id
     * @param array<string|mixed> $attributes
     * @return object
     */
    public function update(string|int $id, array $attributes): object;

    /**
     * @param string|int $id
     * @return bool
     */
    public function delete(string|int $id): bool;

    /**
     * @param $column
     * @param $value
     * @return bool
     */
    public function deleteByColumn($column, $value): bool;

    /**
     * @param $attributes
     * @return mixed
     */
    public function save($attributes): mixed;
}
