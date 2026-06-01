<style>
    @media (min-width: 769px) {
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

    @media (max-width: 768px) {
        body:not(.mini-navbar) #page-wrapper,
        body.body-small:not(.mini-navbar) #page-wrapper {
            margin-left: 0;
        }

        body:not(.mini-navbar) .navbar-static-side,
        body.body-small:not(.mini-navbar) .navbar-static-side {
            display: none;
            width: 220px;
            position: fixed;
            z-index: 2001;
            top: 0;
            bottom: 0;
            overflow-y: auto;
        }

        body.mini-navbar .navbar-static-side,
        body.body-small.mini-navbar .navbar-static-side {
            display: block;
            width: 220px;
            position: fixed;
            z-index: 2001;
            top: 0;
            bottom: 0;
            overflow-y: auto;
        }

        body.mini-navbar #page-wrapper,
        body.body-small.mini-navbar #page-wrapper {
            margin-left: 0;
        }

        body.mini-navbar .profile-element,
        body.mini-navbar .nav-label,
        body.mini-navbar .navbar-default .nav li a span {
            display: inline;
        }

        body.mini-navbar .logo-element {
            display: none;
        }

        body.mini-navbar .nav-header {
            padding: 18px 14px;
        }

        body.mini-navbar .navbar-default .nav > li > a {
            font-size: 13px;
            padding: 12px 18px;
        }

        .sidebar-brand-logo {
            min-height: 70px;
            padding: 4px 8px 8px;
        }

        .sidebar-brand-logo img {
            width: 150px;
            max-height: 62px;
        }

        .sidebar-menu-search {
            padding: 10px 14px 8px;
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
