<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripayCallbackController;
use App\Http\Controllers\IPaymuCallbackController;

Route::get('/payment/{trx_id}', [HomeController::class, 'paymentConfirm'])->name('payment.confirm');
Route::post('/payment/{id}/checkout', [HomeController::class, 'checkout'])->name('payment.checkout');
Route::get('/payment/detail/{trx_id}', [HomeController::class, 'paymentDetail'])->name('payment.detail');
Route::get('/sertifikat', [HomeController::class, 'redirectCertificate'])->name('public.certificate.redirect');
Route::get('/sertifikat/{season_slug}', [HomeController::class, 'redirectCertificateBySlug'])->name('public.certificate.redirect_slug');

Route::get('/storage/{any}', function ($any) {
    $path = storage_path('app/public/' . urldecode($any));
    if (!file_exists($path) || is_dir($path)) {
        abort(404);
    }
    $file = file_get_contents($path);
    $type = mime_content_type($path);
    return response($file, 200)
        ->header('Content-Type', $type)
        ->header('Cache-Control', 'public, max-age=31536000');
})->where('any', '.*');

Route::get('/run-symlink', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');
    
    if (file_exists($link)) {
        return "Link/folder already exists at: " . $link . ". Please delete it first if it is broken.";
    }
    
    // Method 1: Native PHP symlink
    if (function_exists('symlink')) {
        try {
            if (symlink($target, $link)) {
                return "Symlink created successfully using native PHP symlink()!";
            }
        } catch (\Throwable $e) {
            // Proceed to other methods
        }
    }
    
    // Method 2: shell_exec (ln -s)
    if (function_exists('shell_exec')) {
        try {
            $output = shell_exec("ln -s " . escapeshellarg($target) . " " . escapeshellarg($link));
            if (file_exists($link)) {
                return "Symlink created successfully using shell_exec('ln -s')! Output: " . $output;
            }
        } catch (\Throwable $e) {
            // Proceed to other methods
        }
    }
    
    // Method 3: exec (ln -s)
    if (function_exists('exec')) {
        try {
            $output = [];
            $resultCode = null;
            exec("ln -s " . escapeshellarg($target) . " " . escapeshellarg($link), $output, $resultCode);
            if (file_exists($link)) {
                return "Symlink created successfully using exec('ln -s')!";
            }
        } catch (\Throwable $e) {
            // Proceed to other methods
        }
    }
    
    // Method 4: system (ln -s)
    if (function_exists('system')) {
        try {
            ob_start();
            $result = system("ln -s " . escapeshellarg($target) . " " . escapeshellarg($link));
            ob_end_clean();
            if (file_exists($link)) {
                return "Symlink created successfully using system('ln -s')!";
            }
        } catch (\Throwable $e) {
            // Proceed to other methods
        }
    }

    return "Failed to create symlink. All methods (native symlink, shell_exec, exec, system) are either disabled or failed.";
});

