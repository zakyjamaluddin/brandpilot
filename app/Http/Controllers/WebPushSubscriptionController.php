<?php

namespace App\Http\Controllers;

use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebPushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        // 1. Ambil user dari sesi Auth biasa maupun Filament
        $user = auth()->user() ?? Filament::auth()->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'error'   => 'Sesi login tidak ditemukan. Silakan refresh halaman panel.'
            ], 401);
        }

        $endpoint = $request->input('endpoint');
        $key      = $request->input('keys.p256dh');
        $token    = $request->input('keys.auth');

        if (! $endpoint || ! $key || ! $token) {
            return response()->json([
                'success' => false,
                'error'   => 'Data subscription dari browser tidak lengkap.'
            ], 422);
        }

        try {
            $user->updatePushSubscription($endpoint, $key, $token);
            Log::info("WebPush: Sukses menyimpan token perangkat untuk User: {$user->email}");

            return response()->json([
                'success' => true,
                'message' => 'Perangkat berhasil terdaftar.'
            ], 200);

        } catch (\Throwable $e) {
            Log::error("WebPush DB Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => 'Gagal menyimpan ke database: ' . $e->getMessage()
            ], 500);
        }
    }
}