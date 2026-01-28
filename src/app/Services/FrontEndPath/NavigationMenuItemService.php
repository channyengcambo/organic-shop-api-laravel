<?php

namespace App\Services\FrontEndPath;

use App\Models\FrontEndPath\NavigationMenuItems\NavigationMenuItems;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;

class NavigationMenuItemService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    private function buildFullPath(string $label, ?NavigationMenuItems $parent): string
    {
        $slug = Str::slug($label);

        if (!$parent) {
            return '/' . $slug;
        }

        return rtrim($parent->full_path, '/') . '/' . $slug;
    }

//    Public

    public function getMenusForUser(?User $user): Collection
    {
        return Cache::remember(
            'public_navigation_menu',
            now()->addMinutes(10),
            fn() => $this->loadPublicMenus($user)
        );
    }

    protected function loadPublicMenus(?User $user): Collection
    {
        // 1️⃣ Default role
        $roleNames = ['PUBLIC'];

        // 2️⃣ Add user roles if logged in
        if ($user && ($user->relationLoaded('roles') || $user?->roles)) {
            $roleNames = array_unique(array_merge(
                $roleNames,
                $user->roles->pluck('name')->toArray()
            ));
        }

        return NavigationMenuItems::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->whereHas('roles', fn($q) => $q->whereIn('name', $roleNames))
            ->with(['childrenRecursive' => fn($q) => $q->where('is_active', true)
                ->whereHas('roles', fn($q2) => $q2->whereIn('name', $roleNames))
                ->orderBy('order_index')
            ])
            ->orderBy('order_index')
            ->get();
    }

    /**
     * Create menu item
     * @throws \Throwable
     */
    public function create(array $data): NavigationMenuItems
    {
        if (!empty($data['parent_id']) && $data['parent_id'] === ($data['id'] ?? null)) {
            throw new LogicException('Menu cannot be its own parent');
        }

        return DB::transaction(function () use ($data) {

            // 1️⃣ Validate parent (if exists)
            $parent = null;
            if (!empty($data['parent_id'])) {
                $parent = NavigationMenuItems::lockForUpdate()
                    ->findOrFail($data['parent_id']);
            }

            $route = '/' . Str::slug($data['label']);
            // 2️⃣ Create menu (without full_path first)
            $menu = NavigationMenuItems::create([
                'label' => $data['label'],
                'route' => $route,
                'icon' => $data['icon'] ?? '',
                'order_index' => $data['order_index'],
                'is_active' => $data['is_active'],
                'parent_id' => $parent?->id,
                'description' => $data['description'] ?? null,
                'target' => $data['target'] ?? '_self',
                'image' => $data['image'] ?? null,
                'image_action' => $data['image_action'] ?? null,
                'total_items' => $data['total_items'] ?? 0,
            ]);

            // 3️⃣ Build full_path (single logic)
            $menu->full_path = $this->buildFullPath(
                $menu->label,
                $parent
            );

            $menu->save();

            // 4️⃣ Attach roles
            if (!empty($data['role_ids'])) {
                $menu->roles()->sync($data['role_ids']);
            }

            $this->clearPublicMenuCache();
            return $menu->load('roles');
        });
    }

    /**
     * Create child navigation menu items
     */
    public function createChild(int $parentId, array $data): NavigationMenuItems
    {
        $data['parent_id'] = $parentId;
        return $this->create($data);
    }

    /**
     * Update menu item
     * @throws \Throwable
     */
    public function update(int $id, array $data): NavigationMenuItems
    {
        return DB::transaction(function () use ($id, $data) {

            $menu = NavigationMenuItems::findOrFail($id);

            $menu->update([
                'label' => $data['label'] ?? $menu->label,
                'route' => $data['route'] ?? $menu->route,
                'icon' => $data['icon'] ?? $menu->icon,
                'parent_id' => $data['parent_id'] ?? $menu->parent_id,
                'order_index' => $data['order_index'] ?? $menu->order_index,
                'is_active' => $data['is_active'] ?? $menu->is_active,
                'description' => $data['description'] ?? $menu->description,
                'target' => $data['target'] ?? $menu->target,
                'image' => $data['image'] ?? $menu->image,
                'image_action' => $data['image_action'] ?? $menu->image_action,
                'total_items' => $data['total_items'] ?? $menu->total_items,
            ]);

            // Recompute full_path
            if ($menu->parent_id) {
                $parent = NavigationMenuItems::find($menu->parent_id);
                $menu->full_path = rtrim($parent->full_path, '/') . '/' . ltrim($menu->route ?? '', '/');
            } else {
                $menu->full_path = $menu->route;
            }
            $menu->save();

            // Update all children recursively
            $this->updateChildrenFullPath($menu);

            if (array_key_exists('role_ids', $data)) {
                $menu->roles()->sync($data['role_ids']);
            }

            $this->clearPublicMenuCache();
            return $menu;
        });
    }

    /**
     * Delete menu item
     * @throws \Throwable
     */
    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $menu = NavigationMenuItems::findOrFail($id);

            // Delete all children recursively
            $menu->childrenRecursive()->each(fn($child) => $child->delete());

            $menu->roles()->detach();
            $menu->delete();
        });

        $this->clearPublicMenuCache();
    }

    /**
     * Recursively update children's full_path
     */
    private function updateChildrenFullPath(NavigationMenuItems $menu): void
    {
        foreach ($menu->children as $child) {
            $child->full_path = rtrim($menu->full_path, '/') . '/' . ltrim($child->route ?? '', '/');
            $child->save();
            $this->updateChildrenFullPath($child);
        }

        $this->clearPublicMenuCache();
    }

    private function clearPublicMenuCache(): void
    {
        Cache::forget('public_navigation_menu');
    }
}