Route::get('/storage/posters/{filename}', function ($filename) {
    $path = storage_path('app/public/posters/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    $file = file_get_contents($path);
    $type = mime_content_type($path);
    
    return response($file)->header('Content-Type', $type);
});

Route::get('/payment/check-ajax/{trx_id}', [App\Http\Controllers\HomeController::class, 'checkStatusAjax'])
    ->name('payment.check.status');
    
Route::post('/api/callback', [TripayCallbackController::class, 'handle']);
Route::post('/api/ipaymu/callback', [IPaymuCallbackController::class, 'handle']);

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/daftar', [HomeController::class, 'registerForm'])->name('register.form');
Route::get('/daftar-team', [HomeController::class, 'registerTripayForm'])->name('register.tripay');
Route::post('/register/store', [HomeController::class, 'storeRegistration'])->middleware('throttle:6,1')->name('register.store');
Route::get('/success/{trx_id}', [HomeController::class, 'successPage'])->name('payment.success');

Route::get('/download-qris', [HomeController::class, 'downloadQris'])->name('qris.download');

Route::get('/cek-tim', [HomeController::class, 'checkPage'])->name('check.team');
Route::post('/cek-tim', [HomeController::class, 'searchTeam'])->middleware('throttle:15,1')->name('check.team.search');

Route::get('/faq', [HomeController::class, 'faqs'])->name('faq.index');


Route::post('/ai-chat', [HomeController::class, 'aiChat'])->name('ai.chat');

Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'authenticate'])->name('admin.login.post');
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::middleware('admin.auth')->group(function () {
    Route::post('/admin/bulk-store/{season_id}', [AdminController::class, 'bulkStore'])->name('admin.bulk.store');
    Route::get('/admin/team/delete/{id}', [AdminController::class, 'deleteTeam'])->name('admin.team.delete');
    Route::get('/admin/team/delete-all/{season_id}', [AdminController::class, 'deleteAllTeams'])->name('admin.team.deleteAll');
    Route::post('/admin/seasons/store', [AdminController::class, 'storeSeason'])->name('admin.seasons.store');
    Route::post('/admin/seasons/update/{id}', [AdminController::class, 'updateSeason'])->name('admin.seasons.update');
    Route::get('/admin/seasons/delete/{id}', [AdminController::class, 'deleteSeason'])->name('admin.seasons.delete');
    Route::post('/admin/team/update/{id}', [AdminController::class, 'updateTeam'])->name('admin.team.update');
    Route::post('/admin/team/bulk-delete', [AdminController::class, 'bulkDelete'])->name('admin.team.bulkDelete');
    Route::get('/admin/check-new-payments', [AdminController::class, 'checkNewPayments'])->name('admin.payments.check-new');
    
    Route::middleware('permission:payments')->group(function () {
        Route::get('/admin/payments/sync', [AdminController::class, 'syncPayments'])->name('admin.payments.sync');
    });

    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboardHome'])->name('admin.dashboard.home');
        Route::get('/ai-recap-winners', [AdminController::class, 'aiRecapWinners'])->name('admin.ai.recap_winners');
        Route::post('/ai-chat', [AdminController::class, 'adminChatWithAI'])->name('admin.ai.chat');
        
        // Seasons
        Route::middleware('permission:seasons')->group(function () {
            Route::get('/seasons', [AdminController::class, 'seasons'])->name('admin.seasons');
            Route::get('/dashboard/{season_id}', [AdminController::class, 'dashboard'])->name('admin.dashboard');
            
            // Bracket management routes
            Route::get('/dashboard/{season_id}/bracket', [\App\Http\Controllers\BracketController::class, 'manageBracket'])->name('admin.season.bracket');
            Route::post('/dashboard/{season_id}/bracket/generate', [\App\Http\Controllers\BracketController::class, 'generateBracket'])->name('admin.season.bracket.generate');
            Route::post('/dashboard/{season_id}/bracket/update-match', [\App\Http\Controllers\BracketController::class, 'updateMatch'])->name('admin.season.bracket.update-match');
            Route::post('/dashboard/{season_id}/bracket/update-round-times', [\App\Http\Controllers\BracketController::class, 'updateRoundTimes'])->name('admin.season.bracket.update-round-times');
            Route::post('/dashboard/{season_id}/bracket/swap-teams', [\App\Http\Controllers\BracketController::class, 'swapTeams'])->name('admin.season.bracket.swap-teams');
            Route::post('/dashboard/{season_id}/bracket/add-ymd-slots', [\App\Http\Controllers\BracketController::class, 'addYmdSlots'])->name('admin.season.bracket.add-ymd-slots');
            Route::post('/dashboard/{season_id}/bracket/rename-ymd-slot', [\App\Http\Controllers\BracketController::class, 'renameYmdSlot'])->name('admin.season.bracket.rename-ymd-slot');
            Route::post('/dashboard/{season_id}/bracket/delete-all-ymd-slots', [\App\Http\Controllers\BracketController::class, 'deleteAllYmdSlots'])->name('admin.season.bracket.delete-all-ymd-slots');
            Route::post('/dashboard/{season_id}/bracket/toggle-bronze-match', [\App\Http\Controllers\BracketController::class, 'toggleBronzeMatch'])->name('admin.season.bracket.toggle-bronze-match');
            Route::post('/dashboard/{season_id}/bracket/toggle-visibility', [\App\Http\Controllers\BracketController::class, 'toggleBracketVisibility'])->name('admin.season.bracket.toggle-visibility');

            // Match Reports Admin routes
            Route::get('/dashboard/{season_id}/match-reports', [\App\Http\Controllers\BracketController::class, 'adminMatchReports'])->name('admin.season.match-reports');
            Route::post('/match-report/approve/{id}', [\App\Http\Controllers\BracketController::class, 'approveMatchReport'])->name('admin.match-report.approve');
            Route::post('/match-report/reject/{id}', [\App\Http\Controllers\BracketController::class, 'rejectMatchReport'])->name('admin.match-report.reject');
            Route::post('/match-report/rollback/{id}', [\App\Http\Controllers\BracketController::class, 'rollbackMatchReport'])->name('admin.match-report.rollback');
            Route::post('/dashboard/{season_id}/match-reports/clear-all', [\App\Http\Controllers\BracketController::class, 'clearAllMatchReports'])->name('admin.season.match-reports.clear-all');
            Route::get('/dashboard/{season_id}/match-reports/poll', [\App\Http\Controllers\BracketController::class, 'pollMatchReports'])->name('admin.season.match-reports.poll');
            
            // Admin Live Chat routes (real-time chat management)
            Route::get('/dashboard/{season_id}/chat/threads', [\App\Http\Controllers\BracketController::class, 'getChatThreads'])->name('admin.season.chat.threads');
            Route::get('/dashboard/{season_id}/chat/messages/{token}', [\App\Http\Controllers\BracketController::class, 'getThreadMessages'])->name('admin.season.chat.thread-messages');
            Route::post('/dashboard/{season_id}/chat/reply', [\App\Http\Controllers\BracketController::class, 'replyChatMessage'])->name('admin.season.chat.reply');
            Route::post('/dashboard/{season_id}/chat/read/{token}', [\App\Http\Controllers\BracketController::class, 'markThreadAsRead'])->name('admin.season.chat.read');
            Route::delete('/dashboard/{season_id}/chat/delete/{token}', [\App\Http\Controllers\BracketController::class, 'deleteChatThread'])->name('admin.season.chat.delete');
            Route::post('/dashboard/{season_id}/chat/archive/{token}', [\App\Http\Controllers\BracketController::class, 'archiveChatThread'])->name('admin.season.chat.archive');
            Route::delete('/dashboard/{season_id}/chat/clear-all', [\App\Http\Controllers\BracketController::class, 'clearAllSeasonChats'])->name('admin.season.chat.clear-all');
            Route::post('/dashboard/{season_id}/chat/unarchive/{token}', [\App\Http\Controllers\BracketController::class, 'unarchiveChatThread'])->name('admin.season.chat.unarchive');
            Route::post('/dashboard/{season_id}/chat/upload/{token}', [\App\Http\Controllers\BracketController::class, 'adminUploadChatImage'])->name('admin.season.chat.upload');

            // Certificate Generator routes
            Route::get('/dashboard/{season_id}/certificate', [\App\Http\Controllers\CertificateController::class, 'index'])->name('admin.season.certificate');
            Route::post('/dashboard/{season_id}/certificate/layout', [\App\Http\Controllers\CertificateController::class, 'saveLayout'])->name('admin.season.certificate.layout');
            Route::post('/dashboard/{season_id}/certificate/upload-element', [\App\Http\Controllers\CertificateController::class, 'uploadElement'])->name('admin.season.certificate.upload-element');
            Route::get('/certificate/google-login', [\App\Http\Controllers\CertificateController::class, 'googleLogin'])->name('admin.certificate.google-login');
            Route::get('/certificate/google-callback', [\App\Http\Controllers\CertificateController::class, 'googleCallback'])->name('admin.certificate.google-callback');
            Route::get('/certificate/google-disconnect', [\App\Http\Controllers\CertificateController::class, 'googleDisconnect'])->name('admin.certificate.google-disconnect');
            Route::post('/dashboard/{season_id}/certificate/generate-drive', [\App\Http\Controllers\CertificateController::class, 'generateToDrive'])->name('admin.season.certificate.generate-drive');
            Route::get('/dashboard/{season_id}/certificate/logs', [\App\Http\Controllers\CertificateController::class, 'getLogs'])->name('admin.season.certificate.logs');
            Route::get('/certificate/download-single', [\App\Http\Controllers\CertificateController::class, 'downloadSingle'])->name('admin.certificate.download-single');
        });

        // Finance
        Route::middleware('permission:finance')->group(function () {
            Route::get('/dashboard/{season_id}/finance', [AdminController::class, 'financeIndex'])->name('admin.season.finance.index');
            Route::post('/dashboard/{season_id}/finance', [AdminController::class, 'storeFinance'])->name('admin.season.finance.store');
            Route::delete('/dashboard/{season_id}/finance/{id}', [AdminController::class, 'deleteFinance'])->name('admin.season.finance.delete');
        });
        
        // Notes
        Route::middleware('permission:notes')->group(function () {
            Route::get('/notes', [AdminController::class, 'showNotes'])->name('admin.notes.index');
            Route::get('/notes/create', [AdminController::class, 'storeNote'])->name('admin.notes.store');
            Route::get('/notes/delete/{id}', [AdminController::class, 'deleteNote'])->name('admin.notes.delete');
            Route::post('/notes/update/{id}', [AdminController::class, 'updateNotes'])->name('admin.notes.update');
        });
        
        // Activity Log
        Route::middleware('permission:activity_log')->group(function () {
            Route::get('/activity-log', [AdminController::class, 'activityLog'])->name('admin.activity-log');
        });

        // FAQ Management
        Route::middleware('permission:faqs')->group(function () {
            Route::get('/faqs', [AdminController::class, 'faqs'])->name('admin.faqs.index');
            Route::post('/faqs/store', [AdminController::class, 'storeFaq'])->name('admin.faqs.store');
            Route::post('/faqs/update/{id}', [AdminController::class, 'updateFaq'])->name('admin.faqs.update');
            Route::get('/faqs/delete/{id}', [AdminController::class, 'deleteFaq'])->name('admin.faqs.delete');
            Route::post('/faqs/reorder/{id}', [AdminController::class, 'reorderFaq'])->name('admin.faqs.reorder');
            Route::post('/faqs/toggle/{id}', [AdminController::class, 'toggleFaq'])->name('admin.faqs.toggle');
            Route::post('/faqs/bulk-status', [AdminController::class, 'bulkStatusFaq'])->name('admin.faqs.bulk-status');
        });

        // Solo Matchmaker
        Route::middleware('permission:solo_matchmaker')->group(function () {
            Route::get('/solo-matchmaker/{season_id}', [AdminController::class, 'soloMatchmaker'])->name('admin.solo.matchmaker');
            Route::post('/solo-matchmaker/store/{season_id}', [AdminController::class, 'storeSoloPlayer'])->name('admin.solo.store');
            Route::post('/solo-matchmaker/bulk-store/{season_id}', [AdminController::class, 'bulkStoreSolo'])->name('admin.solo.bulkStore');
            Route::post('/solo-matchmaker/group/{season_id}', [AdminController::class, 'groupSoloPlayers'])->name('admin.solo.group');
            Route::post('/solo-matchmaker/create-empty-team/{season_id}', [AdminController::class, 'createEmptySoloTeam'])->name('admin.solo.createEmptyTeam');
            Route::post('/solo-matchmaker/update-player-team/{season_id}', [AdminController::class, 'updatePlayerTeam'])->name('admin.solo.updatePlayerTeam');
            Route::post('/solo-matchmaker/update/{id}', [AdminController::class, 'updateSoloPlayer'])->name('admin.solo.update');
            Route::post('/solo-matchmaker/team/update/{id}', [AdminController::class, 'updateSoloTeamDetails'])->name('admin.solo.team.update');
            Route::get('/solo-matchmaker/suggest/{season_id}', [AdminController::class, 'suggestTeams'])->name('admin.solo.suggest');
            Route::get('/solo-matchmaker/team/delete/{id}', [AdminController::class, 'deleteSoloTeam'])->name('admin.solo.team.delete');
            Route::get('/solo-matchmaker/delete/{id}', [AdminController::class, 'deleteSoloPlayer'])->name('admin.solo.delete');
        });

        // Teams Directory
        Route::middleware('permission:teams')->group(function () {
            Route::get('/teams', [AdminController::class, 'teams'])->name('admin.teams');
        });

        // Payment History
        Route::middleware('permission:payments')->group(function () {
            Route::get('/payment-history', [AdminController::class, 'paymentHistory'])->name('admin.payments');
        });

        // System Settings
        Route::middleware('permission:settings')->group(function () {
            Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
            Route::post('/settings/update', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
            Route::get('/settings/gateway-notifications', [AdminController::class, 'gatewayNotifications'])->name('admin.settings.gateway_notifications');
        });

        // Database Backup
        Route::middleware('permission:backup')->group(function () {
            Route::get('/backup', [AdminController::class, 'backupDatabase'])->name('admin.backup');
        });
        
        // Admin Staff Management
        Route::middleware('permission:manage')->group(function () {
            Route::get('/manage-admins', [AdminController::class, 'adminList'])->name('admin.manage');
            Route::post('/manage-admins/store', [AdminController::class, 'storeAdmin'])->name('admin.manage.store');
            Route::post('/manage-admins/update/{id}', [AdminController::class, 'updateAdmin'])->name('admin.manage.update');
            Route::get('/manage-admins/delete/{id}', [AdminController::class, 'deleteAdmin'])->name('admin.manage.delete');
            Route::post('/manage-admins/toggle-permission', [AdminController::class, 'togglePermission'])->name('admin.manage.toggle-permission');

            Route::get('/storage-manager', [AdminController::class, 'storageManager'])->name('admin.storage');
            Route::post('/storage-manager/clear-folder', [AdminController::class, 'clearStorageFolder'])->name('admin.storage.clear-folder');
            Route::post('/storage-manager/delete-file', [AdminController::class, 'deleteStorageFile'])->name('admin.storage.delete-file');
            Route::get('/system-logs', [AdminController::class, 'laravelLogs'])->name('admin.system-logs');
        });
    });
});

