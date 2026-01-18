<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function me(?Authenticatable $user): User
    {
        if (!$user) {
            throw new \RuntimeException('User not authenticated');
        }

        return $user->load('roles');
    }

}
