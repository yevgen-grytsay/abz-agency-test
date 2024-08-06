<?php

namespace App\Services;

use App\Jobs\TestJob;
use App\Models\Position;
use App\Models\User;

class UserService
{
    public static function createUser(CreateUserDTO $createUserDTO): User
    {
        $user = new User();
        $user->name = $createUserDTO->name;
        $user->email = $createUserDTO->email;
        $user->position()->associate(
            Position::query()
                ->findOrFail($createUserDTO->position_id)
        );
        $user->phone = $createUserDTO->phone;
        $photoPath = $createUserDTO->photo_raw->store('photos');
        $user->photo = 'empty';
        $user->save();

        TestJob::dispatch($user, $photoPath);

        return $user;
    }
}
