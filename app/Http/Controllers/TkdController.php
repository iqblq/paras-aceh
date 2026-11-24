<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TkdController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index(Request $request): View
    {
        $summary = $this->supabase->getTKDSummary();
        $satkerBUN = $this->supabase->getTKDSatkerBUN();
        $otsusItems = $this->supabase->getTKDOtsusItems();
        $dbhItems = $this->supabase->getTKDDbhItems();
        $dbhDaerah = $this->supabase->getTKDDbhDaerah();
        $otsusPorsi = $this->supabase->getTKDOtsusPorsi();
        $tkdData = $this->supabase->getTKDData();

        $tkdSummaryRaw = app(\App\Services\SintesaImportService::class)->getSummary('tkd_summary');
        $danaDesaList = $tkdSummaryRaw['dana_desa'] ?? [];
        $dakFisikList = $tkdSummaryRaw['dak_fisik'] ?? [];

        return view('tkd.index', compact(
            'summary',
            'satkerBUN',
            'otsusItems',
            'dbhItems',
            'dbhDaerah',
            'otsusPorsi',
            'tkdData',
            'danaDesaList',
            'dakFisikList'
        ));
    }

    public function exportCsv(): StreamedResponse
    {
        $dbhItems = $this->supabase->getTKDDbhItems();
        $otsusItems = $this->supabase->getTKDOtsusItems();
        $satkerBUN = $this->supabase->getTKDSatkerBUN();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Transfer-Ke-Daerah-Kanwil-DJPb-Aceh-' . date('Ymd') . '.csv"',
        ];

        $callback = function () use ($dbhItems, $otsusItems, $satkerBUN) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Section 1: DBH
            fputcsv($out, ['=== 1. RINCIAN DANA BAGI HASIL (DBH) ===']);
            fputcsv($out, ['No', 'Uraian DBH', 'Kategori', 'Pagu Alokasi', 'Realisasi Salur', 'Sisa Dana', '% Realisasi', 'Status']);
            foreach ($dbhItems as $it) {
                fputcsv($out, [
                    $it['no'],
                    $it['uraian'],
                    $it['kategori'],
                    $it['pagu'],
                    $it['realisasi'],
                    $it['sisa'],
                    $it['pct'],
                    $it['status']
                ]);
            }
            fputcsv($out, []);

            // Section 2: Otonomi Khusus
            fputcsv($out, ['=== 2. RINCIAN DANA OTONOMI KHUSUS (DOKA) ===']);
            fputcsv($out, ['No', 'Uraian Otonomi Khusus', 'Kode Satker', 'Nama Satker', 'Pagu Alokasi', 'Realisasi Salur', 'Sisa Dana', '% Realisasi', 'Porsi %', 'Status']);
            foreach ($otsusItems as $it) {
                fputcsv($out, [
                    $it['no'],
                    $it['uraian'],
                    $it['kode_satker'],
                    $it['nama_satker'],
                    $it['pagu'],
                    $it['realisasi'],
                    $it['sisa'],
                    $it['pct'],
                    $it['porsi'] . '%',
                    $it['status']
                ]);
            }
            fputcsv($out, []);

            // Section 3: Satker BA BUN
            fputcsv($out, ['=== 3. PAGU REALISASI PER SATKER PENYALUR BA BUN ===']);
            fputcsv($out, ['No', 'Kode Satker', 'Nama Satker Penyalur', 'Klasifikasi', 'Pagu DIPA', 'Realisasi Salur', 'Sisa Alokasi', '% Realisasi', 'Keterangan']);
            foreach ($satkerBUN as $it) {
                fputcsv($out, [
                    $it['no'],
                    $it['kode'],
                    $it['nama'],
                    $it['sub'],
                    $it['pagu'],
                    $it['realisasi'],
                    $it['sisa'],
                    $it['pct'],
                    $it['keterangan']
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
