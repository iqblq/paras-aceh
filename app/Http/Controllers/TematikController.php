<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TematikController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function prioritas(): View
    {
        $summary = $this->supabase->getPrioritasPresidenSummary();
        $jenisList = $this->supabase->getPrioritasPresidenJenis();
        $items = $this->supabase->getMockData('prioritas_presiden');
        return view('tematik.prioritas', compact('summary', 'jenisList', 'items'));
    }

    public function exportPrioritasCsv(): StreamedResponse
    {
        $items = $this->supabase->getMockData('prioritas_presiden');
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Prioritas-Presiden-Kanwil-DJPb-Aceh-' . date('Ymd') . '.csv"',
        ];

        $callback = function () use ($items) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['No', 'Kode Satker', 'Nama Satker', 'K/L', 'Wilayah', 'Kode Jenis', 'Jenis Prioritas', 'Kegiatan', 'Program', 'Pagu', 'Realisasi', '% Realisasi']);
            foreach ($items as $it) {
                fputcsv($out, [
                    $it['no'] ?? '',
                    $it['kode_satker'] ?? '',
                    $it['nama_satker'] ?? '',
                    $it['kl'] ?? '',
                    $it['wilayah'] ?? '',
                    $it['kode_jenis'] ?? '',
                    $it['jenis'] ?? '',
                    $it['kegiatan'] ?? '',
                    $it['program'] ?? '',
                    $it['pagu'] ?? 0,
                    $it['realisasi'] ?? 0,
                    $it['pct'] ?? 0,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function strategis(): View
    {
        $summary = $this->supabase->getProgramStrategisSummary();
        $jenisList = $this->supabase->getProgramStrategisJenis();
        $items = $this->supabase->getMockData('program_strategis');
        return view('tematik.strategis', compact('summary', 'jenisList', 'items'));
    }

    public function exportStrategisCsv(): StreamedResponse
    {
        $items = $this->supabase->getMockData('program_strategis');
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Program-Strategis-Kanwil-DJPb-Aceh-' . date('Ymd') . '.csv"',
        ];

        $callback = function () use ($items) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['No', 'Kode Satker', 'Nama Satker', 'K/L', 'Wilayah', 'Kode Jenis', 'Jenis Program Strategis', 'Kegiatan', 'Program', 'Pagu', 'Realisasi', '% Realisasi', 'Pagu Blokir']);
            foreach ($items as $it) {
                fputcsv($out, [
                    $it['no'] ?? '',
                    $it['kode_satker'] ?? '',
                    $it['nama_satker'] ?? '',
                    $it['kl'] ?? '',
                    $it['wilayah'] ?? '',
                    $it['kode_jenis'] ?? '',
                    $it['jenis'] ?? '',
                    $it['kegiatan'] ?? '',
                    $it['program'] ?? '',
                    $it['pagu'] ?? 0,
                    $it['realisasi'] ?? 0,
                    $it['pct'] ?? 0,
                    $it['blokir'] ?? 0,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bencana(): View
    {
        $summary = $this->supabase->getBencanaSummary();
        $outputs = $this->supabase->getBencanaOutputs();
        $items = $this->supabase->getMockData('penanganan_bencana');
        $rkklOutputs = $this->supabase->getBencanaRkklOutputs();
        $rkklItems = $this->supabase->getBencanaRkklItems();
        return view('tematik.bencana', compact('summary', 'outputs', 'items', 'rkklOutputs', 'rkklItems'));
    }

    public function exportBencanaCsv(): StreamedResponse
    {
        $items = $this->supabase->getMockData('penanganan_bencana');
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Penanganan-Bencana-Kanwil-DJPb-Aceh-' . date('Ymd') . '.csv"',
        ];

        $callback = function () use ($items) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['No', 'Kode Satker', 'Nama Satker', 'K/L', 'Wilayah', 'Kode Output', 'Kegiatan', 'Output', 'Program', 'Pagu', 'Realisasi', '% Realisasi']);
            foreach ($items as $it) {
                fputcsv($out, [
                    $it['no'] ?? '',
                    $it['kode_satker'] ?? '',
                    $it['nama_satker'] ?? '',
                    $it['kl'] ?? '',
                    $it['wilayah'] ?? '',
                    $it['kode_out'] ?? '',
                    $it['kegiatan'] ?? '',
                    $it['output'] ?? '',
                    $it['program'] ?? '',
                    $it['pagu'] ?? 0,
                    $it['realisasi'] ?? 0,
                    $it['pct'] ?? 0,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function jalan(): View
    {
        $summary = $this->supabase->getJalanSummary();
        $outputs = $this->supabase->getJalanOutputs();
        $items = $this->supabase->getMockData('preservasi_jalan');
        return view('tematik.jalan', compact('summary', 'outputs', 'items'));
    }

    public function exportJalanCsv(): StreamedResponse
    {
        $items = $this->supabase->getMockData('preservasi_jalan');
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Preservasi-Jalan-Kanwil-DJPb-Aceh-' . date('Ymd') . '.csv"',
        ];

        $callback = function () use ($items) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['No', 'Kode Satker', 'Nama Satker', 'K/L', 'Wilayah', 'Kode Output', 'Kegiatan', 'Output', 'Program', 'Pagu', 'Realisasi', '% Realisasi']);
            foreach ($items as $it) {
                fputcsv($out, [
                    $it['no'] ?? '',
                    $it['kode_satker'] ?? '',
                    $it['nama_satker'] ?? '',
                    $it['kl'] ?? '',
                    $it['wilayah'] ?? '',
                    $it['kode_out'] ?? '',
                    $it['kegiatan'] ?? '',
                    $it['output'] ?? '',
                    $it['program'] ?? '',
                    $it['pagu'] ?? 0,
                    $it['realisasi'] ?? 0,
                    $it['pct'] ?? 0,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bansos(): View
    {
        $summary = $this->supabase->getBansosSummary();
        $outputs = $this->supabase->getBansosOutputs();
        $items = $this->supabase->getMockData('bantuan_sosial');
        return view('tematik.bansos', compact('summary', 'outputs', 'items'));
    }

    public function exportBansosCsv(): StreamedResponse
    {
        $items = $this->supabase->getMockData('bantuan_sosial');
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Bantuan-Sosial-Kanwil-DJPb-Aceh-' . date('Ymd') . '.csv"',
        ];

        $callback = function () use ($items) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['No', 'Kode Satker', 'Nama Satker', 'K/L', 'Wilayah', 'Kode Kegiatan', 'Kegiatan', 'Kode Output', 'Output', 'Program', 'Pagu', 'Realisasi', 'Sisa Pagu', '% Realisasi', 'Status', 'Deskripsi']);
            foreach ($items as $it) {
                fputcsv($out, [
                    $it['no'] ?? '',
                    $it['kode_satker'] ?? '',
                    $it['nama_satker'] ?? '',
                    $it['kl'] ?? '',
                    $it['wilayah'] ?? '',
                    $it['kegiatan_kode'] ?? '',
                    $it['kegiatan'] ?? '',
                    $it['kode_out'] ?? '',
                    $it['output'] ?? '',
                    $it['program'] ?? '',
                    $it['pagu'] ?? 0,
                    $it['realisasi'] ?? 0,
                    $it['sisa'] ?? 0,
                    $it['pct'] ?? 0,
                    $it['status'] ?? '',
                    $it['deskripsi'] ?? '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