Route::view('/privacy-policy', 'pages.privacy')->name('privacy');
Route::view('/terms-conditions', 'pages.terms')->name('terms');
Route::view('/contact-us', 'pages.contact')->name('contact');

Route::redirect('/register/{id}', '/daftar', 301);

Route::get('/bracket-demo', function () {
    return view('pages.bracket_demo');
})->name('bracket.demo');

// Public Season Landing Page (Obfuscated slug)
Route::get('/season/{slug}', [\App\Http\Controllers\BracketController::class, 'seasonLanding'])->name('public.season.landing');

// Public Bracket Viewer Route (Obfuscated slug)
Route::get('/season/{slug}/bracket', [\App\Http\Controllers\BracketController::class, 'publicBracket'])->name('public.season.bracket');
Route::get('/season/{slug}/bracket/data', [\App\Http\Controllers\BracketController::class, 'getBracketData'])->name('public.season.bracket.data');

// Public Chat API routes
Route::get('/season/{slug}/chat/messages', [\App\Http\Controllers\BracketController::class, 'getChatMessages'])->name('public.season.chat.messages');
Route::post('/season/{slug}/chat/send', [\App\Http\Controllers\BracketController::class, 'sendChatMessage'])->name('public.season.chat.send');
Route::post('/season/{slug}/chat/upload', [\App\Http\Controllers\BracketController::class, 'uploadChatImage'])->name('public.season.chat.upload');

