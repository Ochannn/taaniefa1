<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        DB::transaction(function () use ($request) {
            $kodeUser = $this->generateKodeUser();

            $user = User::create([
                'kode_user' => $kodeUser,
                'kode_role' => 'KRL003',
                'nama_user' => $request->username,
                'email_user' => $request->email,
                'pw_user' => $request->password,
            ]);

            Customer::create([
                'kode_customer' => $this->generateKodeCustomer(),
                'kode_user' => $user->kode_user,
                'nama_customer' => $request->username,
                'nohp_customer' => '',
                'alamat_customer' => '',
                'email_customer' => $request->email,
            ]);
        });

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
        $pesananPerluValidasi = \Illuminate\Support\Facades\DB::table('transaksi_penjualan')
            ->where('status_pembayaran', 'Menunggu Validasi')
            ->count();

        $transaksiHariIni = \Illuminate\Support\Facades\DB::table('transaksi_penjualan')
            ->whereDate('tgl_pesanan', now()->toDateString())
            ->count();

        $pengirimanDiproses = \Illuminate\Support\Facades\DB::table('transaksi_penjualan')
            ->whereIn('status_pesanan', ['Diproses', 'Dikirim'])
            ->count();

        $returMasuk = \Illuminate\Support\Facades\DB::table('retur_penjualan')
            ->count();

        return view('home.index', compact(
            'pesananPerluValidasi',
            'transaksiHariIni',
            'pengirimanDiproses',
            'returMasuk'
        ));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function customerProfile()
    {
        $customer = Customer::where('kode_user', Auth::user()->kode_user)->first();

        if (!$customer) {
            abort(404, 'Data customer tidak ditemukan.');
        }

        return view('master.master_customer', compact('customer'));
    }

    public function updateCustomerProfile(Request $request)
    {
        $customer = Customer::where('kode_user', Auth::user()->kode_user)->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Data customer tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'nama_customer' => 'required|string|max:100',
            'nohp_customer' => 'nullable|string|max:20',
            'alamat_customer' => 'nullable|string|max:255',
            'email_customer' => 'required|email|max:100|unique:master_customer,email_customer,' . $customer->kode_customer . ',kode_customer',
        ], [
            'nama_customer.required' => 'Nama customer wajib diisi.',
            'email_customer.required' => 'Email wajib diisi.',
            'email_customer.email' => 'Format email tidak valid.',
            'email_customer.unique' => 'Email sudah digunakan.',
        ]);

        DB::transaction(function () use ($request, $customer) {
            $customer->update([
                'nama_customer' => $request->nama_customer,
                'nohp_customer' => $request->nohp_customer ?? '',
                'alamat_customer' => $request->alamat_customer ?? '',
                'email_customer' => $request->email_customer,
            ]);

            User::where('kode_user', $customer->kode_user)->update([
                'email_user' => $request->email_customer,
            ]);
        });

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Data anda telah tersimpan.'
            ]);
        }

        return back()->with('success', 'Data anda telah tersimpan.');
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

    private function generateKodeCustomer()
    {
        $lastCustomer = Customer::orderBy('kode_customer', 'desc')->first();

        if (!$lastCustomer) {
            return 'KCS001';
        }

        $lastNumber = (int) substr($lastCustomer->kode_customer, 3);
        $newNumber = $lastNumber + 1;

        return 'KCS' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    
}