<?php

namespace App\Models\FrontEndPath;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MainHero extends Model
{
    protected $table = 'main_heroes';

    protected $fillable = [
        'title',
        'has_discount',
        'discount_label',
        'discount_content',
        'discount_value',
        'discount_subtitle',
        'order_index',
        'is_active',
        'description',
        'target',
        'image',
        'button_action',
        'total_items'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'main_hero_roles',
            'main_hero_id',
            'role_id'
        );
    }
}
