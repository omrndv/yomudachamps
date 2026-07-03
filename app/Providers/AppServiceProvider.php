<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            // Pastikan tabel settings ada sebelum query (mencegah error saat migrate/seed)
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $host = \App\Models\Setting::getVal('mail_host');
                if ($host) {
                    $port = \App\Models\Setting::getVal('mail_port', '587');
                    $username = \App\Models\Setting::getVal('mail_username');
                    $encryptedPass = \App\Models\Setting::getVal('mail_password');
                    $encryption = \App\Models\Setting::getVal('mail_encryption', 'tls');
                    $fromAddress = \App\Models\Setting::getVal('mail_from_address', 'yomudachampionship@gmail.com');
                    $fromName = \App\Models\Setting::getVal('mail_from_name', 'Yomuda Championship');

                    $password = '';
                    if ($encryptedPass) {
                        try {
                            $password = \Illuminate\Support\Facades\Crypt::decryptString($encryptedPass);
                        } catch (\Exception $e) {
                            $password = '';
                        }
                    }

                    config([
                        'mail.mailers.smtp.host' => $host,
                        'mail.mailers.smtp.port' => $port,
                        'mail.mailers.smtp.username' => $username,
                        'mail.mailers.smtp.password' => $password,
                        'mail.mailers.smtp.encryption' => $encryption,
                        'mail.from.address' => $fromAddress,
                        'mail.from.name' => $fromName,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Abaikan jika database belum terkoneksi
        }
    }
}
