<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'channyengcambo@gmail.com'],
            [
                "username" => "channyengcambo",
                'name' => 'Chann Yeng',
                'password' => Hash::make('123456'),
            ]
        );

        $adminRole = Role::where('name', RoleEnum::ADMIN->value)->first();

        $admin->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}

