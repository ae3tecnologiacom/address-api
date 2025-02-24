<?php

namespace App\Base\Repository;

use App\Base\Repository\Contracts\BaseRepositoryContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class BaseRepository implements BaseRepositoryContract
{

    protected $model;

    /**
     * @param $obj
     * @return void
     */
    public function setModel($obj): void {
        $this->model = resolve($obj);
    }

    /**
     * @param string|null $order_by
     * @return mixed
     */
    public function all(?string $order_by = 'id'): mixed {
        return $this->model->orderBy($order_by)->get();
    }

    /**
     * @param array<string|mixed> $per_page
     * @return mixed
     */
    public function getPaginated(
        $per_page
    ): mixed {
        return $this->model->paginate($per_page);
    }

    /**
     * @param mixed $obj
     * @return mixed
     */
    public function reset(mixed $obj): mixed {
        return $this->model = $obj;
    }

    /**
     * @param int|string|null $id
     * @param array $with
     * @return mixed
     */
    public function find(
        int|string|null $id,
        array           $with = [],
    ): mixed {
        return $this->model->with($with)->find($id);
    }

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
    ): mixed {
        return $this->model->with($with)->where($column, $value)->first();
    }

    /**
     * @param $column
     * @param array $value
     * @param array $with
     * @return mixed
     */
    public function getByColumn($column, array $value, array $with = []): mixed {
        return $this->model->with($with)->whereIn($column, $value)->get();
    }

    /**
     * @param int|string $id
     * @return object|null
     */
    public function findOrFail(int|string $id): object|null {
        return $this->model->findOrFail($id);
    }


    /**
     * @param array<string|mixed> $attributes
     * @return mixed
     */
    public function create(array $attributes): mixed {
        if ($this->model instanceof Builder) {
            return $this->model->firstOrCreate($attributes);
        }

        return $this->model->firstOrCreate($attributes);
    }

    /**
     * @param string|int $id
     * @param array<string|mixed> $attributes
     * @return object
     */
    public function update(string|int $id, array $attributes): object {
        return tap($this->model->findOrfail($id), function ($model) use ($attributes) {
            return $model->update($attributes);
        })->fresh();
    }

    /**
     * @param string|int $id
     * @return bool
     */
    public function delete(string|int $id): bool {
        return $this->model->find($id)->delete();
    }

    /**
     * @param $column
     * @param $value
     * @return bool
     */
    public function deleteByColumn($column, $value): bool {
        return $this->model->where($column, $value)->delete();
    }


    /**
     * @param $casts
     * @return Carbon|int
     */
    private function defineDatesByCasts($casts): Carbon|int {
        if (array_key_exists('created_at', $casts)) {
            if ($casts['created_at'] === 'datetime') {
                return now();
            }

            if ($casts['created_at'] === 'integer') {
                return time();
            }
        }

        return now();
    }

    /**
     * @param $attributes
     * @return array
     */
    private function defineTimestamps($attributes): array {
        if ($this->model->timestamps) {
            $date = $this->defineDatesByCasts($this->model->getCasts());
            $attributes = array_merge($attributes, [
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }

        return $attributes;
    }

    /**
     * @param $attributes
     * @return array
     */
    private function handlerAttributes($attributes): array {
        return $this->defineTimestamps($attributes);
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function save($attributes): mixed {
        return $this->model->create($attributes);
    }
}
