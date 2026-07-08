<?php
// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\QrisTransaction;

$trxId = 'YMD64-3KD5N';
$files = glob(public_path('uploads/proofs/proof_' . $trxId . '_*'));

if (!empty($files)) {
    $filePath = $files[0];
    $filename = basename($filePath);
    $ref = 'PROOFS/' . $filename;
    
    $tx = QrisTransaction::where('trx_id', $trxId)->first();
    if ($tx) {
        $tx->update(['gopay_reference' => $ref]);
        echo "SUCCESS: Restored reference for {$trxId} to {$ref}\n";
    } else {
        echo "ERROR: Transaction not found in database\n";
    }
} else {
    echo "ERROR: No proof file starting with proof_{$trxId}_ found in uploads/proofs/\n";
}
unlink(__FILE__); // Auto delete this script after execution for security
