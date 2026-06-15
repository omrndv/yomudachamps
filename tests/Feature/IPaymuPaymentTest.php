<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Season;
use App\Models\Team;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class IPaymuPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        
        // Setup settings
        Setting::create(['key' => 'ipaymu_va', 'value' => '0000001991444084']);
        Setting::create(['key' => 'ipaymu_api_key', 'value' => 'SANDBOXDFA2D9F9-F2CE-4A76-8055-FEC1969B0EDB']);
        Setting::create(['key' => 'ipaymu_mode', 'value' => 'sandbox']);
    }

    public function test_callback_fails_with_invalid_signature()
    {
        $season = Season::create([
            'name' => 'Season 10',
            'price' => 100000,
            'slot' => 16,
            'status' => 'ACTIVE',
            'date_info' => '15-20 Juni 2026',
            'wa_link' => 'https://wa.me/test',
        ]);

        $team = Team::create([
            'season_id' => $season->id,
            'trx_id' => 'YMD-999',
            'name' => 'Team Alpha',
            'wa_number' => '08123456789',
            'status' => 'PENDING',
        ]);

        $payload = [
            'trx_id' => '999999',
            'status' => 'berhasil',
            'status_code' => 1,
            'reference_id' => 'YMD-999',
            'amount' => 100000,
        ];

        $response = $this->postJson('/api/ipaymu/callback', $payload, [
            'signature' => 'invalid-signature-here'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => false, 'message' => 'Invalid signature']);
        
        $this->assertEquals('PENDING', $team->fresh()->status);
    }

    public function test_callback_success_with_valid_signature()
    {
        $season = Season::create([
            'name' => 'Season 10',
            'price' => 100000,
            'slot' => 16,
            'status' => 'ACTIVE',
            'date_info' => '15-20 Juni 2026',
            'wa_link' => 'https://wa.me/test',
        ]);

        $team = Team::create([
            'season_id' => $season->id,
            'trx_id' => 'YMD-888',
            'name' => 'Team Beta',
            'wa_number' => '08123456789',
            'status' => 'PENDING',
        ]);

        $payload = [
            'trx_id' => '888888',
            'status' => 'berhasil',
            'status_code' => 1,
            'reference_id' => 'YMD-888',
            'amount' => 100000,
        ];

        $payloadToSign = $payload;
        // Normalisasi tipe data agar identik dengan format iPaymu JSON asli di test
        $payloadToSign['trx_id'] = (int)$payloadToSign['trx_id'];
        $payloadToSign['status_code'] = (int)$payloadToSign['status_code'];
        $payloadToSign['is_escrow'] = false;
        $payloadToSign['additional_info'] = [];
        ksort($payloadToSign);
        
        $jsonPayload = json_encode($payloadToSign);
        
        // Generate valid signature matching iPaymuCallbackController validation
        $va = '0000001991444084';
        $validSignature = hash_hmac('sha256', $jsonPayload, $va);

        $response = $this->postJson('/api/ipaymu/callback', $payload, [
            'signature' => $validSignature
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->assertEquals('PAID', $team->fresh()->status);
    }
}
