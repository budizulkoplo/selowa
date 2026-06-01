<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'photo' => ['nullable', 'image', 'max:2048'],
            'current_password' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        if ($request->filled('password')) {
            if (! Hash::check((string) $request->current_password, (string) $user->password)) {
                return back()->withErrors('Password lama tidak sesuai.');
            }

            $data['password'] = $request->password;
        } else {
            unset($data['password']);
        }

        unset($data['current_password'], $data['photo_confirmation']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profile berhasil diperbarui.');
    }
}
