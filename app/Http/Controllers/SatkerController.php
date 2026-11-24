<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SatkerController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Tampilan Monitoring 311 Satker Kanwil DJPb Provinsi Aceh
     */
    public function index(Request $request): View
    {
        $kpi = $this->supabase->getExecutiveKpi();
        $jenisBelanja = $this->supabase->getJenisBelanjaCards();
        $satkerList = $this->supabase->get('realisasi_anggaran');
        return view('satker.index', compact('kpi', 'jenisBelanja', 'satkerList'));
    }

    /**
     * Unduh Data Satker dalam Format CSV
     */
    public function exportCsv(): StreamedResponse
    {
        $satkerList = $this->supabase->get('realisasi_anggaran');

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Realisasi-Satker-DJPb-Aceh-' . date('Ymd') . '.csv"',
        ];

        $callback = function () use ($satkerList) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($output, ['Kode Satker', 'Nama Satker', 'K/L', 'KPPN', 'Pagu Belanja', 'Realisasi', '% Capaian', 'Pagu Blokir']);

            foreach ($satkerList as $s) {
                fputcsv($output, [
                    $s['kode_satker'] ?? '',
                    $s['nama_satker'] ?? '',
                    $s['nama_kl'] ?? '',
                    $s['kppn'] ?? '',
                    $s['total_pagu'] ?? 0,
                    $s['total_realisasi'] ?? 0,
                    ($s['persen'] ?? 0) . '%',
                    $s['pagu_blokir'] ?? 0
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
