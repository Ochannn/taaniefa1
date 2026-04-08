<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100|unique:master_user,nama_user',
            'email' => 'required|email|max:100|unique:master_user,email_user',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $kodeUser = $this->generateKodeUser();

        User::create([
            'kode_user' => $kodeUser,
            'kode_role' => 'KRL003',
            'nama_user' => $request->username,
            'email_user' => $request->email,
            'pw_user' => $request->password,
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('nama_user', $request->username)->first();

        if ($user && $request->password === $user->pw_user) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('home');
        }

        return back()->withErrors([
            'loginError' => 'Username atau password salah.',
        ])->withInput();
    }

    public function home()
    {
        return view('home.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function generateKodeUser()
    {
        $lastUser = User::orderBy('kode_user', 'desc')->first();

        if (!$lastUser) {
            return 'KUSR001';
        }

        $lastNumber = (int) substr($lastUser->kode_user, 4);
        $newNumber = $lastNumber + 1;

        return 'KUSR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}