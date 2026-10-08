<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Services\ComposioService;
use Illuminate\Console\Command;

class CheckComposioTools extends Command
{
    protected $signature = 'brandpilot:check-tools {toolkit=facebook} {--keyword=}';
    protected $description = 'Lihat daftar tool resmi Composio beserta parameter wajibnya';

    public function handle(ComposioService $composio)
    {
        $toolkit = strtolower($this->argument('toolkit'));
        $keyword = strtolower($this->option('keyword') ?? '');

        $team = Team::first();
        if (! $team) {
            $this->error('Belum ada tenant/team di database.');
            return;
        }

        $this->info("Menghubungi Composio API v3.1 untuk toolkit: {$toolkit}...");

        // Panggil endpoint /tools resmi Composio
        $client = invade($composio)->client($team);
        $res = $client->get('/tools', ['toolkit_slug' => $toolkit]);

        if (! $res->successful()) {
            $this->error("Gagal mengambil tools: " . $res->body());
            return;
        }

        $tools = $res->json('items') ?? ($res->json('data') ?? $res->json());

        $rows = [];
        foreach ($tools as $tool) {
            $name = $tool['slug'] ?? ($tool['name'] ?? '');
            $desc = $tool['description'] ?? '';
            $params = array_keys($tool['parameters']['properties'] ?? []);
            $required = $tool['parameters']['required'] ?? [];

            if ($keyword && !str_contains(strtolower($name), $keyword) && !str_contains(strtolower($desc), $keyword)) {
                continue;
            }

            $rows[] = [
                $name,
                implode(', ', $required) ?: '-',
                \Illuminate\Support\Str::limit($desc, 60),
            ];
        }

        $this->table(['Nama Tool', 'Parameter Wajib', 'Deskripsi'], $rows);
    }
}