// Public Match Report routes
Route::post('/season/{slug}/match-report/find', [\App\Http\Controllers\BracketController::class, 'findActiveMatchForReport'])->name('public.match-report.find');
Route::post('/season/{slug}/match-report/submit', [\App\Http\Controllers\BracketController::class, 'submitMatchReport'])->name('public.match-report.submit');

Route::get('/chat-debug-files', function() {
    $parentPath = base_path('chat_uploads');
    $publicPath = public_path('chat_uploads');

    if (!file_exists($publicPath)) {
        mkdir($publicPath, 0755, true);
    }

    if (is_dir($parentPath)) {
        $files = scandir($parentPath);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && is_file($parentPath . '/' . $file)) {
                rename($parentPath . '/' . $file, $publicPath . '/' . $file);
            }
        }
    }

    $parentFiles = is_dir($parentPath) ? scandir($parentPath) : ['Directory does not exist'];
    $publicFiles = is_dir($publicPath) ? scandir($publicPath) : ['Directory does not exist'];

    return response()->json([
        'message' => 'Files migrated successfully!',
        'parent_chat_uploads_path' => $parentPath,
        'parent_files' => $parentFiles,
        'public_chat_uploads_path' => $publicPath,
        'public_files' => $publicFiles,
    ]);
});

