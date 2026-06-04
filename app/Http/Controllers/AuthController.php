<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showCover()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('cover');
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            if($user->role == 'admin') {
                return redirect()->route('dashboard')->with('success', 'Selamat datang Admin!');
            } elseif($user->role == 'bendahara') {
                return redirect()->route('dashboard')->with('success', 'Selamat datang Bendahara!');
            } else {
                return redirect()->route('dashboard')->with('success', 'Selamat datang Warga!');
            }
        }
        
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }
    
    public function showRegister()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'house_number' => 'required|string|max:10',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'warga',
            'rt_number' => '001',
            'house_number' => $request->house_number,
            'phone' => $request->phone,
            'address' => $request->address,
            'status_rumah' => $request->status_rumah ?? 'milik_sendiri',
        ]);
        
        Auth::login($user);
        
        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di RT 001');
    }
    
    public function showProfile()
    {
        return view('auth.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'house_number' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'status_rumah' => 'nullable|string|max:50',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'house_number' => $request->house_number,
            'phone' => $request->phone,
            'address' => $request->address,
            'status_rumah' => $request->status_rumah,
        ];

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $photo = $request->file('profile_photo');
            $path = $photo->storeAs('profile_photos', time() . '_' . $user->id . '.' . $photo->extension(), 'public');
            $data['profile_photo'] = $path;
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    public function makeAdmin(Request $request)
    {
        $user = Auth::user();
        $user->role = 'admin';
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Akun Anda sekarang sudah menjadi admin.');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda telah logout');
    }
}