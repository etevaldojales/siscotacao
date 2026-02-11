<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use App\Models\Menu;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {

        Paginator::useBootstrap();
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {

            $items = Menu::getMenus()->map(function (Menu $menu) {
                return [
                    'key' => 'menu-' . $menu->id,
                    'text' => $menu->description,
                    'url' => 'admin/'.trim($menu['description']),
                    'active' => ['menu/' . $menu->id . '/*'],
                    'icon' => $menu->icon,
                ];
            });
            
            $event->menu->addIn(
                'dinamic_menus',
                ...$items
            );
            
            //$event->menu->addAfter('dashboard', $items);

        });

    }
}
