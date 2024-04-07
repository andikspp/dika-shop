<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Buyer;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function registerBuyer(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'nama_lengkap' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
        ]);

        // Membuat user baru
        $user = new User();
        $user->username = $request->input('username'); // Ubah dari 'name' menjadi 'username'
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password')); // Encrypt password
        $user->save();

        // Simpan detail tambahan pembeli
        $buyer = new Buyer();
        $buyer->user_id = $user->id;
        $buyer->nama_lengkap = $request->input('nama_lengkap'); // Ubah dari 'nama_lengkap' menjadi 'nama_lengkap'
        $buyer->nomor_telepon = $request->input('nomor_telepon'); // Ubah dari 'nomor_telepon' menjadi 'nomor_telepon'
        $buyer->save();

        $user->sendEmailVerificationNotification();

        // Redirect ke halaman setelah berhasil mendaftar dengan pesan success
        return redirect()->route('buyer.login')->with('success', 'Registration successful. Please login.');
    }

    public function loginBuyer(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            // Jika proses login berhasil
            $user = Auth::user();
            $buyer = $user->buyer;

            // Periksa apakah buyer ada
            if ($buyer) {
                // Bersihkan session dan regenerasi session ID
                Session::flush();
                Session::regenerate();
                // Simpan user_id dari buyer ke dalam session
                Session::put('id', $buyer->id);
            }
            return redirect()->intended('/home'); // Redirect ke halaman beranda setelah login
        }

        // Jika proses login gagal, kembalikan ke halaman login dengan pesan error
        return redirect()->route('buyer.login')->with('error', 'Invalid credentials. Please try again.');
    }

    public function logoutBuyer(Request $request)
    {
        Auth::logout(); // Proses logout pengguna
        $request->session()->invalidate(); // Menghapus sesi pengguna
        $request->session()->regenerateToken(); // Menghasilkan token sesi baru

        return redirect('/login')->with('success', 'You have been logged out.');
    }
}
