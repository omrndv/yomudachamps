<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\Team;
use App\Models\CertificateLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDriveService;
use Google\Service\Drive\DriveFile;
use Intervention\Image\Facades\Image;
use Setasign\Fpdi\Fpdi;

class CertificateController extends Controller
{
    /**
     * Get Google Client Instance
     */
    private function getGoogleClient()
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(route('admin.certificate.google-callback'));
        $client->addScope(GoogleDriveService::DRIVE);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        return $client;
    }

    /**
     * Tampilkan Halaman Editor & Pengaturan Sertifikat
     */
    public function index($season_id)
    {
        $season = Season::findOrFail($season_id);
        $layout = CertificateLayout::firstOrCreate(
            ['season_id' => $season_id],
            [
                'font_size' => 48,
                'font_color' => '#ffc107',
                'pos_x' => 50.0,
                'pos_y' => 50.0,
            ]
        );

        $paidTeamsCount = Team::where('season_id', $season_id)
            ->where('status', 'PAID')
            ->count();

        // Cek apakah Google Drive terhubung
        $googleConnected = false;
        $googleUserEmail = null;
        if (Session::has('google_oauth_token')) {
            $client = $this->getGoogleClient();
            $client->setAccessToken(Session::get('google_oauth_token'));
            
            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    Session::put('google_oauth_token', $client->getAccessToken());
                    $googleConnected = true;
                } else {
                    Session::forget('google_oauth_token');
                }
            } else {
                $googleConnected = true;
            }

            if ($googleConnected) {
                try {
                    $oauth2 = new \Google\Service\Oauth2($client);
                    $userInfo = $oauth2->userinfo->get();
                    $googleUserEmail = $userInfo->getEmail();
                } catch (\Exception $e) {
                    // Scope oauth2 might not be authorized, which is fine
                }
            }
        }

        return view('admin.certificate', compact('season', 'layout', 'paidTeamsCount', 'googleConnected', 'googleUserEmail'));
    }

    /**
     * Simpan Layout & Unggah Aset Sertifikat
     */
    public function saveLayout(Request $request, $season_id)
    {
        $request->validate([
            'template' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:30720',
            'font' => 'nullable|file|max:5120', // TTF font file
            'font_size' => 'required|integer|min:10|max:200',
            'font_color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'pos_x' => 'required|numeric|min:0|max:100',
            'pos_y' => 'required|numeric|min:0|max:100',
        ]);

        $layout = CertificateLayout::where('season_id', $season_id)->firstOrFail();
        $layout->font_size = $request->font_size;
        $layout->font_color = $request->font_color;
        $layout->pos_x = $request->pos_x;
        $layout->pos_y = $request->pos_y;

        // Handling Template Image Upload
        if ($request->hasFile('template')) {
            // Hapus template lama jika ada
            if ($layout->template_path && file_exists(public_path($layout->template_path))) {
                @unlink(public_path($layout->template_path));
            }

            $templateFile = $request->file('template');
            $templateName = 'template_' . $season_id . '_' . time() . '.' . $templateFile->getClientOriginalExtension();
            
            // Simpan langsung di folder public agar bisa dirender sebagai background canvas di browser
            $templateFile->move(public_path('uploads/certificates'), $templateName);
            $layout->template_path = '/uploads/certificates/' . $templateName;
        }

        // Handling Font File Upload (.ttf)
        if ($request->hasFile('font')) {
            // Hapus font lama jika ada
            if ($layout->font_path && file_exists(storage_path('app/' . $layout->font_path))) {
                @unlink(storage_path('app/' . $layout->font_path));
            }

            $fontFile = $request->file('font');
            $fontName = 'font_' . $season_id . '_' . time() . '.' . $fontFile->getClientOriginalExtension();
            $path = $fontFile->storeAs('fonts', $fontName);
            $layout->font_path = $path;
        }

        $layout->save();

        return redirect()->back()->with('success', 'Konfigurasi layout sertifikat berhasil disimpan!');
    }

    /**
     * Redirect to Google OAuth Consent Page
     */
    public function googleLogin()
    {
        $client = $this->getGoogleClient();
        // Add profile scope to read email address
        $client->addScope('email');
        $client->addScope('profile');
        
        $authUrl = $client->createAuthUrl();
        return redirect()->away($authUrl);
    }

    /**
     * Google OAuth Callback handler
     */
    public function googleCallback(Request $request)
    {
        if (!$request->has('code')) {
            return redirect()->route('admin.seasons')->with('error', 'Autentikasi Google dibatalkan.');
        }

        try {
            $client = $this->getGoogleClient();
            $token = $client->fetchAccessTokenWithAuthCode($request->code);
            
            if (isset($token['error'])) {
                return redirect()->route('admin.seasons')->with('error', 'Gagal terhubung dengan Google: ' . $token['error_description']);
            }

            Session::put('google_oauth_token', $token);
            
            // Redirect back to the previous certificate editor page
            $seasonId = session('current_cert_season_id', 1);
            return redirect()->route('admin.season.certificate', $seasonId)->with('success', 'Berhasil terhubung dengan Google Drive!');
        } catch (\Exception $e) {
            return redirect()->route('admin.seasons')->with('error', 'Error OAuth: ' . $e->getMessage());
        }
    }

    /**
     * Putuskan hubungan akun Google Drive
     */
    public function googleDisconnect()
    {
        Session::forget('google_oauth_token');
        return redirect()->back()->with('success', 'Berhasil memutuskan sambungan akun Google.');
    }

    /**
     * Generate Sertifikat Masal & Unggah langsung ke Google Drive Folder
     */
    public function generateToDrive(Request $request, $season_id)
    {
        try {
            $season = Season::findOrFail($season_id);
            $layout = CertificateLayout::where('season_id', $season_id)->first();

            if (!$layout || !$layout->template_path) {
                return response()->json(['success' => false, 'message' => 'Harap unggah gambar template sertifikat terlebih dahulu.'], 400);
            }

            if (!Session::has('google_oauth_token')) {
                return response()->json(['success' => false, 'message' => 'Silakan hubungkan akun Google Anda terlebih dahulu.'], 401);
            }

            $request->validate([
                'drive_link' => 'required|string',
            ]);

            // Extract Google Drive Folder ID from URL
            $folderId = $this->extractFolderId($request->drive_link);
            if (!$folderId) {
                return response()->json(['success' => false, 'message' => 'Format link Google Drive Folder tidak valid.'], 400);
            }

            // Check if generation is already running
            if (\Illuminate\Support\Facades\Cache::store('file')->get("cert_gen_status_{$season_id}") === 'running') {
                return response()->json(['success' => true, 'message' => 'Proses sinkronisasi sedang berjalan di latar belakang.']);
            }

            // Initialize Google Service
            $client = $this->getGoogleClient();
            $client->setAccessToken(Session::get('google_oauth_token'));
            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    Session::put('google_oauth_token', $client->getAccessToken());
                } else {
                    return response()->json(['success' => false, 'message' => 'Sesi Google Drive telah habis. Hubungkan ulang akun Anda.'], 401);
                }
            }

            // Fetch paid teams/members
            $teams = Team::where('season_id', $season_id)
                ->where('status', 'PAID')
                ->get();

            if ($teams->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada tim terdaftar yang lunas (PAID) untuk season ini.'], 400);
            }

            // Release Laravel session lock so polling getLogs does not block!
            session_write_close();

            // Set running status
            \Illuminate\Support\Facades\Cache::store('file')->put("cert_gen_status_{$season_id}", 'running', 1800);
            \Illuminate\Support\Facades\Cache::store('file')->put("cert_gen_progress_{$season_id}", 0, 1800);
            \Illuminate\Support\Facades\Cache::store('file')->forget("cert_gen_logs_{$season_id}");

            $this->writeGenLog($season_id, "🚀 Memulai proses sinkronisasi sertifikat...");
            $this->writeGenLog($season_id, "Membaca berkas di folder Google Drive tujuan...");

            $driveService = new GoogleDriveService($client);

            // Fetch existing certificates in the folder
            $query = "'{$folderId}' in parents and trashed = false";
            $optParams = [
                'q' => $query,
                'fields' => 'files(name)',
                'pageSize' => 1000
            ];
            $results = $driveService->files->listFiles($optParams);
            
            $existingFiles = [];
            foreach ($results->getFiles() as $file) {
                $existingFiles[$file->getName()] = true;
            }

            $existingCount = count($existingFiles);
            $this->writeGenLog($season_id, "✅ Folder Drive terhubung. Absen file dimuat: {$existingCount} file ditemukan.");

            // Prepare local template and font path
            $templateFullPath = public_path($layout->template_path);
            $fontFullPath = $layout->font_path ? storage_path('app/' . $layout->font_path) : null;
            $isPdf = strtolower(pathinfo($templateFullPath, PATHINFO_EXTENSION)) === 'pdf';

            // Create temporary directory for certificate output
            $tempDir = storage_path('app/temp_certs');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $totalTeams = $teams->count();
            $processedCount = 0;
            $successCount = 0;
            $skippedCount = 0;

            foreach ($teams as $team) {
                $processedCount++;
                $progressPercent = round(($processedCount / $totalTeams) * 100);

                // Generate filename
                // Generate filename (always PDF)
                $fileName = 'Sertifikat - ' . $team->name . '.pdf';
                $tempFilePath = $tempDir . '/' . $fileName;

                // Check if file already exists in folder
                if (isset($existingFiles[$fileName])) {
                    $this->writeGenLog($season_id, "✅ SKIP (Sudah ada): " . $team->name, $progressPercent);
                    $skippedCount++;
                    continue;
                }

                // If not exist, generate and upload
                try {
                    $pdf = new Fpdi();
                    
                    if ($isPdf) {
                        // For PDF template, use points 'pt' unit
                        $pdf = new Fpdi('L', 'pt');
                        $pdf->setSourceFile($templateFullPath);
                        $tplIdx = $pdf->importPage(1);
                        $specs = $pdf->getTemplateSize($tplIdx);
                        $width = $specs['width'];
                        $height = $specs['height'];

                        $orientation = ($width > $height) ? 'L' : 'P';
                        $pdf->AddPage($orientation, [$width, $height]);
                        $pdf->useTemplate($tplIdx);

                        $posX = ($layout->pos_x / 100) * $width;
                        $posY = ($layout->pos_y / 100) * $height;

                        $fontSize = $layout->font_size / 1.5;
                        $pdf->SetFont('Arial', 'B', $fontSize);

                        $hex = str_replace('#', '', $layout->font_color);
                        $r = hexdec(substr($hex, 0, 2));
                        $g = hexdec(substr($hex, 2, 2));
                        $b = hexdec(substr($hex, 4, 2));
                        $pdf->SetTextColor($r, $g, $b);

                        $pdf->SetXY(0, $posY - ($fontSize / 2));
                        $pdf->Cell($width, $fontSize, $team->name, 0, 0, 'C');
                    } else {
                        // For Image template, determine pixel dimensions and convert to PDF
                        list($imgWidth, $imgHeight) = getimagesize($templateFullPath);
                        $orientation = ($imgWidth > $imgHeight) ? 'L' : 'P';

                        $pdf = new Fpdi($orientation, 'pt', [$imgWidth, $imgHeight]);
                        $pdf->AddPage($orientation, [$imgWidth, $imgHeight]);

                        // Background template image
                        $pdf->Image($templateFullPath, 0, 0, $imgWidth, $imgHeight);

                        $posX = ($layout->pos_x / 100) * $imgWidth;
                        $posY = ($layout->pos_y / 100) * $imgHeight;

                        $fontSize = $layout->font_size;
                        $pdf->SetFont('Arial', 'B', $fontSize);

                        $hex = str_replace('#', '', $layout->font_color);
                        $r = hexdec(substr($hex, 0, 2));
                        $g = hexdec(substr($hex, 2, 2));
                        $b = hexdec(substr($hex, 4, 2));
                        $pdf->SetTextColor($r, $g, $b);

                        $pdf->SetXY(0, $posY - ($fontSize / 2));
                        $pdf->Cell($imgWidth, $fontSize, $team->name, 0, 0, 'C');
                    }

                    $pdf->Output('F', $tempFilePath);

                    // Upload to Google Drive
                    $fileMetadata = new DriveFile([
                        'name' => $fileName,
                        'parents' => [$folderId]
                    ]);

                    $content = file_get_contents($tempFilePath);
                    $mimeType = 'application/pdf';
                    
                    $driveService->files->create($fileMetadata, [
                        'data' => $content,
                        'mimeType' => $mimeType,
                        'uploadType' => 'multipart',
                        'fields' => 'id'
                    ]);

                    @unlink($tempFilePath);
                    $successCount++;
                    $this->writeGenLog($season_id, "🔨 DIBUAT BARU: " . $team->name, $progressPercent);
                } catch (\Exception $e) {
                    $this->writeGenLog($season_id, "❌ GAGAL MEMPROSES: " . $team->name . " (" . $e->getMessage() . ")", $progressPercent);
                }
            }

            $this->writeGenLog($season_id, "🎉 Selesai! Folder sudah bersih dan lengkap. ({$successCount} dibuat baru, {$skippedCount} di-skip, {$totalTeams} total)");
            return response()->json(['success' => true, 'message' => "Proses sinkronisasi selesai! {$successCount} diunggah, {$skippedCount} di-skip."]);
        } catch (\Exception $e) {
            $this->writeGenLog($season_id, "🚨 Error inisialisasi: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        } finally {
            \Illuminate\Support\Facades\Cache::store('file')->put("cert_gen_status_{$season_id}", 'idle', 1800);
            \Illuminate\Support\Facades\Cache::store('file')->put("cert_gen_progress_{$season_id}", 100, 1800);
        }
    }

    /**
     * Download manual satu sertifikat (on-the-fly streaming)
     */
    public function downloadSingle(Request $request)
    {
        $request->validate([
            'season_id' => 'required|integer',
            'name' => 'required|string|max:100',
        ]);

        $layout = CertificateLayout::where('season_id', $request->season_id)->first();
        if (!$layout || !$layout->template_path) {
            return redirect()->back()->with('error', 'Harap simpan template sertifikat terlebih dahulu.');
        }

        $templateFullPath = public_path($layout->template_path);
        $isPdf = strtolower(pathinfo($templateFullPath, PATHINFO_EXTENSION)) === 'pdf';

        try {
            $pdf = new Fpdi();
            
            if ($isPdf) {
                // For PDF template, use points 'pt' unit
                $pdf = new Fpdi('L', 'pt');
                $pdf->setSourceFile($templateFullPath);
                $tplIdx = $pdf->importPage(1);
                $specs = $pdf->getTemplateSize($tplIdx);
                $width = $specs['width'];
                $height = $specs['height'];

                $orientation = ($width > $height) ? 'L' : 'P';
                $pdf->AddPage($orientation, [$width, $height]);
                $pdf->useTemplate($tplIdx);

                $posX = ($layout->pos_x / 100) * $width;
                $posY = ($layout->pos_y / 100) * $height;

                $fontSize = $layout->font_size / 1.5;
                $pdf->SetFont('Arial', 'B', $fontSize);

                $hex = str_replace('#', '', $layout->font_color);
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                $pdf->SetTextColor($r, $g, $b);

                $pdf->SetXY(0, $posY - ($fontSize / 2));
                $pdf->Cell($width, $fontSize, $request->name, 0, 0, 'C');
            } else {
                // For Image template, convert to PDF
                list($imgWidth, $imgHeight) = getimagesize($templateFullPath);
                $orientation = ($imgWidth > $imgHeight) ? 'L' : 'P';
                
                $pdf = new Fpdi($orientation, 'pt', [$imgWidth, $imgHeight]);
                $pdf->AddPage($orientation, [$imgWidth, $imgHeight]);
                
                $pdf->Image($templateFullPath, 0, 0, $imgWidth, $imgHeight);

                $posX = ($layout->pos_x / 100) * $imgWidth;
                $posY = ($layout->pos_y / 100) * $imgHeight;

                $fontSize = $layout->font_size;
                $pdf->SetFont('Arial', 'B', $fontSize);

                $hex = str_replace('#', '', $layout->font_color);
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                $pdf->SetTextColor($r, $g, $b);

                $pdf->SetXY(0, $posY - ($fontSize / 2));
                $pdf->Cell($imgWidth, $fontSize, $request->name, 0, 0, 'C');
            }

            return response($pdf->Output('S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Sertifikat - ' . $request->name . '.pdf"'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses sertifikat: ' . $e->getMessage());
        }
    }

    /**
     * Tulis log aktivitas sinkronisasi sertifikat ke Cache
     */
    private function writeGenLog($seasonId, $message, $progress = null)
    {
        $logs = \Illuminate\Support\Facades\Cache::store('file')->get("cert_gen_logs_{$seasonId}", []);
        $logs[] = "[" . date('H:i:s') . "] " . $message;
        \Illuminate\Support\Facades\Cache::store('file')->put("cert_gen_logs_{$seasonId}", $logs, 1800);
        if ($progress !== null) {
            \Illuminate\Support\Facades\Cache::store('file')->put("cert_gen_progress_{$seasonId}", $progress, 1800);
        }
    }

    /**
     * Endpoint polling untuk mendapatkan status & log aktivitas sinkronisasi sertifikat (JSON)
     */
    public function getLogs($season_id)
    {
        return response()->json([
            'status' => \Illuminate\Support\Facades\Cache::store('file')->get("cert_gen_status_{$season_id}", 'idle'),
            'progress' => \Illuminate\Support\Facades\Cache::store('file')->get("cert_gen_progress_{$season_id}", 0),
            'logs' => \Illuminate\Support\Facades\Cache::store('file')->get("cert_gen_logs_{$season_id}", [])
        ]);
    }

    /**
     * Parse Google Drive Folder Link to extract Folder ID
     */
    private function extractFolderId($url)
    {
        // Format link: https://drive.google.com/drive/folders/FOLDER_ID?usp=sharing
        // Atau: https://drive.google.com/open?id=FOLDER_ID
        if (preg_match('/folders\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        // Asumsi input langsung FOLDER_ID jika bukan URL lengkap
        if (preg_match('/^[a-zA-Z0-9_-]{15,}$/', $url)) {
            return $url;
        }

        return null;
    }
}
