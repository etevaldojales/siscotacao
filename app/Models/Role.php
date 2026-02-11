<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * The permissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * The menus that belong to the role.
     */
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'role_menu');
    }

    /**
     * Load menus by role.
     */
    public function loadMenus()
    {
        return $this->menus()->where('actived', 1)->get();
    }
}
