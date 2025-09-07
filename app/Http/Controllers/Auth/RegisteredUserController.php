<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create_siswa(): View
    {
        return view('auth.register_siswa');
    }

    public function create_guru(): View
    {
        return view('auth.register_guru');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',

            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'password.min' => 'password minimal 8 karakter.',

            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'no_hp.unique' => 'No Hp sudah terdaftar.',

            'kabupaten.required' => 'Kabupaten wajib diisi.',
            'kota.required' => 'Kota wajib diisi.',
            'instansi.required' => 'Instansi wajib diisi.',
            'role.required' => 'Role wajib dipilih.',
        ];
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', 'min:8'],
            'no_hp' => ['required', 'string', 'max:20', 'unique:' . User::class],
            'kabupaten' => ['required', 'string', 'max:100'],
            'kota' => ['required', 'string', 'max:100'],
            'instansi' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string'],
        ], $messages);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_hp' => $request->no_hp,
            'referral_code' => Str::random(8),
            'kabupaten' => $request->kabupaten,
            'kota' => $request->kota,
            'instansi' => $request->instansi,
            'role' => $request->role,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboardUser', absolute: false));
    }
}
