<ul class="navbar-nav me-auto">
    @foreach ($menus as $menu)
        <li class="nav-item">
            <a class="nav-link" href="{{ url($menu->url ?? '#') }}">
                @if ($menu->icon)
                    <i class="{{ $menu->icon }}"></i>
                @endif
                {{ $menu->description }}
            </a>
        </li>
    @endforeach
</ul>
