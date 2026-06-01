<style>
    @media (min-width: 992px) {
        body:not(.mini-navbar) .navbar-static-side {
            width: 260px;
        }

        body:not(.mini-navbar) #page-wrapper {
            margin-left: 260px;
        }
    }

    .sidebar-brand-logo {
        width: 100%;
        min-height: 92px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px 14px 12px;
        margin-bottom: 4px;
    }

    .sidebar-brand-logo img {
        width: 168px;
        max-width: 100%;
        max-height: 82px;
        object-fit: contain;
    }

    .profile-element {
        text-align: center;
    }

    .sidebar-brand-name {
        display: block;
        margin-top: 4px;
        font-weight: 700;
        color: #dfe4ed;
    }

    .sidebar-menu-search {
        padding: 14px 18px 10px;
    }

    .sidebar-menu-search .form-control {
        height: 34px;
        border: 0;
        border-radius: 4px;
        background: rgba(255,255,255,.08);
        color: #dfe4ed;
    }

    .sidebar-menu-search .form-control::placeholder {
        color: #9ea6b3;
    }

    .sidebar-menu-search small {
        display: block;
        min-height: 16px;
        margin-top: 6px;
        color: #9ea6b3;
    }

    @media (max-width: 991px) {
        body:not(.mini-navbar) #page-wrapper {
            margin-left: 0;
        }
    }

    @media (max-width: 768px) {
        #page-wrapper,
        body.body-small #page-wrapper,
        body.body-small.mini-navbar #page-wrapper {
            margin-left: 0 !important;
            padding-left: 10px;
            padding-right: 10px;
        }

        .row.border-bottom .navbar {
            min-height: 54px;
        }

        .navbar-header {
            display: flex !important;
            float: none;
            align-items: center;
        }

        .navbar-top-links {
            display: flex;
            justify-content: flex-end;
            width: 100%;
            margin: 0;
        }

        .navbar-top-links li {
            white-space: nowrap;
        }

        .navbar-top-links li:first-child {
            max-width: 44vw;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .navbar-top-links li a,
        .navbar-top-links li button {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }

        body.body-small .navbar-static-side {
            display: block !important;
            width: 260px !important;
            position: fixed !important;
            z-index: 2300;
            top: 0;
            bottom: 0;
            left: -260px;
            overflow-y: auto;
            background: #2f4050;
            box-shadow: 0 0 24px rgba(15, 23, 42, .28);
            transition: left .22s ease;
        }

        body.body-small.mini-navbar .navbar-static-side {
            left: 0;
        }

        body.body-small.mini-navbar:before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .38);
            z-index: 2200;
        }

        body.body-small.mini-navbar .profile-element,
        body.body-small.mini-navbar .nav-label,
        body.body-small.mini-navbar .navbar-default .nav li a span {
            display: inline-block !important;
        }

        body.body-small.mini-navbar .logo-element {
            display: none !important;
        }

        body.body-small.mini-navbar .nav-header {
            padding: 18px 14px !important;
            background-size: cover;
        }

        body.body-small.mini-navbar .navbar-default .nav > li > a {
            font-size: 13px;
            padding: 12px 18px !important;
            display: block;
            min-height: 0;
        }

        body.body-small.mini-navbar .nav > li > a i {
            width: 18px;
            margin-right: 8px;
            text-align: center;
        }

        body.body-small.mini-navbar .nav-second-level {
            position: static !important;
            left: auto !important;
            top: auto !important;
            width: auto !important;
            background: #293846;
            padding: 0;
            box-shadow: none;
        }

        body.body-small.mini-navbar .nav-second-level li a {
            padding: 9px 10px 9px 46px !important;
            width: auto !important;
            display: block !important;
        }

        body.body-small.mini-navbar li.active .nav-second-level {
            display: block;
        }

        .sidebar-brand-logo {
            min-height: 76px;
            padding: 4px 8px 8px;
        }

        .sidebar-brand-logo img {
            width: 172px;
            max-height: 68px;
        }

        .sidebar-menu-search {
            padding: 12px 16px 8px;
        }

        .sidebar-menu-search .form-control {
            background: rgba(255,255,255,.1);
        }
    }
</style>

<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <div class="sidebar-brand-logo">
                        <img src="{{ asset('selowa.webp') }}" alt="Logo Selowa">
                    </div>
                    <span class="sidebar-brand-name">Selowa</span>
                    <span class="text-muted text-xs block">Isi Ulang Air Minum</span>
                </div>
                <div class="logo-element">
                    SW
                </div>
            </li>

            <li class="search-bar sidebar-menu-search">
                <input id="menuSearch" type="text" class="form-control" placeholder="Cari menu...">
                <small id="searchResultCount"></small>
            </li>
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="fa fa-th-large"></i>
                    <span class="nav-label">Dashboard</span>
                </a>
            </li>
            @php
                $user = auth()->user();
                $menuQuery = \App\Models\Menu::with('children')->active()->whereNull('parent_id')->orderBy('sort_order')->orderBy('title');
                $menus = ($user && $user->hasAnyRole(['owner', 'superadmin']))
                    ? $menuQuery->get()
                    : $menuQuery->whereHas('roles', fn ($query) => $query->whereIn('roles.id', $user?->roles->pluck('id') ?? []))->get();
            @endphp

            @foreach ($menus as $menu)
                @php
                    $children = $menu->children->where('is_active', true);
                    if ($user && ! $user->hasAnyRole(['owner', 'superadmin'])) {
                        $roleIds = $user->roles->pluck('id');
                        $children = $children->filter(fn ($child) => $child->roles()->whereIn('roles.id', $roleIds)->exists());
                    }
                    $active = $menu->route_name ? request()->routeIs($menu->route_name) : false;
                    if (! $active && $children->isNotEmpty()) {
                        $active = $children->contains(fn ($child) => $child->route_name && request()->routeIs($child->route_name));
                    }
                @endphp
                <li class="{{ $active ? 'active' : '' }}">
                    <a href="{{ $children->isNotEmpty() ? '#' : $menu->href() }}">
                        <i class="fa {{ $menu->icon ?: 'fa-circle-o' }}"></i>
                        <span class="nav-label">{{ $menu->title }}</span>
                        @if ($children->isNotEmpty()) <span class="fa arrow"></span> @endif
                    </a>
                    @if ($children->isNotEmpty())
                        <ul class="nav nav-second-level">
                            @foreach ($children as $child)
                                <li><a href="{{ $child->href() }}">{{ $child->title }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</nav>
