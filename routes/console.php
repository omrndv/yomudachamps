<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Polling transaksi GoPay PENDING setiap menit (mode daemon)
// Schedule::command('qris:poll --daemon')->everyMinute();

// Rekonsiliasi audit GoPay setiap jam
// Schedule::command('qris:reconcile')->hourly();

// Pengingat pendaftaran pending lewat WhatsApp setiap 30 menit
// Schedule::command('qris:recover-abandoned')->everyThirtyMinutes();
