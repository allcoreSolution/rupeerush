<?php

namespace Tests\Feature;

use App\Models\AdminRole;
use App\Models\RouletteBet;
use App\Models\RouletteRound;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminBetsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = AdminRole::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Administrator',
                'description' => 'Unrestricted master access',
                'permissions' => array_keys(AdminRole::permissionCatalog()),
                'is_system' => true,
            ]
        );

        $this->admin = User::create([
            'name' => 'Root Admin',
            'username' => 'root_admin',
            'phone' => '9999999999',
            'email' => 'root@example.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
            'admin_role_id' => $role->id,
        ]);
    }

    public function test_admin_can_view_unified_bets_including_roulette(): void
    {
        $player = User::factory()->create(['name' => 'Vijay Player', 'phone' => '9876543210']);

        $round = RouletteRound::create([
            'round_id' => 'ROU_20261028_001',
            'start_time' => now(),
            'end_time' => now()->addSeconds(20),
            'status' => 'completed',
            'winning_number' => 17,
            'winning_color' => 'black',
            'is_even' => false,
            'is_high' => false,
            'dozen' => 2,
            'column' => 2,
            'seed_hash' => hash('sha256', 'seed1'),
            'server_seed' => 'seed1',
            'is_manual' => false,
            'total_bets_count' => 1,
            'total_bet_amount' => 500.00,
            'total_payout_amount' => 18000.00,
        ]);

        RouletteBet::create([
            'round_id' => $round->round_id,
            'user_id' => $player->id,
            'bet_id' => 'BET-ROU-999',
            'bet_type' => 'straight',
            'bet_value' => '17',
            'bet_amount' => 500,
            'odds' => 36.0,
            'payout_amount' => 18000,
            'net_profit' => 17500,
            'status' => 'won',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.bets.index'));

        $response->assertStatus(200);
        $response->assertSee('All Game Bets Monitor');
        $response->assertSee('Roulette 36x');
        $response->assertSee('Straight #17');
        $response->assertSee('Vijay Player');
        $response->assertSee('18,000.00');
    }

    public function test_admin_can_filter_specifically_by_roulette_game(): void
    {
        $player = User::factory()->create(['name' => 'Rahul Sharma']);

        $round = RouletteRound::create([
            'round_id' => 'ROU_20261028_002',
            'start_time' => now(),
            'end_time' => now()->addSeconds(20),
            'status' => 'completed',
            'winning_number' => 7,
            'winning_color' => 'red',
            'is_even' => false,
            'is_high' => false,
            'dozen' => 1,
            'column' => 1,
            'seed_hash' => hash('sha256', 'seed2'),
            'server_seed' => 'seed2',
            'is_manual' => false,
            'total_bets_count' => 1,
            'total_bet_amount' => 1000.00,
            'total_payout_amount' => 2000.00,
        ]);

        RouletteBet::create([
            'round_id' => $round->round_id,
            'user_id' => $player->id,
            'bet_id' => 'BET-ROU-777',
            'bet_type' => 'red',
            'bet_value' => 'red',
            'bet_amount' => 1000,
            'odds' => 2.0,
            'payout_amount' => 2000,
            'net_profit' => 1000,
            'status' => 'won',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.bets.index', ['game' => 'roulette']));

        $response->assertStatus(200);
        $response->assertSee('Roulette 36x');
        $response->assertSee('RED (🔴)');
        $response->assertSee('Rahul Sharma');
        $response->assertSee('1,000.00');
    }

    public function test_roulette_high_amount_filter_and_search_work(): void
    {
        $player = User::factory()->create(['name' => 'VIP Bettor', 'phone' => '9988776655']);

        $round = RouletteRound::create([
            'round_id' => 'ROU_20261028_003',
            'start_time' => now(),
            'end_time' => now()->addSeconds(20),
            'status' => 'completed',
            'winning_number' => 22,
            'winning_color' => 'black',
            'is_even' => true,
            'is_high' => true,
            'dozen' => 2,
            'column' => 1,
            'seed_hash' => hash('sha256', 'seed3'),
            'server_seed' => 'seed3',
            'is_manual' => false,
            'total_bets_count' => 1,
            'total_bet_amount' => 50000.00,
            'total_payout_amount' => 100000.00,
        ]);

        RouletteBet::create([
            'round_id' => $round->round_id,
            'user_id' => $player->id,
            'bet_id' => 'BET-ROU-VIP',
            'bet_type' => 'black',
            'bet_value' => 'black',
            'bet_amount' => 50000,
            'odds' => 2.0,
            'payout_amount' => 100000,
            'net_profit' => 50000,
            'status' => 'won',
        ]);

        // Search by player phone
        $response = $this->actingAs($this->admin)->get(route('admin.bets.index', [
            'game' => 'roulette',
            'search' => '9988776655',
        ]));
        $response->assertStatus(200);
        $response->assertSee('VIP Bettor');
        $response->assertSee('50,000.00');

        // Filter by higher amount
        $responseFilter = $this->actingAs($this->admin)->get(route('admin.bets.index', [
            'game' => 'roulette',
            'filter' => 'higher_amount',
        ]));
        $responseFilter->assertStatus(200);
        $responseFilter->assertSee('50,000.00');
    }

    public function test_admin_bets_pagination_renders_bootstrap_five_properly(): void
    {
        $player = User::factory()->create();

        $round = RouletteRound::create([
            'round_id' => 'ROU_20261028_PAG',
            'start_time' => now(),
            'end_time' => now()->addSeconds(20),
            'status' => 'completed',
            'winning_number' => 10,
            'winning_color' => 'black',
            'seed_hash' => 'hash',
            'server_seed' => 'seed',
        ]);

        // Create 30 bets to trigger pagination (perPage is 25)
        for ($i = 1; $i <= 30; $i++) {
            RouletteBet::create([
                'round_id' => $round->round_id,
                'user_id' => $player->id,
                'bet_id' => 'BET-ROU-PAG-'.$i,
                'bet_type' => 'black',
                'bet_value' => 'black',
                'bet_amount' => 100,
                'odds' => 2.0,
                'status' => 'lost',
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('admin.bets.index'));
        $response->assertStatus(200);
        // Ensure Bootstrap 5 pagination classes are present
        $response->assertSee('pagination');
        $response->assertSee('page-item');
        $response->assertSee('page-link');
        $response->assertSee('Showing');
        $response->assertSee('results');
    }
}
