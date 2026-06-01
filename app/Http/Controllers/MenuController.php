<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        return view('menus.index', [
            'menus' => Menu::with('parent')->orderBy('parent_id')->orderBy('sort_order')->orderBy('title')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('menus.form', [
            'menu' => new Menu(['is_active' => true, 'sort_order' => 0]),
            'parents' => Menu::orderBy('title')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Menu::create($this->validated($request));

        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu): View
    {
        return view('menus.form', [
            'menu' => $menu,
            'parents' => Menu::whereKeyNot($menu->id)->orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $menu->update($this->validated($request, $menu));

        return redirect()->route('menus.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus.');
    }

    private function validated(Request $request, ?Menu $menu = null): array
    {
        $data = $request->validate([
            'parent_id' => ['nullable', 'exists:menus,id'],
            'title' => ['required', 'string', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($menu && (int) ($data['parent_id'] ?? 0) === $menu->id) {
            $data['parent_id'] = null;
        }

        return $data + ['is_active' => false, 'sort_order' => 0];
    }
}
