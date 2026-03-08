<?php

namespace App\Services;

use App\Repo\UserRepo;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected UserRepo $userRepo)
    {
        //
    }

    public function getPaginatedUsers(array $params)
    {
        return $this->userRepo->getPaginated($params);
    }
}
