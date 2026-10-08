<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use App\Models\TenantSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cache permission Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat User Admin Utama
        $user = User::create([
            'name' => 'Owner Pilot',
            'email' => 'admin@brandpilot.ai',
            'password' => Hash::make('password123'),
        ]);

        // 3. Buat Brand/Tenant Pertama
        $team = Team::create([
            'name' => 'Fashion Glow Official',
            'slug' => 'fashion-glow',
            'tiktok_handle' => 'fashionglow.id',
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addDays(30),
            'customer_email' => 'admin@brandpilot.ai',
        ]);

        // 4. Hubungkan User dengan Team tersebut
        $user->teams()->attach($team);

        // 5. Buat Default Setting untuk Team
        TenantSetting::create([
            'team_id' => $team->id,
            'schedule_mode' => 'dynamic_delay',
            'dynamic_delay_minutes' => 30,
            'auto_caption_enabled' => true,
            'auto_reply_enabled' => true,
            'brand_knowledge_base' => 'Kami menjual pakaian wanita elegan siap kirim ke seluruh Indonesia.',
        ]);

        // 6. Set Konteks Tenant untuk Spatie & Buat Role 'super_admin'
        setPermissionsTeamId($team->id);

        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
            'team_id' => $team->id,
        ]);

        // 7. Berikan role ke user
        $user->assignRole($superAdminRole);
    }
}
