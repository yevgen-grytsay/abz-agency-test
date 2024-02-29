<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function getById(int $id)
    {
        return User::findOrFail($id);
    }

    public function getUsersForApi(int $count): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator $paginator */
        return User::with('position')
            ->orderByDesc('id')
            ->paginate($count);
    }

    public function getUsersForApiWithOffset(int $offset, int $count): Collection
    {
        return User::with('position')
            ->orderByDesc('id')
            ->offset($offset)
            ->limit($count)
            ->get();
    }
}
