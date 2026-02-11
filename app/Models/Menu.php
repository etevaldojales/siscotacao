<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    public $table = 'menu';

    protected $fillable = [
        'description',
        'icon',
        'actived',
    ];

    /**
     * The roles that belong to the menu.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_menu');
    }

    public static function getMenus() 
    {
        $user = auth()->user();

        if ($user && $user->isAdmin()) {
            $menus = Menu::where('actived', true)->get();
        } 
        else {
            $menus = Menu::join('role_menu', 'menu.id', '=', 'role_menu.menu_id')
                ->where('role_menu.role_id', 2)
                ->where('menu.actived', true)
                ->select('menu.*')
                ->get();
        }
        return $menus;
    }
}
