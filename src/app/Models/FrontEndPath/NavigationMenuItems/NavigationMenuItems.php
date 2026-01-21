<?php

namespace App\Models\FrontEndPath\NavigationMenuItems;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class NavigationMenuItems extends Model
{
    protected $table = 'navigation_menu_items';
    protected $fillable = [
        'parent_id',
        'label',
        'route',
        'icon',
        'order_index',
        'is_active',
        'description',
        'target',
        'image',
        'image_action',
        'total_items'
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order_index');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function getFullRouteAttribute(): string
    {
        if ($this->parent) {
            return rtrim($this->parent->full_route, '/') . '/' . ltrim($this->route, '/');
        }
        return $this->route;
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'navigation_menu_roles',
            'menu_id',
            'role_id'
        );
    }
}
