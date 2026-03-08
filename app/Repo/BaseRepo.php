<?php

namespace App\Repo;

/**
 * Interface BaseRepo
 *
 * Defines the standard contract for all repository classes in the application.
 */
interface BaseRepo
{
    /**
     * Retrieve all records.
     */
    public function getAll();

    /**
     * Retrieve paginated records based on provided parameters (search, sort, limit, etc.).
     */
    public function getPaginated(array $params);

    /**
     * Find a single record by its ID.
     */
    public function find(string $id);

    /**
     * Create a new record with the provided data.
     */
    public function create(array $data);

    /**
     * Update an existing record by its ID with the provided data.
     */
    public function update(string $id, array $data);

    /**
     * Remove a record by its ID.
     */
    public function delete(string $id);
}