// ==========================================
// QRIS GoPay Merchant Gateway (Terisolasi)
// ==========================================
Route::prefix('qris-gateway')->name('qris.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('qris.dashboard');
    });

    Route::get('/pay/{trx_id}', [\App\Http\Controllers\Qris\QrisController::class, 'showPayment'])->name('pay');
    Route::post('/pay/{trx_id}/proof', [\App\Http\Controllers\Qris\QrisController::class, 'uploadProof'])->name('pay.proof');
    Route::get('/check/{trx_id}', [\App\Http\Controllers\Qris\QrisController::class, 'checkStatus'])->name('check');
    Route::get('/check-now/{trx_id}', [\App\Http\Controllers\Qris\QrisController::class, 'forceCheckStatus'])->name('check.force');
    Route::get('/debug/{trx_id}', [\App\Http\Controllers\Qris\QrisController::class, 'debugTrans'])->name('debug');



    Route::middleware([\App\Http\Middleware\QrisAuthMiddleware::class])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Qris\QrisAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/transactions', [\App\Http\Controllers\Qris\QrisAdminController::class, 'transactions'])->name('transactions');
        Route::get('/settings', [\App\Http\Controllers\Qris\QrisAdminController::class, 'settings'])->name('settings');
        Route::get('/test-poll', [\App\Http\Controllers\Qris\QrisAdminController::class, 'testPoll'])->name('test-poll');
        Route::post('/config', [\App\Http\Controllers\Qris\QrisAdminController::class, 'updateConfig'])->name('config.update');
        Route::post('/settle/{trx_id}', [\App\Http\Controllers\Qris\QrisAdminController::class, 'manualSettle'])->name('settle');
        Route::post('/sync-pending', [\App\Http\Controllers\Qris\QrisAdminController::class, 'syncPending'])->name('sync-pending');
        Route::delete('/delete/{trx_id}', [\App\Http\Controllers\Qris\QrisAdminController::class, 'deleteTransaction'])->name('delete');
        Route::post('/delete-bulk', [\App\Http\Controllers\Qris\QrisAdminController::class, 'deleteBulkTransactions'])->name('delete-bulk');
        Route::get('/test-gopay-response', [\App\Http\Controllers\Qris\QrisAdminController::class, 'testGopayResponse'])->name('test-gopay-response');
    });
});
