<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('roles.index', [
            'roles' => Role::withCount('users')->orderBy('name')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('roles.form', ['role' => new Role()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Role::create($this->validated($request));

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role): View
    {
        return view('roles.form', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $role->update($this->validated($request, $role->id));

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['owner', 'superadmin'], true)) {
            return back()->withErrors('Role utama tidak boleh dihapus.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$ignoreId],
            'guard_name' => ['nullable', 'string', 'max:255'],
        ]) + ['guard_name' => 'web'];
    }
}
