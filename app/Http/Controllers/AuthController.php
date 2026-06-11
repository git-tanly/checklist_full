<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function redirect(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('state', $state);

        $query = http_build_query([
            'client_id' => env('SSO_CLIENT_ID'),
            'redirect_uri' => env('SSO_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
        ]);

        return redirect(env('SSO_PORTAL_URL') . '/oauth/authorize?' . $query);
    }

    public function callback(Request $request)
    {
        // 1. Validasi State untuk mencegah CSRF
        $state = $request->session()->pull('state');
        if (strlen($state) > 0 && $state !== $request->state) {
            return redirect('/')->withErrors(['error' => 'State mismatch. Silakan coba login kembali.']);
        }

        // 2. Tukar Authorization Code dengan Access Token
        $response = Http::asForm()->post(env('SSO_PORTAL_URL') . '/api/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => env('SSO_CLIENT_ID'),
            'client_secret' => env('SSO_CLIENT_SECRET'),
            'redirect_uri' => env('SSO_REDIRECT_URI'),
            'code' => $request->code,
        ]);

        if ($response->failed()) {
            return redirect('/')->withErrors(['error' => 'Gagal mendapatkan token akses dari Portal SSO.']);
        }

        $accessToken = $response->json()['access_token'];

        // 3. Ambil Data Profil User dari Portal SSO
        $userResponse = Http::withToken($accessToken)
                            ->acceptJson()
                            ->get(env('SSO_PORTAL_URL') . '/api/user');

        if ($userResponse->failed()) {
            return redirect('/')->withErrors(['error' => 'Gagal mengambil data profil dari Portal SSO.']);
        }

        $ssoUser = $userResponse->json();
        $ssoData = isset($ssoUser['data']) ? $ssoUser['data'] : $ssoUser;

        // ========================================================
        // 4. LOGIKA JIT PROVISIONING (Otomatisasi Akun Baru)
        // ========================================================
        $user = User::where('email', $ssoData['email'])->first();
        $passwordHash = $ssoData['password'] ?? null;

        if (!$user) {
            // A. CREATE USER BARU
            $user = User::create([
                'sso_id'   => $ssoData['id'],
                'name'     => $ssoData['name'],
                'email'    => $ssoData['email'],
                'password' => $passwordHash,
            ]);

            // B. ASSIGN ROLE DEFAULT: Daily Worker
            // Kita bungkus dalam try-catch berjaga-jaga jika role belum ada di DB
            try {
                $user->assignRole('Daily Worker');
            } catch (\Exception $e) {
                // Abaikan jika role belum di-seed ke DB
            }

            // C. ASSIGN RESTAURANT DEFAULT: 209 Dining
            $defaultRestaurant = Restaurant::where('name', '209 Dining')->first();
            if ($defaultRestaurant) {
                $user->restaurants()->attach($defaultRestaurant->id);
            }

        } else {
            // JIKA USER SUDAH ADA: Update data dasar (tanpa menyentuh Role/Restoran)
            $user->update([
                'sso_id'   => $ssoData['id'],
                'name'     => $ssoData['name'],
                'password' => $passwordHash,
            ]);
        }

        // 5. Login Lokal & Buat Sesi
        Auth::login($user);
        $request->session()->save(); 

        // 6. Redirect ke halaman tujuan atau ke halaman utama Dashboard
        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(env('SSO_PORTAL_URL') . '/logout');
    }
}