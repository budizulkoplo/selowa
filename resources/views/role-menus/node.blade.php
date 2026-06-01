<div class="menu-node" style="margin-left: {{ $level * 22 }}px">
    <label>
        <input type="checkbox" name="menus[]" value="{{ $menu->id }}" @checked(in_array($menu->id, $selectedMenus, true))>
        <i class="fa {{ $menu->icon ?: 'fa-circle-o' }}"></i> {{ $menu->title }}
    </label>
    @foreach ($menu->children as $child)
        @include('role-menus.node', ['menu' => $child, 'selectedMenus' => $selectedMenus, 'level' => $level + 1])
    @endforeach
</div>
