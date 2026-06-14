<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login_and_is_redirected_to_dashboard()
    {
        $response = $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'yomuda123'
        ]);

        $response->assertRedirect(route('admin.dashboard.home'));
        $this->assertTrue(session('admin_logged_in'));
    }

    public function test_authenticated_admin_can_view_dashboard()
    {
        // Create an active season and some teams
        $season = Season::create([
            'name' => 'Season Test',
            'price' => 50000,
            'slot' => 32,
            'status' => 'ACTIVE',
            'date_info' => '15-20 Juni 2026',
            'wa_link' => 'https://wa.me/test',
        ]);

        Team::create([
            'season_id' => $season->id,
            'trx_id' => 'YMD-001',
            'name' => 'Team Alpha',
            'wa_number' => '08123456789',
            'status' => 'PAID',
            'tripay_reference' => 'REF123',
            'payment_method' => 'QRIS',
        ]);

        Team::create([
            'season_id' => $season->id,
            'trx_id' => 'YMD-002',
            'name' => 'Team Beta',
            'wa_number' => '08123456780',
            'status' => 'PENDING',
            'tripay_reference' => 'REF124',
            'payment_method' => 'QRIS',
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Overview');
        $response->assertSee('Keterisian Slot Turnamen');
        $response->assertSee('Season Test');
        $response->assertSee('Team Alpha');
        $response->assertSee('50.000');
    }

    public function test_guest_cannot_access_teams_directory()
    {
        $response = $this->get('/admin/teams');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_can_view_teams_directory_with_history()
    {
        $season1 = Season::create([
            'name' => 'Season A',
            'price' => 25000,
            'slot' => 16,
            'status' => 'FINISHED',
            'date_info' => '1-5 Jan 2026',
        ]);

        $season2 = Season::create([
            'name' => 'Season B',
            'price' => 30000,
            'slot' => 16,
            'status' => 'ACTIVE',
            'date_info' => '10-15 Jan 2026',
        ]);

        // Same team (same WA number) registers in both seasons
        Team::create([
            'season_id' => $season1->id,
            'trx_id' => 'YMD-S1',
            'name' => 'Royal Esport',
            'wa_number' => '08999999999',
            'status' => 'PAID',
        ]);

        Team::create([
            'season_id' => $season2->id,
            'trx_id' => 'YMD-S2',
            'name' => 'Royal Squad',
            'wa_number' => '08999999999',
            'status' => 'PAID',
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->get('/admin/teams');

        $response->assertStatus(200);
        $response->assertSee('Direktori Tim Global');
        $response->assertSee('Royal Squad');
        $response->assertSee('LOYALTY (2)');
    }
}
