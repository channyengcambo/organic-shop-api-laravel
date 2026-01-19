<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate([
                'name' => $role->value,
                "slug" => strtolower($role->value),
            ]);
        }
    }
}
