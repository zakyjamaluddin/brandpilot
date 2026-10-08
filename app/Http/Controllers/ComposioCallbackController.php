<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\SocialIntegration;
use Illuminate\Http\Request;
use App\Services\ComposioService;

class ComposioCallbackController extends Controller
{
    public function handle(Request $request, Team $team, string $platform, ComposioService $composio)
    {
        // 1. Tangkap ID dari query param (mendukung format camelCase maupun snake_case)
        $connectedAccountId = $request->query('connectedAccountId')
            ?? $request->query('connected_account_id');

        // Jika ID ada di query parameter, langsung simpan seketika
        if ($connectedAccountId) {
            SocialIntegration::updateOrCreate(
                [
                    'team_id'  => $team->id,
                    'platform' => $platform,
                ],
                [
                    'composio_connection_id' => $connectedAccountId,
                    'account_name'           => ucfirst($platform) . ' Connected',
                    'is_connected'           => true,
                ]
            );
        }

        // 2. Lakukan sinkronisasi resmi ke API Composio untuk konfirmasi status aktif
        $composio->syncConnectedAccounts($team);

        return redirect('/admin/' . $team->slug . '/manage-integrations')
            ->with('notify', ucfirst($platform) . ' berhasil terhubung!');
    }
}
