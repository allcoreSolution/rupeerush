<?php

namespace App\Console\Commands;

use App\Services\Sports\CricketEngine;
use Illuminate\Console\Command;

class SyncCricketMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sports:sync-cricket {--force : Bypass cache and force fetch from CricAPI}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and synchronize live & upcoming cricket matches from CricAPI into database';

    /**
     * Execute the console command.
     */
    public function handle(CricketEngine $engine): int
    {
        $this->info('Starting CricAPI cricket matches synchronization...');

        $force = (bool) $this->option('force');

        // Test API diagnostic first
        $diag = $engine->getApiService()->testApiConnection();
        if (! empty($diag['success'])) {
            $this->info("✓ CricAPI Connected successfully in {$diag['latency_seconds']}s");
            $this->line("  Hits Used Today: {$diag['hits_today']} / {$diag['hits_limit']} (Remaining: {$diag['hits_remaining']})");
            $this->line("  Matches in API: {$diag['total_matches_found']}");
        } else {
            $this->warn("⚠ CricAPI warning: {$diag['message']}");
            $this->line('  Continuing with fallback / local simulation mode.');
        }

        $synced = $engine->syncMatches($force);

        $this->info('✓ Matches synchronized into database: '.count($synced));

        $rows = [];
        foreach (array_slice($synced, 0, 10) as $m) {
            $rows[] = [
                $m->id,
                $m->title,
                $m->is_live ? 'LIVE' : strtoupper($m->status),
                $m->score_team_a ?? '-',
                $m->score_team_b ?? '-',
                $m->markets()->count().' markets',
            ];
        }

        if (! empty($rows)) {
            $this->table(['ID', 'Match Title', 'Status', 'Score A', 'Score B', 'Markets'], $rows);
        }

        return self::SUCCESS;
    }
}

