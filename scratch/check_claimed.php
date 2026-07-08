<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QrisTransaction;
use App\Models\Team;

$claimed = QrisTransaction::where('status', 'CLAIMED')->get();
foreach ($claimed as $tx) {
    $team = Team::where('trx_id', $tx->trx_id)->first();
    echo "TX ID: {$tx->trx_id}, Amount: {$tx->amount}, Team Exists: " . ($team ? "YES ({$team->name})" : "NO") . "\n";
}
