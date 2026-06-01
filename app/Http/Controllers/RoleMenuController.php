<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleMenuController extends Controller
{
    public function index(Request $request): View
    {
        $roles = Role::orderBy('name')->get();
        $role = Role::with('permissions')->find($request->integer('role_id')) ?? $roles->first();

        return view('role-menus.index', [
            'roles' => $roles,
            'role' => $role,
            'menus' => Menu::with('children.children')->whereNull('parent_id')->orderBy('sort_order')->orderBy('title')->get(),
            'selectedMenus' => $role ? $role->menus()->pluck('menus.id')->all() : [],
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $menuIds = $request->validate([
            'menus' => ['array'],
            'menus.*' => ['exists:menus,id'],
        ])['menus'] ?? [];

        $role->menus()->sync($menuIds);

        return redirect()->route('role-menus.index', ['role_id' => $role->id])->with('success', 'Akses menu berhasil diperbarui.');
    }
}
