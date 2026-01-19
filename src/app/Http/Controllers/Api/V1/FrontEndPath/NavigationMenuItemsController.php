<?php

namespace App\Http\Controllers\Api\V1\FrontEndPath;

use App\Http\Controllers\Controller;
use App\Models\FrontEndPath\NavigationMenuItems\NavigationMenuItems;
use Illuminate\Support\Facades\Auth;

class NavigationMenuItemsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1️⃣ Default roles = PUBLIC
        $roleNames = ['PUBLIC'];

        // 2️⃣ If user logged in → add user roles
        if ($user && $user->roles) {
            $roleNames = array_unique(array_merge(
                $roleNames,
                $user->roles->pluck('name')->toArray()
            ));
        }

        // 3️⃣ Query menu
        $menus = NavigationMenuItems::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->whereHas('roles', function ($q) use ($roleNames) {
                $q->whereIn('name', $roleNames);
            })
            ->with(['children' => function ($q) use ($roleNames) {
                $q->where('is_active', true)
                    ->whereHas('roles', function ($q2) use ($roleNames) {
                        $q2->whereIn('name', $roleNames);
                    })
                    ->orderBy('order_index');
            }])
            ->orderBy('order_index')
            ->get();

        return response()->json($menus);
    }

}
