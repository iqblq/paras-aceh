<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SintesaImportService
{
    private bool $inquiryPrioritasImported = false;

    /**
     * Jalankan sinkronisasi seluruh dataset dari folder default
     */
    public function syncAllFromFolder(string $folderPath = null): array
    {
        $folder = $folderPath ?? 'c:\UIN\DJPb Aceh\dataset csv excel';
        $results = [];

        if (!is_dir($folder)) {
            return ['status' => 'error', 'message' => "Folder dataset tidak ditemukan: {$folder}"];
        }

        // 1. Prioritaskan file gabungan terbaru: Inquiry Data Pagu & Realisasi (2. Pagu Realisasi)
        $paguRealFile = $this->findLatestFile($folder, 'inquiry_data_pagu_realisasi_2026_*.csv');
        if ($paguRealFile) {
            $results['pagu_realisasi'] = $this->importPaguRealisasi($paguRealFile);
        } else {
            // Fallback ke format lama (2 file terpisah)
            $paguFile = $this->findLatestFile($folder, 'inquiry_data_pagu_dan_blokir_2026_*.csv');
            if ($paguFile) {
                $results['pagu'] = $this->importPaguDanBlokir($paguFile);
            }
            $belanjaFile = $this->findLatestFile($folder, 'belanja_kewilayahan_2026_*.csv');
            if ($belanjaFile) {
                $results['belanja'] = $this->importBelanjaKewilayahan($belanjaFile);
            }
        }

        // 2. Scan file Tematik dari Inquiry Data Tematik (Prioritas Presiden, Program Strategis, Bantuan Pemerintah)
        $bulananFiles = glob("{$folder}/inquiry_data_pagu_realisasi_bulanan_2026_*.csv") ?: [];
        foreach ($bulananFiles as $bf) {
            $fh = fopen($bf, 'r');
            $hdr = $fh ? fgetcsv($fh) : [];
            if ($fh) fclose($fh);
            $hdrStr = strtolower(implode(',', $hdr ?: []));

            if (str_contains($hdrStr, 'jenisprioritaspresiden_kode')) {
                $results['prioritas_presiden'] = $this->importInquiryTematikPrioritas($bf);
            } elseif (str_contains($hdrStr, 'jenisprogramstrategis_kode')) {
                $results['program_strategis'] = $this->importInquiryProgramStrategis($bf);
            } elseif (str_contains($hdrStr, 'outputkro_kode')) {
                $results['bantuan_pemerintah'] = $this->importInquiryBantuanPemerintah($bf);
            }
        }
        // Fallback jika hanya ada file belwil_tematik lama
        if (empty($results['prioritas_presiden'])) {
            $tematikFile = $this->findLatestFile($folder, 'belwil_tematik_*prioritasPresiden_*.csv');
            if ($tematikFile) {
                $results['tematik'] = $this->importTematikPrioritas($tematikFile);
            }
        }

        // 3. Bansos Kewilayahan (BPNT, PKH, POS_BST)
        $bansosFile = $this->findLatestFile($folder, 'bansos_kewilayahan_2026_*.csv');
        if ($bansosFile) {
            $results['bansos'] = $this->importBansosKewilayahan($bansosFile);
        }

        // 4. File Dana Desa 2026
        $danaDesaFile = $this->findLatestFile($folder, 'dana_desa_*.csv');
        if ($danaDesaFile) {
            $results['dana_desa'] = $this->importDanaDesa($danaDesaFile);
        }

        // 5. File DAK Fisik (jika ada)
        $dakFile = $this->findLatestFile($folder, 'dak_fisik_*.csv');
        if ($dakFile) {
            $results['dak_fisik'] = $this->importDakFisik($dakFile);
        }

        // 6. BOS/BOP 2026 & TPG 2026
        $bosFile = $this->findLatestFile($folder, 'bos_bop_*.csv');
        $tpgFile = $this->findLatestFile($folder, 'tpg_*.csv');
        if ($bosFile || $tpgFile) {
            $results['tkd_nonfisik'] = $this->importBosAndTpg($bosFile, $tpgFile);
        }

        // 7. Generate Pre-Aggregated Summary Tables untuk Instant High-Speed Load
        $results['summary'] = $this->generateExecutiveSummary();

        return [
            'status' => 'success',
            'message' => 'Seluruh dataset SINTESA berhasil disinkronisasi ke database lokal SQLite',
            'details' => $results
        ];
    }

    /**
     * 1b. Import Format Baru Gabungan: Inquiry Data Pagu & Realisasi (Pagu DIPA + Realisasi + Blokir)
     */
    public function importPaguRealisasi(string $filePath): array
    {
        DB::table('sintesa_satker_pagu')->truncate();
        DB::table('sintesa_belanja_bulanan')->truncate();

        $handle = fopen($filePath, 'r');
        if (!$handle) return ['status' => 'error', 'message' => 'Gagal membuka file Pagu Realisasi'];

        $header = fgetcsv($handle);
        $batchPagu = [];
        $batchBelanja = [];
        $totalRows = 0;
        $totalPagu = 0;
        $totalReal = 0;
        $totalBlokir = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 13) continue;

            // Pastikan murni Kanwil 01 jika ada baris kanwil lain
            $kanwilKode = trim($row[2] ?? '01');
            if ($kanwilKode !== '01') continue;

            $pagu = (int) preg_replace('/[^\d]/', '', $row[10] ?? '0');
            $real = (int) preg_replace('/[^\d]/', '', $row[11] ?? '0');
            $blokir = (int) preg_replace('/[^\d]/', '', $row[12] ?? '0');

            $totalPagu += $pagu;
            $totalReal += $real;
            $totalBlokir += $blokir;

            $base = [
                'kementerian_kode' => trim($row[0] ?? ''),
                'kementerian_uraian' => trim($row[1] ?? ''),
                'kanwil_kode' => $kanwilKode,
                'kanwil_uraian' => trim($row[3] ?? 'BANDA ACEH'),
                'kppn_kode' => trim($row[4] ?? ''),
                'kppn_uraian' => trim($row[5] ?? ''),
                'satker_kode' => trim($row[6] ?? ''),
                'satker_uraian' => trim($row[7] ?? ''),
                'akun_kode' => trim($row[8] ?? ''),
                'akun_uraian' => trim($row[9] ?? ''),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $batchPagu[] = array_merge($base, [
                'pagu' => $pagu,
                'blokir' => $blokir,
            ]);

            $batchBelanja[] = array_merge($base, [
                'real1' => 0, 'real2' => 0, 'real3' => 0, 'real4' => 0,
                'real5' => 0, 'real6' => 0, 'real7' => 0, 'real8' => 0,
                'real9' => 0, 'real10' => 0, 'real11' => 0, 'real12' => 0,
                'total_realisasi' => $real,
            ]);

            if (count($batchPagu) >= 500) {
                DB::table('sintesa_satker_pagu')->insert($batchPagu);
                DB::table('sintesa_belanja_bulanan')->insert($batchBelanja);
                $batchPagu = [];
                $batchBelanja = [];
            }
            $totalRows++;
        }

        if (!empty($batchPagu)) {
            DB::table('sintesa_satker_pagu')->insert($batchPagu);
            DB::table('sintesa_belanja_bulanan')->insert($batchBelanja);
        }
        fclose($handle);

        $this->logSync(basename($filePath), 'PAGU_REALISASI', $totalRows);

        return [
            'rows' => $totalRows,
            'total_pagu' => $totalPagu,
            'total_realisasi' => $totalReal,
            'total_blokir' => $totalBlokir,
            'file' => basename($filePath),
        ];
    }

    /**
     * 1. Import Pagu & Blokir Satker (18.503 baris)
     */
    public function importPaguDanBlokir(string $filePath): array
    {
        DB::table('sintesa_satker_pagu')->truncate();
        $handle = fopen($filePath, 'r');
        if (!$handle) return ['status' => 'error', 'message' => 'Gagal membuka file Pagu'];

        $header = fgetcsv($handle);
        $batch = [];
        $totalRows = 0;
        $totalPagu = 0;
        $totalBlokir = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 12) continue;

            $pagu = (int) preg_replace('/[^\d]/', '', $row[10] ?? '0');
            $blokir = (int) preg_replace('/[^\d]/', '', $row[11] ?? '0');
            $totalPagu += $pagu;
            $totalBlokir += $blokir;

            $batch[] = [
                'kementerian_kode' => trim($row[0] ?? ''),
                'kementerian_uraian' => trim($row[1] ?? ''),
                'kanwil_kode' => trim($row[2] ?? '01'),
                'kanwil_uraian' => trim($row[3] ?? 'BANDA ACEH'),
                'kppn_kode' => trim($row[4] ?? ''),
                'kppn_uraian' => trim($row[5] ?? ''),
                'satker_kode' => trim($row[6] ?? ''),
                'satker_uraian' => trim($row[7] ?? ''),
                'akun_kode' => trim($row[8] ?? ''),
                'akun_uraian' => trim($row[9] ?? ''),
                'pagu' => $pagu,
                'blokir' => $blokir,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= 500) {
                DB::table('sintesa_satker_pagu')->insert($batch);
                $batch = [];
            }
            $totalRows++;
        }

        if (!empty($batch)) {
            DB::table('sintesa_satker_pagu')->insert($batch);
        }
        fclose($handle);

        $this->logSync(basename($filePath), 'PAGU_DAN_BLOKIR', $totalRows);

        return [
            'rows' => $totalRows,
            'total_pagu' => $totalPagu,
            'total_blokir' => $totalBlokir,
            'file' => basename($filePath)
        ];
    }

    /**
     * 2. Import Belanja Kewilayahan Bulanan (17.921 baris)
     */
    public function importBelanjaKewilayahan(string $filePath): array
    {
        DB::table('sintesa_belanja_bulanan')->truncate();
        $handle = fopen($filePath, 'r');
        if (!$handle) return ['status' => 'error', 'message' => 'Gagal membuka file Belanja'];

        $header = fgetcsv($handle);
        $batch = [];
        $totalRows = 0;
        $grandRealisasi = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 23) continue;

            $totalReal = (int) preg_replace('/[^\d]/', '', $row[22] ?? '0');
            $grandRealisasi += $totalReal;

            $batch[] = [
                'kementerian_kode' => trim($row[0] ?? ''),
                'kementerian_uraian' => trim($row[1] ?? ''),
                'kanwil_kode' => trim($row[2] ?? '01'),
                'kanwil_uraian' => trim($row[3] ?? 'BANDA ACEH'),
                'kppn_kode' => trim($row[4] ?? ''),
                'kppn_uraian' => trim($row[5] ?? ''),
                'satker_kode' => trim($row[6] ?? ''),
                'satker_uraian' => trim($row[7] ?? ''),
                'akun_kode' => trim($row[8] ?? ''),
                'akun_uraian' => trim($row[9] ?? ''),
                'real1' => (int) preg_replace('/[^\d]/', '', $row[10] ?? '0'),
                'real2' => (int) preg_replace('/[^\d]/', '', $row[11] ?? '0'),
                'real3' => (int) preg_replace('/[^\d]/', '', $row[12] ?? '0'),
                'real4' => (int) preg_replace('/[^\d]/', '', $row[13] ?? '0'),
                'real5' => (int) preg_replace('/[^\d]/', '', $row[14] ?? '0'),
                'real6' => (int) preg_replace('/[^\d]/', '', $row[15] ?? '0'),
                'real7' => (int) preg_replace('/[^\d]/', '', $row[16] ?? '0'),
                'real8' => (int) preg_replace('/[^\d]/', '', $row[17] ?? '0'),
                'real9' => (int) preg_replace('/[^\d]/', '', $row[18] ?? '0'),
                'real10' => (int) preg_replace('/[^\d]/', '', $row[19] ?? '0'),
                'real11' => (int) preg_replace('/[^\d]/', '', $row[20] ?? '0'),
                'real12' => (int) preg_replace('/[^\d]/', '', $row[21] ?? '0'),
                'total_realisasi' => $totalReal,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= 500) {
                DB::table('sintesa_belanja_bulanan')->insert($batch);
                $batch = [];
            }
            $totalRows++;
        }

        if (!empty($batch)) {
            DB::table('sintesa_belanja_bulanan')->insert($batch);
        }
        fclose($handle);

        $this->logSync(basename($filePath), 'BELANJA_KEWILAYAHAN', $totalRows);

        return [
            'rows' => $totalRows,
            'total_realisasi' => $grandRealisasi,
            'file' => basename($filePath)
        ];
    }

    /**
     * 3. Import Prioritas Presiden 2026 (73 paket)
     */
    public function importTematikPrioritas(string $filePath): array
    {
        DB::table('sintesa_tematik_prioritas')->truncate();
        $handle = fopen($filePath, 'r');
        if (!$handle) return ['status' => 'error', 'message' => 'Gagal membuka file Tematik'];

        $header = fgetcsv($handle);
        $batch = [];
        $totalRows = 0;
        $totalReal = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 25) continue;

            $realVal = (int) preg_replace('/[^\d]/', '', $row[24] ?? '0');
            $totalReal += $realVal;

            $batch[] = [
                'kementerian_kode' => trim($row[0] ?? ''),
                'kementerian_uraian' => trim($row[1] ?? ''),
                'satker_kode' => trim($row[4] ?? ''),
                'satker_uraian' => trim($row[5] ?? ''),
                'program_kode' => trim($row[6] ?? ''),
                'program_uraian' => trim($row[7] ?? ''),
                'kegiatan_kode' => trim($row[8] ?? ''),
                'kegiatan_uraian' => trim($row[9] ?? ''),
                'kdpriopres' => trim($row[10] ?? ''),
                'nmpriopres' => trim($row[11] ?? ''),
                'real1' => (int) preg_replace('/[^\d]/', '', $row[12] ?? '0'),
                'real2' => (int) preg_replace('/[^\d]/', '', $row[13] ?? '0'),
                'real3' => (int) preg_replace('/[^\d]/', '', $row[14] ?? '0'),
                'real4' => (int) preg_replace('/[^\d]/', '', $row[15] ?? '0'),
                'real5' => (int) preg_replace('/[^\d]/', '', $row[16] ?? '0'),
                'real6' => (int) preg_replace('/[^\d]/', '', $row[17] ?? '0'),
                'real7' => (int) preg_replace('/[^\d]/', '', $row[18] ?? '0'),
                'real8' => (int) preg_replace('/[^\d]/', '', $row[19] ?? '0'),
                'real9' => (int) preg_replace('/[^\d]/', '', $row[20] ?? '0'),
                'real10' => (int) preg_replace('/[^\d]/', '', $row[21] ?? '0'),
                'real11' => (int) preg_replace('/[^\d]/', '', $row[22] ?? '0'),
                'real12' => (int) preg_replace('/[^\d]/', '', $row[23] ?? '0'),
                'total_realisasi' => $realVal,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $totalRows++;
        }

        if (!empty($batch)) {
            DB::table('sintesa_tematik_prioritas')->insert($batch);
        }
        fclose($handle);

        $this->logSync(basename($filePath), 'TEMATIK_PRIORITAS_PRESIDEN', $totalRows);

        return [
            'rows' => $totalRows,
            'total_realisasi' => $totalReal,
            'file' => basename($filePath)
        ];
    }

    /**
     * 4. Import Dana Desa 2026 (22 Pemda se-Aceh)
     */
    public function importDanaDesa(string $filePath): array
    {
        DB::table('sintesa_tkd_dana_desa')->truncate();
        $handle = fopen($filePath, 'r');
        if (!$handle) return ['status' => 'error', 'message' => 'Gagal membuka file Dana Desa'];

        $header = fgetcsv($handle);
        $batch = [];
        $totalRows = 0;
        $totalPagu = 0;
        $totalReal = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 9) continue;

            $pagu = (int) preg_replace('/[^\d]/', '', $row[8] ?? '0');
            $real = (int) preg_replace('/[^\d]/', '', end($row) ?? '0');
            $pct = $pagu > 0 ? round(($real / $pagu) * 100, 2) : 0;

            $totalPagu += $pagu;
            $totalReal += $real;

            $batch[] = [
                'tahun' => trim($row[1] ?? '2026'),
                'kppn_kode' => trim($row[4] ?? ''),
                'kppn_nama' => trim($row[5] ?? ''),
                'lokasi_kode' => trim($row[6] ?? ''),
                'pemda_nama' => trim($row[7] ?? ''),
                'pagu' => $pagu,
                'realisasi' => $real,
                'persentase' => $pct,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $totalRows++;
        }

        if (!empty($batch)) {
            DB::table('sintesa_tkd_dana_desa')->insert($batch);
        }
        fclose($handle);

        $this->logSync(basename($filePath), 'TKD_DANA_DESA', $totalRows);

        return [
            'rows' => $totalRows,
            'total_pagu' => $totalPagu,
            'total_realisasi' => $totalReal,
            'file' => basename($filePath)
        ];
    }

    /**
     * 5. Import DAK Fisik 2025 Baseline (113 Sub-Bidang)
     */
    public function importDakFisik(string $filePath): array
    {
        DB::table('sintesa_tkd_dak_fisik')->truncate();
        $handle = fopen($filePath, 'r');
        if (!$handle) return ['status' => 'error', 'message' => 'Gagal membuka file DAK Fisik'];

        $header = fgetcsv($handle);
        $batch = [];
        $totalRows = 0;
        $totalSalur = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 16) continue;

            $pagu = (int) preg_replace('/[^\d]/', '', $row[13] ?? '0');
            $salur = (int) preg_replace('/[^\d]/', '', $row[14] ?? '0');
            $sisa = (int) preg_replace('/[^\d]/', '', $row[15] ?? '0');
            $pct = (float) str_replace(',', '.', trim($row[16] ?? '0'));
            $totalSalur += $salur;

            $batch[] = [
                'tahun' => trim($row[1] ?? '2025'),
                'lokasi_kode' => trim($row[2] ?? ''),
                'pemda_nama' => trim($row[3] ?? ''),
                'kppn_kode' => trim($row[5] ?? ''),
                'kppn_nama' => trim($row[6] ?? ''),
                'jenis_dana' => trim($row[8] ?? ''),
                'bidang_kode' => trim($row[9] ?? ''),
                'bidang_nama' => trim($row[10] ?? ''),
                'sub_bidang_kode' => trim($row[11] ?? ''),
                'sub_bidang_nama' => trim($row[12] ?? ''),
                'pagu' => $pagu,
                'penyaluran' => $salur,
                'sisa_pagu' => $sisa,
                'persentase' => $pct,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $totalRows++;
        }

        if (!empty($batch)) {
            DB::table('sintesa_tkd_dak_fisik')->insert($batch);
        }
        fclose($handle);

        $this->logSync(basename($filePath), 'TKD_DAK_FISIK', $totalRows);

        return [
            'rows' => $totalRows,
            'total_penyaluran' => $totalSalur,
            'file' => basename($filePath)
        ];
    }

    /**
     * 5b. Import Inquiry Data Tematik: Prioritas Presiden (dengan Pagu DIPA, Jan-Nov, Blokir)
     * Sekaligus membentuk ringkasan Modul Preservasi Jalan & Jembatan (kode 13 & 14)
     */
    public function importInquiryTematikPrioritas(string $filePath): array
    {
        DB::table('sintesa_tematik_prioritas')->truncate();
        $handle = fopen($filePath, 'r');
        if (!$handle) return ['status' => 'error', 'message' => 'Gagal membuka file Prioritas Presiden'];

        fgetcsv($handle);
        $batch = [];
        $items = [];
        $jalanItems = [];
        $clusters = [];
        $jalanOutputs = [];
        $totalPagu = 0;
        $totalReal = 0;
        $totalBlokir = 0;
        $jalanPagu = 0;
        $jalanReal = 0;
        $satkers = [];
        $jalanSatkers = [];
        $no = 1;
        $jNo = 1;

        $palette = ['#2563eb', '#10b981', '#0ea5e9', '#6366f1', '#f59e0b', '#f43f5e', '#8b5cf6', '#059669', '#14b8a6'];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 25) continue;
            if (trim($row[2] ?? '') !== '01') continue;

            $pagu = (int) preg_replace('/[^\d]/', '', $row[12] ?? '0');
            $months = [];
            for ($m = 0; $m < 11; $m++) {
                $months[$m] = (int) preg_replace('/[^\d]/', '', $row[13 + $m] ?? '0');
            }
            $real = array_sum($months);
            $blokir = (int) preg_replace('/[^\d]/', '', $row[24] ?? '0');

            $kodeJenis = trim($row[10] ?? '');
            $namaJenis = trim($row[11] ?? '');
            $satkerKode = trim($row[4] ?? '');
            $satkerNama = trim($row[5] ?? '');
            $klNama = trim($row[1] ?? '');
            $progKode = trim($row[6] ?? '');
            $progNama = trim($row[7] ?? '');
            $giatKode = trim($row[8] ?? '');
            $giatNama = trim($row[9] ?? '');

            $totalPagu += $pagu;
            $totalReal += $real;
            $totalBlokir += $blokir;
            $satkers[$satkerKode] = true;

            $pct = $pagu > 0 ? round(($real / $pagu) * 100, 2) : 0;

            $batch[] = [
                'kementerian_kode' => trim($row[0] ?? ''),
                'kementerian_uraian' => $klNama,
                'satker_kode' => $satkerKode,
                'satker_uraian' => $satkerNama,
                'program_kode' => $progKode,
                'program_uraian' => $progNama,
                'kegiatan_kode' => $giatKode,
                'kegiatan_uraian' => $giatNama,
                'kdpriopres' => $kodeJenis,
                'nmpriopres' => $namaJenis,
                'real1' => $months[0], 'real2' => $months[1], 'real3' => $months[2],
                'real4' => $months[3], 'real5' => $months[4], 'real6' => $months[5],
                'real7' => $months[6], 'real8' => $months[7], 'real9' => $months[8],
                'real10' => $months[9], 'real11' => $months[10], 'real12' => 0,
                'total_realisasi' => $real,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $items[] = [
                'no' => $no++,
                'kode_satker' => $satkerKode,
                'nama_satker' => $satkerNama,
                'kl' => $klNama,
                'wilayah' => 'Aceh',
                'kode_jenis' => $kodeJenis,
                'jenis' => $namaJenis,
                'kegiatan' => "[{$giatKode}] {$giatNama}",
                'program' => "[{$progKode}] {$progNama}",
                'pagu' => $pagu,
                'realisasi' => $real,
                'blokir' => $blokir,
                'pct' => $pct,
            ];

            if (!isset($clusters[$kodeJenis])) {
                $clusters[$kodeJenis] = [
                    'kode' => $kodeJenis,
                    'nama' => "[{$kodeJenis}] {$namaJenis}",
                    'label' => $namaJenis,
                    'pagu' => 0,
                    'realisasi' => 0,
                    'satkers' => [],
                    'paket_count' => 0,
                ];
            }
            $clusters[$kodeJenis]['pagu'] += $pagu;
            $clusters[$kodeJenis]['realisasi'] += $real;
            $clusters[$kodeJenis]['satkers'][$satkerKode] = true;
            $clusters[$kodeJenis]['paket_count']++;

            // Ekstrak data nyata Preservasi Jalan & Jembatan (kode 13 & 14)
            if (in_array($kodeJenis, ['13', '14'], true)) {
                $jalanPagu += $pagu;
                $jalanReal += $real;
                $jalanSatkers[$satkerKode] = true;

                $jalanItems[] = [
                    'no' => $jNo++,
                    'kode_satker' => $satkerKode,
                    'nama_satker' => $satkerNama,
                    'kl' => $klNama,
                    'wilayah' => 'Aceh',
                    'kode_out' => $kodeJenis,
                    'kegiatan' => "[{$giatKode}] {$giatNama}",
                    'output' => "[{$kodeJenis}] {$namaJenis}",
                    'program' => "[{$progKode}] {$progNama}",
                    'pagu' => $pagu,
                    'realisasi' => $real,
                    'pct' => $pct,
                ];

                if (!isset($jalanOutputs[$kodeJenis])) {
                    $jalanOutputs[$kodeJenis] = [
                        'kode' => $kodeJenis,
                        'nama' => "[{$kodeJenis}] {$namaJenis}",
                        'label' => $namaJenis,
                        'pagu' => 0,
                        'realisasi' => 0,
                        'row_count' => 0,
                    ];
                }
                $jalanOutputs[$kodeJenis]['pagu'] += $pagu;
                $jalanOutputs[$kodeJenis]['realisasi'] += $real;
                $jalanOutputs[$kodeJenis]['row_count']++;
            }
        }
        fclose($handle);

        if (!empty($batch)) {
            DB::table('sintesa_tematik_prioritas')->insert($batch);
        }

        // Susun jenis prioritas
        $jenisList = [];
        $idx = 0;
        uasort($clusters, fn($a, $b) => $b['pagu'] <=> $a['pagu']);
        foreach ($clusters as $c) {
            $jenisList[] = [
                'kode' => $c['kode'],
                'nama' => $c['nama'],
                'label' => $c['label'],
                'pagu' => $c['pagu'],
                'realisasi' => $c['realisasi'],
                'persen' => $c['pagu'] > 0 ? round(($c['realisasi'] / $c['pagu']) * 100, 2) : 0,
                'satker_count' => count($c['satkers']),
                'paket_count' => $c['paket_count'],
                'color' => $palette[$idx % count($palette)],
            ];
            $idx++;
        }

        $sisaPagu = $totalPagu - $totalReal;
        $pctReal = $totalPagu > 0 ? round(($totalReal / $totalPagu) * 100, 2) : 0;
        $this->saveSummary('tematik_prioritas_summary', [
            'has_pagu' => true,
            'total_pagu' => $totalPagu,
            'total_pagu_formatted' => 'Rp ' . number_format($totalPagu, 0, ',', '.'),
            'total_pagu_short' => round($totalPagu / 1e12, 2) . ' T',
            'total_realisasi' => $totalReal,
            'total_realisasi_formatted' => 'Rp ' . number_format($totalReal, 0, ',', '.'),
            'total_realisasi_short' => round($totalReal / 1e12, 2) . ' T',
            'total_blokir' => $totalBlokir,
            'sisa_pagu' => $sisaPagu,
            'sisa_pagu_formatted' => 'Rp ' . number_format($sisaPagu, 0, ',', '.'),
            'sisa_pagu_short' => 'Rp ' . round($sisaPagu / 1e12, 2) . ' T',
            'persen_realisasi' => $pctReal,
            'persen_sisa' => round(100 - $pctReal, 2),
            'total_satker' => count($satkers),
            'total_jenis' => count($jenisList),
            'total_ro' => count($items),
        ]);
        $this->saveSummary('tematik_prioritas_jenis', $jenisList);
        $this->saveSummary('tematik_prioritas_items', $items);

        // Simpan Modul Preservasi Jalan dari data Prioritas Presiden kode 13 & 14
        $jalanSisa = $jalanPagu - $jalanReal;
        $jalanPct = $jalanPagu > 0 ? round(($jalanReal / $jalanPagu) * 100, 2) : 0;
        $jalanOutList = [];
        foreach ($jalanOutputs as $jo) {
            $jo['persen'] = $jo['pagu'] > 0 ? round(($jo['realisasi'] / $jo['pagu']) * 100, 2) : 0;
            $jalanOutList[] = $jo;
        }
        $this->saveSummary('jalan_summary', [
            'total_pagu' => $jalanPagu,
            'total_pagu_formatted' => 'Rp ' . number_format($jalanPagu, 0, ',', '.'),
            'total_pagu_short' => round($jalanPagu / 1e12, 2) . ' T',
            'total_realisasi' => $jalanReal,
            'total_realisasi_formatted' => 'Rp ' . number_format($jalanReal, 0, ',', '.'),
            'sisa_pagu' => $jalanSisa,
            'sisa_pagu_formatted' => 'Rp ' . number_format($jalanSisa, 0, ',', '.'),
            'sisa_pagu_short' => 'Rp ' . round($jalanSisa / 1e12, 2) . ' T',
            'persen_realisasi' => $jalanPct,
            'persen_sisa' => round(100 - $jalanPct, 2),
            'total_satker' => count($jalanSatkers),
            'total_output' => count($jalanOutList),
            'total_rows' => count($jalanItems),
        ]);
        $this->saveSummary('jalan_outputs', $jalanOutList);
        $this->saveSummary('jalan_items', $jalanItems);

        $this->inquiryPrioritasImported = true;
        $this->logSync(basename($filePath), 'INQUIRY_PRIORITAS_PRESIDEN', count($items));

        return [
            'rows' => count($items),
            'total_pagu' => $totalPagu,
            'total_realisasi' => $totalReal,
            'file' => basename($filePath),
        ];
    }

    /**
     * 5c. Import Inquiry Data Tematik: Program Strategis (90 baris)
     */
    public function importInquiryProgramStrategis(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) return ['status' => 'error', 'message' => 'Gagal membuka file Program Strategis'];

        fgetcsv($handle);
        $items = [];
        $clusters = [];
        $totalPagu = 0;
        $totalReal = 0;
        $totalBlokir = 0;
        $satkers = [];
        $no = 1;
        $palette = ['#f43f5e', '#10b981', '#2563eb', '#059669', '#0ea5e9', '#f59e0b'];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 27) continue;
            if (trim($row[2] ?? '') !== '01') continue;

            $pagu = (int) preg_replace('/[^\d]/', '', $row[14] ?? '0');
            $real = 0;
            for ($m = 0; $m < 11; $m++) {
                $real += (int) preg_replace('/[^\d]/', '', $row[15 + $m] ?? '0');
            }
            $blokir = (int) preg_replace('/[^\d]/', '', $row[26] ?? '0');

            $satkerKode = trim($row[4] ?? '');
            $satkerNama = trim($row[5] ?? '');
            $klNama = trim($row[1] ?? '');
            $progKode = trim($row[6] ?? '');
            $progNama = trim($row[7] ?? '');
            $giatKode = trim($row[8] ?? '');
            $giatNama = trim($row[9] ?? '');
            $outKode = trim($row[10] ?? '');
            $outNama = trim($row[11] ?? '');
            $kodeJenis = trim($row[12] ?? '');
            $namaJenis = trim($row[13] ?? '');

            $totalPagu += $pagu;
            $totalReal += $real;
            $totalBlokir += $blokir;
            $satkers[$satkerKode] = true;

            $pct = $pagu > 0 ? round(($real / $pagu) * 100, 2) : 0;

            $items[] = [
                'no' => $no++,
                'kode_satker' => $satkerKode,
                'nama_satker' => $satkerNama,
                'kl' => $klNama,
                'wilayah' => 'Aceh',
                'kode_jenis' => $kodeJenis,
                'jenis' => $namaJenis,
                'kegiatan' => "[{$giatKode}] {$giatNama} · [{$outKode}] {$outNama}",
                'program' => "[{$progKode}] {$progNama}",
                'pagu' => $pagu,
                'realisasi' => $real,
                'pct' => $pct,
                'blokir' => $blokir,
            ];

            if (!isset($clusters[$kodeJenis])) {
                $clusters[$kodeJenis] = [
                    'kode' => $kodeJenis,
                    'nama' => "[{$kodeJenis}] {$namaJenis}",
                    'label' => $namaJenis,
                    'pagu' => 0,
                    'realisasi' => 0,
                    'ro_count' => 0,
                ];
            }
            $clusters[$kodeJenis]['pagu'] += $pagu;
            $clusters[$kodeJenis]['realisasi'] += $real;
            $clusters[$kodeJenis]['ro_count']++;
        }
        fclose($handle);

        $jenisList = [];
        $idx = 0;
        uasort($clusters, fn($a, $b) => $b['pagu'] <=> $a['pagu']);
        foreach ($clusters as $c) {
            $jenisList[] = [
                'kode' => $c['kode'],
                'nama' => $c['nama'],
                'label' => $c['label'],
                'pagu' => $c['pagu'],
                'realisasi' => $c['realisasi'],
                'persen' => $c['pagu'] > 0 ? round(($c['realisasi'] / $c['pagu']) * 100, 2) : 0,
                'ro_count' => $c['ro_count'],
                'color' => $palette[$idx % count($palette)],
            ];
            $idx++;
        }

        $sisaPagu = $totalPagu - $totalReal;
        $pctReal = $totalPagu > 0 ? round(($totalReal / $totalPagu) * 100, 2) : 0;

        $this->saveSummary('program_strategis_summary', [
            'total_pagu' => $totalPagu,
            'total_pagu_formatted' => 'Rp ' . number_format($totalPagu, 0, ',', '.'),
            'total_pagu_short' => round($totalPagu / 1e12, 2) . ' T',
            'total_realisasi' => $totalReal,
            'total_realisasi_formatted' => 'Rp ' . number_format($totalReal, 0, ',', '.'),
            'total_blokir' => $totalBlokir,
            'sisa_pagu' => $sisaPagu,
            'sisa_pagu_formatted' => 'Rp ' . number_format($sisaPagu, 0, ',', '.'),
            'sisa_pagu_short' => 'Rp ' . round($sisaPagu / 1e12, 2) . ' T',
            'persen_realisasi' => $pctReal,
            'persen_sisa' => round(100 - $pctReal, 2),
            'total_satker' => count($satkers),
            'total_jenis' => count($jenisList),
            'total_ro' => count($items),
        ]);
        $this->saveSummary('program_strategis_jenis', $jenisList);
        $this->saveSummary('program_strategis_items', $items);

        $this->logSync(basename($filePath), 'PROGRAM_STRATEGIS', count($items));

        return [
            'rows' => count($items),
            'total_pagu' => $totalPagu,
            'total_realisasi' => $totalReal,
            'file' => basename($filePath),
        ];
    }

    /**
     * 5d. Import Inquiry Data Tematik: Bantuan Pemerintah (85 baris)
     */
    public function importInquiryBantuanPemerintah(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) return ['status' => 'error', 'message' => 'Gagal membuka file Bantuan Pemerintah'];

        fgetcsv($handle);
        $items = [];
        $no = 1;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 25) continue;
            if (trim($row[2] ?? '') !== '01') continue;
            $pagu = (int) preg_replace('/[^\d]/', '', $row[12] ?? '0');
            $real = 0;
            for ($m = 0; $m < 11; $m++) {
                $real += (int) preg_replace('/[^\d]/', '', $row[13 + $m] ?? '0');
            }
            $blokir = (int) preg_replace('/[^\d]/', '', $row[24] ?? '0');
            $items[] = [
                'no' => $no++,
                'kode_satker' => trim($row[4] ?? ''),
                'nama_satker' => trim($row[5] ?? ''),
                'kl' => trim($row[1] ?? ''),
                'kegiatan' => '[' . trim($row[8] ?? '') . '] ' . trim($row[9] ?? ''),
                'output' => '[' . trim($row[10] ?? '') . '] ' . trim($row[11] ?? ''),
                'pagu' => $pagu,
                'realisasi' => $real,
                'blokir' => $blokir,
                'pct' => $pagu > 0 ? round(($real / $pagu) * 100, 2) : 0,
            ];
        }
        fclose($handle);
        $this->saveSummary('bantuan_pemerintah_items', $items);
        $this->logSync(basename($filePath), 'BANTUAN_PEMERINTAH', count($items));

        return ['rows' => count($items), 'file' => basename($filePath)];
    }

    /**
     * 5e. Import Bansos Kewilayahan (BPNT, PKH, POS_BST - 207 baris dengan KPM Penerima)
     */
    public function importBansosKewilayahan(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) return ['status' => 'error', 'message' => 'Gagal membuka file Bansos'];

        $header = fgetcsv($handle);
        $hasPenerima = in_array('total_penerima', array_map('strtolower', $header ?: []));
        $items = [];
        $jenisAgg = [];
        $kabkotaSet = [];
        $satkerSet = [];
        $totalReal = 0;
        $totalPenerima = 0;
        $no = 1;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 23) continue;
            if (trim($row[4] ?? '') !== '01') continue;

            $jenis = trim($row[8] ?? '');
            $tahap = trim($row[9] ?? '');
            $kabkota = trim($row[3] ?? '');
            $satkerKode = trim($row[6] ?? '');
            $satkerNama = trim($row[7] ?? '');
            $klNama = trim($row[1] ?? '');

            $real = $hasPenerima
                ? (int) preg_replace('/[^\d]/', '', $row[34] ?? '0')
                : (int) preg_replace('/[^\d]/', '', $row[22] ?? '0');
            $penerima = $hasPenerima
                ? (int) preg_replace('/[^\d]/', '', $row[35] ?? '0')
                : 0;

            $totalReal += $real;
            $totalPenerima += $penerima;
            $kabkotaSet[$kabkota] = true;
            $satkerSet[$satkerKode] = true;

            $jenisLabel = match ($jenis) {
                'BPNT' => 'Bantuan Pangan Non-Tunai (Sembako)',
                'PKH' => 'Program Keluarga Harapan (PKH)',
                'POS_BST' => 'Bantuan Sosial Tunai via PT Pos',
                default => $jenis,
            };

            $items[] = [
                'no' => $no++,
                'kode_satker' => $satkerKode,
                'nama_satker' => $satkerNama,
                'kl' => $klNama,
                'wilayah' => $kabkota,
                'kegiatan_kode' => $tahap,
                'kegiatan' => "Tahap {$tahap} · {$jenisLabel}",
                'kegiatan_nama' => "{$jenisLabel} (Tahap {$tahap})",
                'kode_out' => $jenis,
                'output' => "[{$jenis}] {$jenisLabel}",
                'output_nama' => $jenisLabel,
                'program' => 'Penyaluran Bansos Kewilayahan Aceh',
                'pagu' => $real,
                'realisasi' => $real,
                'sisa' => 0,
                'penerima' => $penerima,
                'pct' => 100.0,
                'status' => 'Tersalur',
                'icon' => $jenis === 'BPNT' ? 'shield' : ($jenis === 'PKH' ? 'heart' : 'tools'),
                'label_singkat' => "{$kabkota} · Tahap {$tahap}",
                'deskripsi' => "Penyaluran {$jenisLabel} Tahap {$tahap} wilayah {$kabkota} kepada " . number_format($penerima, 0, ',', '.') . " KPM penerima.",
            ];

            if (!isset($jenisAgg[$jenis])) {
                $jenisAgg[$jenis] = [
                    'kode_out' => $jenis,
                    'output' => "[{$jenis}] {$jenisLabel}",
                    'output_singkat' => $jenisLabel,
                    'pagu' => 0,
                    'realisasi' => 0,
                    'sisa' => 0,
                    'penerima' => 0,
                    'pct' => 100.0,
                    'jumlah_paket' => 0,
                    'paket_names' => $jenisLabel,
                    'deskripsi' => "Total penyaluran {$jenisLabel} di Provinsi Aceh.",
                ];
            }
            $jenisAgg[$jenis]['pagu'] += $real;
            $jenisAgg[$jenis]['realisasi'] += $real;
            $jenisAgg[$jenis]['penerima'] += $penerima;
            $jenisAgg[$jenis]['jumlah_paket']++;
        }
        fclose($handle);

        $outputs = array_values($jenisAgg);
        foreach ($outputs as &$o) {
            $o['pct'] = $totalReal > 0 ? round(($o['realisasi'] / $totalReal) * 100, 2) : 0;
        }
        usort($outputs, fn($a, $b) => $b['realisasi'] <=> $a['realisasi']);

        $this->saveSummary('bansos_summary', [
            'has_pagu' => false,
            'total_pagu' => $totalReal,
            'total_realisasi' => $totalReal,
            'total_sisa' => 0,
            'total_penerima' => $totalPenerima,
            'total_penerima_formatted' => number_format($totalPenerima, 0, ',', '.'),
            'persen_realisasi' => 100.0,
            'persen_sisa' => 0,
            'total_satker' => count($satkerSet),
            'total_kabkota' => count($kabkotaSet),
            'total_output' => count($outputs),
            'total_paket' => count($items),
            'total_pagu_formatted' => 'Rp ' . number_format($totalReal, 0, ',', '.'),
            'total_realisasi_formatted' => 'Rp ' . number_format($totalReal, 0, ',', '.'),
            'sisa_pagu_formatted' => 'Rp 0',
            'total_pagu_short' => round($totalReal / 1e12, 2) . ' T',
            'total_realisasi_short' => round($totalReal / 1e12, 2) . ' T',
            'sisa_pagu_short' => '0',
            'cut_off' => date('d F Y'),
        ]);
        $this->saveSummary('bansos_outputs', $outputs);
        $this->saveSummary('bansos_items', $items);

        $this->logSync(basename($filePath), 'BANSOS_KEWILAYAHAN', count($items));

        return [
            'rows' => count($items),
            'total_realisasi' => $totalReal,
            'total_penerima' => $totalPenerima,
            'file' => basename($filePath),
        ];
    }

    /**
     * 5f. Import TKD Nonfisik: BOS/BOP 2026 & TPG 2026
     */
    public function importBosAndTpg(?string $bosFile, ?string $tpgFile): array
    {
        $bosTotal = 0; $bosSiswa = 0; $bosRows = 0;
        if ($bosFile && ($fh = fopen($bosFile, 'r'))) {
            fgetcsv($fh);
            while (($r = fgetcsv($fh)) !== false) {
                if (count($r) < 26) continue;
                $bosTotal += (int) preg_replace('/[^\d]/', '', $r[24] ?? '0');
                $bosSiswa += (int) preg_replace('/[^\d]/', '', $r[25] ?? '0');
                $bosRows++;
            }
            fclose($fh);
            $this->logSync(basename($bosFile), 'TKD_BOS_BOP', $bosRows);
        }

        $tpgTotal = 0; $tpgRows = 0;
        if ($tpgFile && ($fh = fopen($tpgFile, 'r'))) {
            fgetcsv($fh);
            while (($r = fgetcsv($fh)) !== false) {
                if (count($r) < 22) continue;
                $tpgTotal += (int) preg_replace('/[^\d]/', '', $r[21] ?? '0');
                $tpgRows++;
            }
            fclose($fh);
            $this->logSync(basename($tpgFile), 'TKD_TPG', $tpgRows);
        }

        $extra = [
            'bos_bop' => ['rows' => $bosRows, 'total_nilai' => $bosTotal, 'total_siswa' => $bosSiswa],
            'tpg' => ['rows' => $tpgRows, 'total_nilai' => $tpgTotal],
        ];
        $this->saveSummary('tkd_extra_summary', $extra);
        return $extra;
    }

    /**
     * 6. Generate Pre-Aggregated Summary Tables untuk Instant High-Speed Load
     */
    public function generateExecutiveSummary(): array
    {
        // 1. KPI Regional Utama (Khusus Satker K/L - Non BA 999 BUN sesuai kebijakan dashboard eksekutif)
        $klPaguQuery = DB::table('sintesa_satker_pagu')
            ->where('kementerian_kode', '!=', '999')
            ->where('kementerian_uraian', 'not like', '%BENDAHARA UMUM NEGARA%');
        $klBelanjaQuery = DB::table('sintesa_belanja_bulanan')
            ->where('kementerian_kode', '!=', '999')
            ->where('kementerian_uraian', 'not like', '%BENDAHARA UMUM NEGARA%');

        $totalPagu = (int) (clone $klPaguQuery)->sum('pagu');
        $totalBlokir = (int) (clone $klPaguQuery)->sum('blokir');
        $totalRealisasi = (int) (clone $klBelanjaQuery)->sum('total_realisasi');
        $paguEfektif = $totalPagu - $totalBlokir;
        $sisaPagu = $totalPagu - $totalRealisasi;
        $sisaPaguEfektif = $paguEfektif - $totalRealisasi;

        $persenRealisasi = $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100, 2) : 0;
        $persenBlokir = $totalPagu > 0 ? round(($totalBlokir / $totalPagu) * 100, 2) : 0;
        $persenSisa = $totalPagu > 0 ? round(($sisaPagu / $totalPagu) * 100, 2) : 0;

        $kpiData = [
            'total_pagu' => $totalPagu,
            'total_pagu_formatted' => 'Rp ' . number_format($totalPagu, 0, ',', '.'),
            'total_realisasi' => $totalRealisasi,
            'total_realisasi_formatted' => 'Rp ' . number_format($totalRealisasi, 0, ',', '.'),
            'persen_realisasi' => $persenRealisasi,
            'total_blokir' => $totalBlokir,
            'total_blokir_formatted' => 'Rp ' . number_format($totalBlokir, 0, ',', '.'),
            'persen_blokir' => $persenBlokir,
            'pagu_efektif' => $paguEfektif,
            'pagu_efektif_formatted' => 'Rp ' . number_format($paguEfektif, 0, ',', '.'),
            'sisa_pagu' => $sisaPagu,
            'sisa_pagu_formatted' => 'Rp ' . number_format($sisaPagu, 0, ',', '.'),
            'persen_sisa' => $persenSisa,
            'sisa_pagu_efektif' => $sisaPaguEfektif,
            'sisa_pagu_efektif_formatted' => 'Rp ' . number_format($sisaPaguEfektif, 0, ',', '.'),
            'realisasi_sp2d_cutoff_formatted' => 'Rp ' . number_format($totalRealisasi, 0, ',', '.'),
            'cutoff_date' => date('d F Y'),
            'cutoff_time' => date('d F Y \p\u\k\u\l H.i \W\I\B'),
            'total_satker_count' => (clone $klPaguQuery)->distinct('satker_kode')->count('satker_kode'),
        ];
        $this->saveSummary('executive_kpi', $kpiData);

        // 2. Ranking KPPN se-Aceh (Satker K/L Non-BUN dicocokkan per satker KPPN)
        $kppnPagu = (clone $klPaguQuery)
            ->select('kppn_kode', 'kppn_uraian', DB::raw('SUM(pagu) as total_pagu'), DB::raw('SUM(blokir) as total_blokir'))
            ->groupBy('kppn_kode', 'kppn_uraian')
            ->get()
            ->keyBy('kppn_kode');

        $kppnRanking = [];
        foreach ($kppnPagu as $kode => $kp) {
            $pagu = (int) $kp->total_pagu;
            $kppnSatkers = (clone $klPaguQuery)
                ->where('kppn_kode', $kode)
                ->distinct()
                ->pluck('satker_kode')
                ->toArray();
            $real = (int) (clone $klBelanjaQuery)
                ->whereIn('satker_kode', $kppnSatkers)
                ->sum('total_realisasi');
            $pct = $pagu > 0 ? round(($real / $pagu) * 100, 2) : 0;

            $kppnRanking[] = [
                'kode' => $kode,
                'nama' => $kp->kppn_uraian,
                'pagu' => $pagu,
                'pagu_formatted' => 'Rp ' . number_format($pagu, 0, ',', '.'),
                'realisasi' => $real,
                'realisasi_formatted' => 'Rp ' . number_format($real, 0, ',', '.'),
                'persentase' => $pct,
                'persentase_formatted' => number_format($pct, 2, ',', '.') . '%',
            ];
        }
        usort($kppnRanking, fn($a, $b) => $b['persentase'] <=> $a['persentase']);
        $this->saveSummary('kppn_ranking', $kppnRanking);

        // 3. Tren Bulanan Realisasi Jan - Des (Line Chart Satker K/L)
        $monthlyTrend = (clone $klBelanjaQuery)
            ->selectRaw('
                SUM(real1) as m1, SUM(real2) as m2, SUM(real3) as m3, SUM(real4) as m4,
                SUM(real5) as m5, SUM(real6) as m6, SUM(real7) as m7, SUM(real8) as m8,
                SUM(real9) as m9, SUM(real10) as m10, SUM(real11) as m11, SUM(real12) as m12
            ')
            ->first();

        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyData = [];
        $accumulative = 0;
        for ($i = 1; $i <= 12; $i++) {
            $val = (int) ($monthlyTrend->{"m{$i}"} ?? 0);
            $accumulative += $val;
            $monthlyData[] = [
                'bulan' => $bulanLabels[$i - 1],
                'nominal' => $val,
                'nominal_formatted' => 'Rp ' . number_format($val, 0, ',', '.'),
                'kumulatif' => $accumulative,
                'kumulatif_formatted' => 'Rp ' . number_format($accumulative, 0, ',', '.'),
            ];
        }
        $this->saveSummary('monthly_trend', $monthlyData);

        // 4. Komposisi 4 Jenis Belanja (Akun 51, 52, 53, 57)
        $jenisAkunDef = [
            '51' => 'Belanja Pegawai',
            '52' => 'Belanja Barang',
            '53' => 'Belanja Modal',
            '57' => 'Bantuan Sosial',
        ];

        $paguAkun = DB::table('sintesa_satker_pagu')
            ->select(DB::raw('SUBSTR(akun_kode, 1, 2) as prefix'), DB::raw('SUM(pagu) as total_pagu'))
            ->groupBy('prefix')
            ->get()
            ->keyBy('prefix');

        $realAkun = DB::table('sintesa_belanja_bulanan')
            ->select(DB::raw('SUBSTR(akun_kode, 1, 2) as prefix'), DB::raw('SUM(total_realisasi) as total_real'))
            ->groupBy('prefix')
            ->get()
            ->keyBy('prefix');

        $jenisBelanjaData = [];
        foreach ($jenisAkunDef as $prefix => $nama) {
            $p = (int) ($paguAkun[$prefix]->total_pagu ?? 0);
            $r = (int) ($realAkun[$prefix]->total_real ?? 0);
            $pct = $p > 0 ? round(($r / $p) * 100, 2) : 0;

            $jenisBelanjaData[] = [
                'kode' => $prefix,
                'nama' => $nama,
                'pagu' => $p,
                'pagu_formatted' => 'Rp ' . number_format($p, 0, ',', '.'),
                'realisasi' => $r,
                'realisasi_formatted' => 'Rp ' . number_format($r, 0, ',', '.'),
                'persentase' => $pct,
                'persentase_formatted' => number_format($pct, 2, ',', '.') . '%',
            ];
        }
        $this->saveSummary('jenis_belanja', $jenisBelanjaData);

        // 4b. Komposisi Lengkap 10 Jenis Belanja (51-57 K/L + 61-66 TKD BA BUN dari Database SQLite)
        $tkdDanaDesaColl = DB::table('sintesa_tkd_dana_desa')->orderBy('lokasi_kode')->get();
        $ddTotalPagu = (int) $tkdDanaDesaColl->sum('pagu');
        $ddTotalReal = (int) $tkdDanaDesaColl->sum('realisasi');

        $allAkunDef = [
            '51' => 'Belanja Pegawai',
            '52' => 'Belanja Barang',
            '53' => 'Belanja Modal',
            '57' => 'Bantuan Sosial',
            '61' => 'Dana Bagi Hasil',
            '62' => 'DAU',
            '63' => 'DAK Fisik',
            '64' => 'Otsus & Keistimewaan',
            '65' => 'DAK Nonfisik & Tunj. Guru',
            '66' => 'Dana Desa',
        ];
        $jenisBelanjaAll = [];
        foreach ($allAkunDef as $prefix => $nama) {
            if ($prefix === '66') {
                $p = $ddTotalPagu > 0 ? $ddTotalPagu : (int) ($paguAkun['66']->total_pagu ?? 0);
                $r = $ddTotalReal;
            } else {
                $p = (int) ($paguAkun[$prefix]->total_pagu ?? 0);
                $r = (int) ($realAkun[$prefix]->total_real ?? 0);
            }
            $pct = $p > 0 ? round(($r / $p) * 100, 2) : 0;
            $jenisBelanjaAll[$prefix] = [
                'kode' => $prefix,
                'nama' => $nama,
                'pagu' => $p,
                'pagu_formatted' => 'Rp ' . number_format($p, 0, ',', '.'),
                'realisasi' => $r,
                'realisasi_formatted' => 'Rp ' . number_format($r, 0, ',', '.'),
                'persentase' => $pct,
                'persentase_formatted' => number_format($pct, 2, ',', '.') . '%',
            ];
        }
        $this->saveSummary('jenis_belanja_all', $jenisBelanjaAll);

        // 5. Prioritas Presiden Clusters (fallback jika hanya file belwil_tematik lama)
        if (!$this->inquiryPrioritasImported) {
            $prioClusters = DB::table('sintesa_tematik_prioritas')
                ->select('kdpriopres', 'nmpriopres', DB::raw('COUNT(*) as total_paket'), DB::raw('COUNT(DISTINCT satker_kode) as satker_count'), DB::raw('SUM(total_realisasi) as total_realisasi'))
                ->groupBy('kdpriopres', 'nmpriopres')
                ->orderByDesc('total_realisasi')
                ->get();
            $this->saveSummary('tematik_prioritas', $prioClusters);

            $prioColors = ['#2563eb', '#10b981', '#0ea5e9', '#6366f1', '#f59e0b', '#f43f5e', '#8b5cf6', '#059669', '#14b8a6', '#ec4899'];
            $prioJenisList = [];
            foreach ($prioClusters as $idx => $pc) {
                $prioJenisList[] = [
                    'kode' => $pc->kdpriopres,
                    'nama' => "[{$pc->kdpriopres}] {$pc->nmpriopres}",
                    'label' => $pc->nmpriopres,
                    'pagu' => 0,
                    'realisasi' => (int) $pc->total_realisasi,
                    'persen' => 0,
                    'satker_count' => (int) $pc->satker_count,
                    'paket_count' => (int) $pc->total_paket,
                    'color' => $prioColors[$idx % count($prioColors)],
                ];
            }
            $this->saveSummary('tematik_prioritas_jenis', $prioJenisList);
        }

        // 6. TKD Dana Desa 2026 & DAK Fisik 2025 Summary
        $tkdDakFisik = DB::table('sintesa_tkd_dak_fisik')->orderBy('lokasi_kode')->get();
        $this->saveSummary('tkd_summary', [
            'dana_desa' => $tkdDanaDesaColl,
            'dak_fisik' => $tkdDakFisik,
            'dana_desa_total_pagu' => $ddTotalPagu,
            'dana_desa_total_realisasi' => $ddTotalReal,
            'dak_fisik_total_salur' => (int) $tkdDakFisik->sum('penyaluran'),
        ]);

        // 6b. BA BUN Regional Data (21 Satker BUN di 7 KPPN dengan Pagu & Realisasi Asli dari SQLite)
        $bunRows = DB::table('sintesa_satker_pagu')
            ->where('kementerian_kode', '999')
            ->select('kppn_kode', 'kppn_uraian', 'satker_kode', 'satker_uraian', DB::raw('SUM(pagu) as total_pagu'), DB::raw('SUM(blokir) as total_blokir'))
            ->groupBy('kppn_kode', 'kppn_uraian', 'satker_kode', 'satker_uraian')
            ->orderBy('kppn_kode')
            ->orderBy('satker_kode')
            ->get();

        $bunRealBySatker = DB::table('sintesa_belanja_bulanan')
            ->where('kementerian_kode', '999')
            ->select('satker_kode', DB::raw('SUM(total_realisasi) as total_real'))
            ->groupBy('satker_kode')
            ->pluck('total_real', 'satker_kode');

        $bunTotalPagu = (int) $bunRows->sum('total_pagu');
        $bunTotalBlokir = (int) $bunRows->sum('total_blokir');
        $bunTotalReal = (int) $bunRows->sum(fn($r) => (int) ($bunRealBySatker[$r->satker_kode] ?? 0));

        $bun500Pagu = (int) $bunRows->filter(fn($r) => str_starts_with($r->satker_kode, '5'))->sum('total_pagu');
        $bun500Real = (int) $bunRows->filter(fn($r) => str_starts_with($r->satker_kode, '5'))->sum(fn($r) => (int) ($bunRealBySatker[$r->satker_kode] ?? 0));
        $bun600Pagu = (int) $bunRows->filter(fn($r) => str_starts_with($r->satker_kode, '6'))->sum('total_pagu');
        $bun600Real = (int) $bunRows->filter(fn($r) => str_starts_with($r->satker_kode, '6'))->sum(fn($r) => (int) ($bunRealBySatker[$r->satker_kode] ?? 0));
        $bun700Pagu = (int) $bunRows->filter(fn($r) => str_starts_with($r->satker_kode, '7'))->sum('total_pagu');
        $bun700Real = (int) $bunRows->filter(fn($r) => str_starts_with($r->satker_kode, '7'))->sum(fn($r) => (int) ($bunRealBySatker[$r->satker_kode] ?? 0));

        $bunPctAll = $bunTotalPagu > 0 ? round(($bunTotalReal / $bunTotalPagu) * 100, 2) : 0;
        $bunRegionalData = [
            'ALL' => [
                'label' => 'Semua KPPN Aceh (' . $bunRows->count() . ' Satker BUN)',
                'labels' => [
                    ['500xxx', 'TRANSFER UMUM (DAU & DBH)'],
                    ['600xxx', 'TRANSFER KHUSUS (DAK)'],
                    ['700xxx', 'DANA DESA, OTSUS & IF']
                ],
                'fullLabels' => [
                    'Satker Penyalur Transfer Umum (DAU & DBH) - 7 KPPN Se-Aceh',
                    'Satker Penyalur Transfer Khusus (DAK Fisik & Nonfisik) - 7 KPPN Se-Aceh',
                    'Satker Penyalur Dana Desa, Insentif Fiskal & Otsus - 7 KPPN Se-Aceh'
                ],
                'pagu' => [
                    round($bun500Pagu / 1e9, 1),
                    round($bun600Pagu / 1e9, 1),
                    round($bun700Pagu / 1e9, 1)
                ],
                'realisasi' => [
                    round($bun500Real / 1e9, 1),
                    round($bun600Real / 1e9, 1),
                    round($bun700Real / 1e9, 1)
                ],
                'cardPagu' => 'Rp ' . number_format($bunTotalPagu, 0, ',', '.'),
                'subPagu' => 'Alokasi ' . $bunRows->count() . ' Satker Penyalur BUN · 7 KPPN Se-Aceh',
                'cardRealisasi' => 'Rp ' . number_format($bunTotalReal, 0, ',', '.'),
                'subRealisasi' => number_format($bunPctAll, 2, ',', '.') . '% Capaian Penyaluran BUN',
                'cardBlokir' => 'Rp ' . number_format($bunTotalBlokir, 0, ',', '.'),
                'subBlokir' => $bunTotalBlokir === 0 ? 'Nihil / Tidak Ada Blokir' : 'Pagu Terblokir BA 999',
                'footer' => 'Satker Penyalur Transfer Ke Daerah (TKD) · Database SQLite Kanwil DJPb Aceh'
            ]
        ];

        $bunByKppn = $bunRows->groupBy('kppn_kode')->sortByDesc(fn($grp) => $grp->sum('total_pagu'));
        foreach ($bunByKppn as $kppnKode => $grp) {
            $kppnNama = ucwords(strtolower(str_replace('  ', ' ', str_replace('L A N G S A', 'Langsa', trim($grp->first()->kppn_uraian)))));
            $kPagu = (int) $grp->sum('total_pagu');
            $kBlokir = (int) $grp->sum('total_blokir');
            $kReal = (int) $grp->sum(fn($sat) => (int) ($bunRealBySatker[$sat->satker_kode] ?? 0));
            $kPct = $kPagu > 0 ? round(($kReal / $kPagu) * 100, 2) : 0;

            $kLabels = [];
            $kFull = [];
            $kPaguArr = [];
            $kRealArr = [];
            foreach ($grp as $sat) {
                $shortType = str_starts_with($sat->satker_kode, '5') ? 'TRANSFER UMUM' : (str_starts_with($sat->satker_kode, '6') ? 'TRANSFER KHUSUS' : 'DANA DESA & OTSUS');
                $kLabels[] = [$sat->satker_kode, $shortType];
                $kFull[] = "{$sat->satker_kode} {$sat->satker_uraian}";
                $kPaguArr[] = round($sat->total_pagu / 1e9, 1);
                $kRealArr[] = round(((int) ($bunRealBySatker[$sat->satker_kode] ?? 0)) / 1e9, 1);
            }

            $bunRegionalData['kppn_' . $kppnKode] = [
                'label' => "KPPN {$kppnNama} ({$grp->count()} Satker)",
                'labels' => $kLabels,
                'fullLabels' => $kFull,
                'pagu' => $kPaguArr,
                'realisasi' => $kRealArr,
                'cardPagu' => 'Rp ' . number_format($kPagu, 0, ',', '.'),
                'subPagu' => "Alokasi {$grp->count()} Satker BUN · KPPN {$kppnNama}",
                'cardRealisasi' => 'Rp ' . number_format($kReal, 0, ',', '.'),
                'subRealisasi' => number_format($kPct, 2, ',', '.') . '% Capaian KPPN ' . $kppnNama,
                'cardBlokir' => 'Rp ' . number_format($kBlokir, 0, ',', '.'),
                'subBlokir' => $kBlokir === 0 ? 'Nihil / Tidak Ada Blokir' : 'Pagu Terblokir',
                'footer' => "Wilayah KPPN {$kppnNama} · Database SQLite Kanwil DJPb Aceh"
            ];
        }
        $this->saveSummary('bun_regional_data', $bunRegionalData);

        // 6c. Detail TKD & BA BUN (untuk Halaman /tkd agar 100% sesuai Database SQLite)
        $dbhRows = DB::table('sintesa_satker_pagu')
            ->where('kementerian_kode', '999')
            ->where('akun_kode', 'like', '61%')
            ->select('akun_kode', 'akun_uraian', DB::raw('SUM(pagu) as pagu'))
            ->groupBy('akun_kode', 'akun_uraian')
            ->orderByDesc('pagu')
            ->get();

        $dbhRealMap = DB::table('sintesa_belanja_bulanan')
            ->where('kementerian_kode', '999')
            ->where('akun_kode', 'like', '61%')
            ->select('akun_kode', DB::raw('SUM(total_realisasi) as realisasi'))
            ->groupBy('akun_kode')
            ->pluck('realisasi', 'akun_kode');

        $dbhItems = [];
        $dbhNo = 1;
        foreach ($dbhRows as $dr) {
            $uraian = $dr->akun_uraian;
            $paguD = (int) $dr->pagu;
            $realD = (int) ($dbhRealMap[$dr->akun_kode] ?? 0);
            $sisaD = max(0, $paguD - $realD);
            $pctD = $paguD > 0 ? round(($realD / $paguD) * 100, 2) : 0;
            $kat = str_contains($uraian, 'PPh') || str_contains($uraian, 'PBB') ? 'Pajak'
                : (str_contains($uraian, 'Cukai') ? 'Cukai'
                : (str_contains($uraian, 'Gas') || str_contains($uraian, 'Minyak') ? 'SDA Migas'
                : (str_contains($uraian, 'Kehutanan') ? 'SDA Kehutanan'
                : (str_contains($uraian, 'Perikanan') ? 'SDA Perikanan'
                : (str_contains($uraian, 'Sawit') ? 'SDA Perkebunan' : 'SDA Minerba')))));
            $dbhItems[] = [
                'no' => $dbhNo++,
                'uraian' => $uraian,
                'kategori' => $kat,
                'pagu' => $paguD,
                'realisasi' => $realD,
                'sisa' => $sisaD,
                'pct' => $pctD,
                'status' => $pctD >= 95 ? 'Optimal' : ($pctD >= 80 ? 'On Track' : 'Perhatian'),
            ];
        }

        $otsusRows = DB::table('sintesa_satker_pagu')
            ->where('kementerian_kode', '999')
            ->where(function ($q) {
                $q->where('akun_kode', 'like', '64%')
                  ->orWhere('akun_uraian', 'like', '%Otonomi Khusus%');
            })
            ->select('akun_kode', 'akun_uraian', DB::raw('SUM(pagu) as pagu'))
            ->groupBy('akun_kode', 'akun_uraian')
            ->orderByDesc('pagu')
            ->get();
        $otsusRealMap = DB::table('sintesa_belanja_bulanan')
            ->where('kementerian_kode', '999')
            ->whereIn('akun_kode', $otsusRows->pluck('akun_kode')->toArray())
            ->select('akun_kode', DB::raw('SUM(total_realisasi) as realisasi'))
            ->groupBy('akun_kode')
            ->pluck('realisasi', 'akun_kode');

        $totalOtsusPagu = (int) $otsusRows->sum('pagu');
        $otsusItems = [];
        $otNo = 1;
        foreach ($otsusRows as $or) {
            $paguO = (int) $or->pagu;
            $realO = (int) ($otsusRealMap[$or->akun_kode] ?? 0);
            $sisaO = max(0, $paguO - $realO);
            $pctO = $paguO > 0 ? round(($realO / $paguO) * 100, 2) : 0;
            $kodeSat = str_starts_with($or->akun_kode, '64') ? '700xxx' : '500xxx';
            $namaSat = str_starts_with($or->akun_kode, '64') ? 'SATKER PENYALUR DANA DESA & OTSUS (7 KPPN)' : 'SATKER PENYALUR TRANSFER UMUM (7 KPPN)';
            $porsi = $totalOtsusPagu > 0 ? round(($paguO / $totalOtsusPagu) * 100, 1) : 0;
            $otsusItems[] = [
                'no' => $otNo++,
                'uraian' => $or->akun_uraian,
                'kode_satker' => $kodeSat,
                'nama_satker' => $namaSat,
                'pagu' => $paguO,
                'realisasi' => $realO,
                'sisa' => $sisaO,
                'pct' => $pctO,
                'porsi' => $porsi,
                'status' => $pctO >= 95 ? 'Optimal' : ($pctO >= 80 ? 'On Track' : 'Perhatian'),
                'keterangan' => 'Alokasi & Realisasi resmi BA 999 wilayah Aceh (Dataset SINTESA Pagu Realisasi).',
            ];
        }

        $pct500 = $bun500Pagu > 0 ? round(($bun500Real / $bun500Pagu) * 100, 2) : 0;
        $pct600 = $bun600Pagu > 0 ? round(($bun600Real / $bun600Pagu) * 100, 2) : 0;
        $pct700 = $bun700Pagu > 0 ? round(($bun700Real / $bun700Pagu) * 100, 2) : 0;

        $satkerBunClusters = [
            [
                'no' => 1,
                'kode' => '500xxx',
                'nama' => '7 SATKER PENYALUR DANA TRANSFER UMUM (DAU & DBH)',
                'sub' => 'BAGIAN ANGGARAN BENDAHARA UMUM NEGARA (7 KPPN ACEH)',
                'pagu' => $bun500Pagu,
                'realisasi' => $bun500Real,
                'sisa' => max(0, $bun500Pagu - $bun500Real),
                'pct' => $pct500,
                'status' => $pct500 >= 95 ? 'Optimal' : ($pct500 >= 90 ? 'On Track' : 'Perhatian'),
                'icon' => 'building-library',
                'keterangan' => 'Penyaluran Dana Alokasi Umum (DAU) dan Dana Bagi Hasil (DBH) di 7 KPPN Aceh.'
            ],
            [
                'no' => 2,
                'kode' => '600xxx',
                'nama' => '7 SATKER PENYALUR DANA TRANSFER KHUSUS (DAK)',
                'sub' => 'BAGIAN ANGGARAN BENDAHARA UMUM NEGARA (7 KPPN ACEH)',
                'pagu' => $bun600Pagu,
                'realisasi' => $bun600Real,
                'sisa' => max(0, $bun600Pagu - $bun600Real),
                'pct' => $pct600,
                'status' => $pct600 >= 95 ? 'Optimal' : ($pct600 >= 90 ? 'On Track' : 'Perhatian'),
                'icon' => 'briefcase',
                'keterangan' => 'Penyaluran DAK Fisik & DAK Nonfisik (BOS, BOK, TPG) di 7 KPPN Aceh.'
            ],
            [
                'no' => 3,
                'kode' => '700xxx',
                'nama' => '7 SATKER PENYALUR DANA DESA, INSENTIF FISKAL & OTSUS',
                'sub' => 'BAGIAN ANGGARAN BENDAHARA UMUM NEGARA (7 KPPN ACEH)',
                'pagu' => $bun700Pagu,
                'realisasi' => $bun700Real,
                'sisa' => max(0, $bun700Pagu - $bun700Real),
                'pct' => $pct700,
                'status' => $pct700 >= 95 ? 'Optimal' : ($pct700 >= 90 ? 'On Track' : 'Perhatian'),
                'icon' => 'sparkles',
                'keterangan' => 'Penyaluran Dana Desa, Otsus Aceh (DOKA), dan Insentif Fiskal di 7 KPPN Aceh.'
            ],
        ];

        $tkdSisaAll = max(0, $bunTotalPagu - $bunTotalReal);
        $this->saveSummary('tkd_bun_detail', [
            'summary' => [
                'total_pagu' => $bunTotalPagu,
                'total_realisasi' => $bunTotalReal,
                'total_sisa' => $tkdSisaAll,
                'persen_realisasi' => $bunPctAll,
                'persen_sisa' => round(100 - $bunPctAll, 2),
                'total_satker' => $bunRows->count(),
                'total_jenis_dbh' => count($dbhItems),
                'total_uraian_otsus' => count($otsusItems),
                'total_pagu_formatted' => 'Rp ' . number_format($bunTotalPagu, 0, ',', '.'),
                'total_realisasi_formatted' => 'Rp ' . number_format($bunTotalReal, 0, ',', '.'),
                'sisa_pagu_formatted' => 'Rp ' . number_format($tkdSisaAll, 0, ',', '.'),
                'total_pagu_short' => round($bunTotalPagu / 1e12, 2) . ' T',
                'total_realisasi_short' => round($bunTotalReal / 1e12, 2) . ' T',
                'sisa_pagu_short' => round($tkdSisaAll / 1e12, 2) . ' T',
                'cut_off' => date('d F Y'),
            ],
            'satker_bun' => $satkerBunClusters,
            'dbh_items' => $dbhItems,
            'otsus_items' => $otsusItems,
        ]);

        // 7. Non-BUN K/L Summary & Top 10 K/L
        $klPagu = (int) DB::table('sintesa_satker_pagu')->where('kementerian_kode', '!=', '999')->sum('pagu');
        $klBlokir = (int) DB::table('sintesa_satker_pagu')->where('kementerian_kode', '!=', '999')->sum('blokir');
        $klReal = (int) DB::table('sintesa_belanja_bulanan')->where('kementerian_kode', '!=', '999')->sum('total_realisasi');
        $klSatkerCount = DB::table('sintesa_satker_pagu')->where('kementerian_kode', '!=', '999')->distinct('satker_kode')->count('satker_kode');
        $klCount = DB::table('sintesa_satker_pagu')->where('kementerian_kode', '!=', '999')->distinct('kementerian_kode')->count('kementerian_kode');
        $klPct = $klPagu > 0 ? round(($klReal / $klPagu) * 100, 2) : 0;
        $klBlokirPct = $klPagu > 0 ? round(($klBlokir / $klPagu) * 100, 2) : 0;

        $satkerKlSummary = [
            'pagu' => $klPagu,
            'pagu_formatted' => 'Rp ' . number_format($klPagu, 0, ',', '.'),
            'realisasi' => $klReal,
            'realisasi_formatted' => 'Rp ' . number_format($klReal, 0, ',', '.'),
            'persen' => $klPct,
            'blokir' => $klBlokir,
            'blokir_formatted' => 'Rp ' . number_format($klBlokir, 0, ',', '.'),
            'persen_blokir' => $klBlokirPct,
            'satker_count' => $klSatkerCount,
            'kl_count' => $klCount,
        ];
        $this->saveSummary('satker_kl_summary', $satkerKlSummary);

        // Top 10 K/L
        $topKlPagu = DB::table('sintesa_satker_pagu')
            ->where('kementerian_kode', '!=', '999')
            ->select('kementerian_kode', 'kementerian_uraian', DB::raw('SUM(pagu) as total_pagu'))
            ->groupBy('kementerian_kode', 'kementerian_uraian')
            ->orderByDesc('total_pagu')
            ->limit(10)
            ->get();

        $topKlReal = DB::table('sintesa_belanja_bulanan')
            ->where('kementerian_kode', '!=', '999')
            ->select('kementerian_kode', DB::raw('SUM(total_realisasi) as total_realisasi'))
            ->groupBy('kementerian_kode')
            ->pluck('total_realisasi', 'kementerian_kode');

        $topKlLabels = [];
        $topKlPaguArr = [];
        $topKlRealArr = [];
        foreach ($topKlPagu as $k) {
            $r = $topKlReal[$k->kementerian_kode] ?? 0;
            $cleanName = ucwords(strtolower($k->kementerian_uraian));
            $cleanName = str_replace([
                'Kementerian Pekerjaan Umum',
                'Kementerian Perumahan Dan Kawasan Permukiman',
                'Kepolisian Negara Republik Indonesia',
                'Kementerian Pendidikan Tinggi, Sains, Dan Teknologi'
            ], [
                'Kementerian PU',
                'Kementerian PKP',
                'Polri',
                'Kemendiktisaintek'
            ], $cleanName);

            $topKlLabels[] = $cleanName;
            $topKlPaguArr[] = round($k->total_pagu / 1000000000000, 2);
            $topKlRealArr[] = round($r / 1000000000000, 2);
        }

        $this->saveSummary('top_10_kl', [
            'labels' => $topKlLabels,
            'pagu' => $topKlPaguArr,
            'realisasi' => $topKlRealArr,
        ]);

        // 8. Master Satker Seluruh Lingkup Aceh (789 Satker dengan detail Pagu, Blokir, Realisasi)
        $satkerPaguRows = DB::table('sintesa_satker_pagu')
            ->select(
                'satker_kode',
                'satker_uraian',
                'kementerian_kode',
                'kementerian_uraian',
                'kppn_kode',
                'kppn_uraian',
                DB::raw('SUM(pagu) as total_pagu'),
                DB::raw('SUM(blokir) as pagu_blokir')
            )
            ->groupBy('satker_kode', 'satker_uraian', 'kementerian_kode', 'kementerian_uraian', 'kppn_kode', 'kppn_uraian')
            ->orderByDesc('total_pagu')
            ->get();

        $satkerRealMap = DB::table('sintesa_belanja_bulanan')
            ->select('satker_kode', DB::raw('SUM(total_realisasi) as total_realisasi'))
            ->groupBy('satker_kode')
            ->pluck('total_realisasi', 'satker_kode');

        $satkerMaster = [];
        $no = 1;
        foreach ($satkerPaguRows as $p) {
            $real = (int) ($satkerRealMap[$p->satker_kode] ?? 0);
            $pagu = (int) $p->total_pagu;
            $pct = $pagu > 0 ? round(($real / $pagu) * 100, 2) : 0;

            $satkerMaster[] = [
                'no' => $no++,
                'kode_satker' => $p->satker_kode,
                'nama_satker' => $p->satker_uraian,
                'kode_kl' => $p->kementerian_kode,
                'nama_kl' => $p->kementerian_uraian,
                'kppn' => $p->kppn_uraian,
                'wilayah' => $p->kppn_uraian,
                'total_pagu' => $pagu,
                'total_realisasi' => $real,
                'pagu_blokir' => (int) $p->pagu_blokir,
                'persen' => $pct,
            ];
        }
        $this->saveSummary('satker_master', $satkerMaster);

        // 9. Tematik Prioritas Presiden fallback jika hanya memakai belwil_tematik lama
        if (!$this->inquiryPrioritasImported) {
            $prioRows = DB::table('sintesa_tematik_prioritas')->orderByDesc('total_realisasi')->get();
            $prioTotalReal = (int) $prioRows->sum('total_realisasi');
            $prioTotalSatker = $prioRows->pluck('satker_kode')->unique()->count();
            $prioTotalJenis = $prioRows->pluck('kdpriopres')->unique()->count();
            $porsiDariKlReal = $totalRealisasi > 0 ? round(($prioTotalReal / $totalRealisasi) * 100, 2) : 0;

            $this->saveSummary('tematik_prioritas_summary', [
                'has_pagu' => false,
                'total_pagu' => 0,
                'total_pagu_formatted' => 'N/A (CSV Hanya Realisasi)',
                'total_pagu_short' => 'N/A',
                'total_realisasi' => $prioTotalReal,
                'total_realisasi_formatted' => 'Rp ' . number_format($prioTotalReal, 0, ',', '.'),
                'total_realisasi_short' => round($prioTotalReal / 1000000000000, 2) . ' T',
                'sisa_pagu' => 0,
                'sisa_pagu_formatted' => '-',
                'sisa_pagu_short' => 'N/A',
                'persen_realisasi' => $porsiDariKlReal,
                'persen_sisa' => 0,
                'total_satker' => $prioTotalSatker,
                'total_jenis' => $prioTotalJenis,
                'total_ro' => $prioRows->count(),
            ]);

            $prioItems = [];
            $prioNo = 1;
            foreach ($prioRows as $pr) {
                $realPr = (int) $pr->total_realisasi;

                $prioItems[] = [
                    'no' => $prioNo++,
                    'kode_satker' => $pr->satker_kode,
                    'nama_satker' => $pr->satker_uraian,
                    'kl' => $pr->kementerian_uraian,
                    'wilayah' => 'Aceh',
                    'kode_jenis' => $pr->kdpriopres,
                    'jenis' => $pr->nmpriopres,
                    'kegiatan' => $pr->kegiatan_uraian ?: 'Kegiatan Prioritas ' . $pr->nmpriopres,
                    'program' => $pr->program_uraian ?: 'Program Prioritas Nasional T.A. 2026',
                    'pagu' => 0,
                    'realisasi' => $realPr,
                    'pct' => 0,
                ];
            }
            $this->saveSummary('tematik_prioritas_items', $prioItems);
        }

        return [
            'total_pagu' => $totalPagu,
            'total_realisasi' => $totalRealisasi,
            'persen_realisasi' => $persenRealisasi,
            'kppn_count' => count($kppnRanking),
            'satker_count' => $kpiData['total_satker_count'],
        ];
    }

    /**
     * Ambil data summary berkecepatan tinggi
     */
    public function getSummary(string $key, $default = null)
    {
        $row = DB::table('sintesa_summary')->where('key', $key)->first();
        if ($row) {
            return json_decode($row->payload, true);
        }
        return $default;
    }

    private function saveSummary(string $key, $data): void
    {
        DB::table('sintesa_summary')->updateOrInsert(
            ['key' => $key],
            ['payload' => json_encode($data), 'updated_at' => now()]
        );
    }

    private function logSync(string $filename, string $fileType, int $totalRows): void
    {
        DB::table('sintesa_sync_logs')->insert([
            'filename' => $filename,
            'file_type' => $fileType,
            'total_rows' => $totalRows,
            'status' => 'SUCCESS',
            'message' => "Berhasil mengimpor {$totalRows} baris data.",
            'synced_by' => 'Syahrial (Kanwil DJPb)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function findLatestFile(string $folder, string $pattern): ?string
    {
        $files = glob("{$folder}/{$pattern}");
        if (empty($files)) return null;
        usort($files, fn($a, $b) => filemtime($b) <=> filemtime($a));
        return $files[0];
    }
}
