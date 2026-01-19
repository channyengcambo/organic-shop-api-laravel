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
        'is_active'
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order_index');
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
