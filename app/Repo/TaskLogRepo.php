<?php

namespace App\Repo;

use App\Models\TaskLog;

class TaskLogRepo implements BaseRepo
{
    /**
     * Create a new class instance.
     */
    public function __construct(private TaskLog $taskLog)
    {
        //
    }

    /**
     * Retrieve all records.
     */
    public function getAll() {}

    /**
     * Retrieve paginated records based on provided parameters (search, sort, limit, etc.).
     */
    public function getPaginated(array $params) {}

    /**
     * Find a single record by its ID.
     */
    public function find(string $id) {}

    /**
     * Create a new record with the provided data.
     */
    public function create(array $data)
    {
        return $this->taskLog->create($data);
    }

    /**
     * Update an existing record by its ID with the provided data.
     */
    public function update(string $id, array $data) {}

    /**
     * Remove a record by its ID.
     */
    public function delete(string $id) {}
}
