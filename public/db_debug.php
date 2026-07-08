<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QrisTransaction;
use App\Models\Team;

header('Content-Type: text/plain');

$claimed = QrisTransaction::where('status', 'CLAIMED')->get();
echo "Total CLAIMED: " . count($claimed) . "\n\n";

foreach ($claimed as $tx) {
    echo "====================================\n";
    echo "TX ID: {$tx->trx_id}\n";
    echo "Amount: {$tx->amount}\n";
    echo "Status: {$tx->status}\n";
    echo "Gopay Ref: {$tx->gopay_reference}\n";
    
    $team = Team::where('trx_id', $tx->trx_id)->first();
    if ($team) {
        echo "Team Found: YES\n";
        echo "Team Name: {$team->name}\n";
        echo "Team WA: {$team->wa_number}\n";
        echo "Team Status: {$team->status}\n";
        echo "Season ID: {$team->season_id}\n";
    } else {
        echo "Team Found: NO (Not in teams table)\n";
    }
}
