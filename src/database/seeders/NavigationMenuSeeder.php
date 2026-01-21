<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavigationMenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // 1️⃣ Create menu
            $dashboardId = DB::table('navigation_menu_items')->insertGetId([
                'label' => 'Home',
                'route' => '/',
                'icon' => '',
                'order_index' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2️⃣ Fetch role IDs (FAIL if missing)
            $roles = Role::whereIn('name', [
                RoleEnum::ADMIN->value,
                RoleEnum::USER->value,
                RoleEnum::PUBLIC->value,
            ])->pluck('id', 'name');

            if ($roles->count() !== 3) {
                throw new \Exception('Required roles are missing. Run RoleSeeder first.');
            }

            // 3️⃣ Build pivot data
            $pivotData = $roles->values()->map(fn($roleId) => [
                'menu_id' => $dashboardId,
                'role_id' => $roleId,
            ])->toArray();

            // 4️⃣ Insert pivot rows
            DB::table('navigation_menu_roles')->insert($pivotData);
        });
    }
}
