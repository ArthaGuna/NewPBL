<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman registrasi.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Proses pendaftaran pengguna.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => 'required|string|max:15',
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // Generate kode verifikasi (6 digit angka acak)
        $verificationCode = rand(100000, 999999);

        // Tentukan waktu kedaluwarsa kode verifikasi
        $expirationTime = Carbon::now('Asia/Makassar')->addMinutes(30);

        // Buat pengguna baru
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'email_verification_code' => $verificationCode,
            'email_verification_expires_at' => $expirationTime, // Simpan di database
        ]);

        // Kirim kode verifikasi melalui email
        try {
            Mail::to($user->email)->send(new VerificationEmail(
                $verificationCode, 
                $user->name, 
                $expirationTime->format('H:i') . ' WITA' // Format waktu
            ));
        } catch (\Exception $e) {
            // Jika email gagal dikirim, hapus pengguna dan beri notifikasi
            $user->delete();
            return redirect()->back()->withErrors(['email' => 'Gagal mengirim email verifikasi. Silakan coba lagi.']);
        }

        // Simpan ID pengguna di sesi untuk proses verifikasi
        session(['user_id' => $user->id]);

        // Trigger event registrasi (opsional, jika menggunakan event listener)
        event(new Registered($user));

        // Arahkan ke halaman input kode verifikasi
        return redirect()->route('verification.input')->with('success', 'Kode verifikasi telah dikirim ke email Anda.');
    }
}
