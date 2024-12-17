<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => ['required', 'string', 'regex:/^[0-9]{10,13}$/', 'unique:users'],
            'password' => 'required|string|min:6|confirmed',
        ], [
            'phone_number.regex' => 'Format nomor WhatsApp tidak valid. Masukkan 10-13 digit angka tanpa karakter khusus.',
            'phone_number.unique' => 'Nomor WhatsApp sudah terdaftar.',
        ]);

        // Format phone number to ensure it starts with "62"
        $phone_number = $request->phone_number;
        if (substr($phone_number, 0, 2) !== '62') {
            $phone_number = '62' . ltrim($phone_number, '0');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $phone_number,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
} 
