<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavigationMenuSeeder extends Seeder
{
    public function run(): void
    {
        $dashboardId = DB::table('navigation_menu_items')->insertGetId([
            'label' => 'Home',
            'route' => '/',
            'icon' => '',
            'order_index' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2️⃣ Find the role IDs
        $adminRoleId = Role::where('name', 'ADMIN')->value('id');
        $userRoleId = Role::where('name', 'USER')->value('id');
        $publicRoleId = Role::where('name', 'PUBLIC')->value('id');

        // 3️⃣ Insert into pivot table
        DB::table('navigation_menu_roles')->insert([
            ['menu_id' => $dashboardId, 'role_id' => $adminRoleId],
            ['menu_id' => $dashboardId, 'role_id' => $userRoleId],
            ['menu_id' => $dashboardId, 'role_id' => $publicRoleId],
        ]);
    }
}
