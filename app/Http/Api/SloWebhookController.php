<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Session;
use Illuminate\Support\Facades\Log;

class SloWebhookController extends Controller
{
    //
    public function handle(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'sso_id' => 'required|string',
            'signature' => 'required|string',
        ]);

        $ssoId = $request->sso_id;
        $signature = $request->signature;

        // Rahasia kita: Satelit menggunakan SSO_CLIENT_SECRET untuk memverifikasi
        // $secret = env('SSO_CLIENT_SECRET');
        $secret = env('SSO_WEBHOOK_SECRET');

        // 2. Verifikasi Tanda Tangan Keamanan (HMAC)
        // Satelit menghitung ulang hash menggunakan sso_id dan secret yang sama
        $expectedSignature = hash_hmac('sha256', $ssoId, $secret);

        // hash_equals digunakan untuk mencegah Timing Attack
        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('SLO Webhook: Verifikasi Signature GAGAL dari IP ' . $request->ip());
            return response()->json(['message' => 'Unauthorized / Invalid Signature'], 401);
        }

        // 3. Cari pengguna lokal berdasarkan sso_id dari Portal
        $user = User::where('sso_id', $ssoId)->first();

        if ($user) {
            // 4. Hancurkan semua sesi aktif milik user ini di Aplikasi Satelit
            Session::where('user_id', $user->id)->delete();

            Log::info("SLO Webhook: Berhasil memutus sesi satelit untuk User ID: {$user->id}");
        } else {
            Log::info("SLO Webhook: Diabaikan. User dengan sso_id {$ssoId} tidak ditemukan di satelit.");
        }

        return response()->json(['message' => 'SLO processed successfully'], 200);
    }
}
