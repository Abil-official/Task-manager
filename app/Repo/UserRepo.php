<?php

namespace App\Repo;

use App\Models\User;

class UserRepo
{
    public function __construct(private User $user)
    {
        //
    }

    public function getPaginated(array $params)
    {
        $query = $this->user->query()->with(['executors']);

        $query = $this->applySearch($query, $params['search'] ?? null);
        $query = $this->applySort($query, $params['sort_by'] ?? 'created_at', $params['sort_order'] ?? 'desc');

        return $query->paginate($params['limit'] ?? 10, ['*'], 'page', $params['page'] ?? 1);
    }

    private function applySearch($query, $search)
    {
        return $query->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%");
            });
        });
    }

    private function applySort($query, $sortBy, $sortOrder)
    {
        return $query->orderBy($sortBy, $sortOrder);
    }
}
