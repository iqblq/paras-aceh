<?php

namespace App\Http\Controllers;

use App\Services\SintesaImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    protected SintesaImportService $importService;

    public function __construct(SintesaImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Halaman Dashboard Admin Kelola & Sinkronisasi Dataset SINTESA
     */
    public function index(): View
    {
        $kpi = $this->importService->getSummary('executive_kpi');
        $logs = DB::table('sintesa_sync_logs')->orderByDesc('created_at')->limit(10)->get();
        $folder = 'c:\UIN\DJPb Aceh\dataset csv excel';
        
        $files = [];
        if (is_dir($folder)) {
            foreach (glob("{$folder}/*.csv") as $f) {
                $files[] = [
                    'name' => basename($f),
                    'size' => round(filesize($f) / 1024, 2) . ' KB',
                    'updated_at' => date('d M Y H:i', filemtime($f)),
                ];
            }
        }

        $useRealData = config('app.sintesa_real_data', true);

        return view('admin.sync', compact('kpi', 'logs', 'files', 'useRealData'));
    }

    /**
     * Trigger proses sinkronisasi dari folder dataset
     */
    public function processSync(Request $request): JsonResponse
    {
        try {
            $folder = 'c:\UIN\DJPb Aceh\dataset csv excel';
            $result = $this->importService->syncAllFromFolder($folder);

            return response()->json([
                'status' => 'success',
                'message' => 'Sinkronisasi 5 dataset resmi SINTESA berhasil diselesaikan!',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan sinkronisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload berkas CSV baru dari web browser
     */
    public function uploadCsv(Request $request): JsonResponse
    {
        $request->validate([
            'csv_file' => 'required|file',
            'file_category' => 'required|string'
        ]);

        try {
            $file = $request->file('csv_file');
            $category = $request->input('file_category');
            $folder = 'c:\UIN\DJPb Aceh\dataset csv excel';

            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            $prefix = match ($category) {
                'pagu' => 'inquiry_data_pagu_dan_blokir_2026_',
                'belanja' => 'belanja_kewilayahan_2026_',
                'tematik' => 'belwil_tematik_belwil_tematik_prioritasPresiden_2026_',
                'dana_desa' => 'dana_desa_2026_',
                'dak_fisik' => 'dak_fisik_2025_',
                default => 'dataset_sintesa_'
            };

            $targetName = $prefix . time() . '.csv';
            $file->move($folder, $targetName);

            // Langsung sinkronkan
            $this->importService->syncAllFromFolder($folder);

            return response()->json([
                'status' => 'success',
                'message' => "Berkas {$file->getClientOriginalName()} berhasil diunggah dan disinkronkan ke database!",
                'saved_as' => $targetName
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunggah berkas: ' . $e->getMessage()
            ], 500);
        }
    }
}
