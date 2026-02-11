<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class BuildingMenu
{
    use Dispatchable, SerializesModels;

    public $menu;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $menu
     * @return void
     */
    public function __construct($menu)
    {
        $this->menu = $menu;
    }
}
