<?php

namespace App\Services;

class SupabaseService
{
    private $url;
    private $anonKey;
    private $serviceKey;
    private $isConfigured = false;

    public function __construct()
    {
        $this->url = rtrim(config('supabase.supabase_url', ''), '/');
        $this->anonKey = config('supabase.supabase_anon_key', '');
        $this->serviceKey = config('supabase.supabase_service_key', '');

        if (!empty($this->url) && !empty($this->anonKey) && str_starts_with($this->url, 'http') && !str_contains($this->url, 'your-project-ref')) {
            $this->isConfigured = true;
        }
    }

    public function isConnected(): bool
    {
        return $this->isConfigured;
    }

    public function get($table, $queryParams = '')
    {
        if (!$this->isConfigured) {
            return $this->getMockData($table, $queryParams);
        }

        $endpoint = "{$this->url}/rest/v1/{$table}" . ($queryParams ? "?{$queryParams}" : "");
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_HTTPHEADER => [
                "apikey: {$this->anonKey}",
                "Authorization: Bearer {$this->anonKey}",
                "Content-Type: application/json",
                "Prefer: return=representation"
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300 && !empty($response)) {
            $data = json_decode($response, true);
            if (is_array($data) && !empty($data)) {
                return $data;
            }
        }

        return $this->getMockData($table, $queryParams);
    }

    public function getMockData($table, $queryParams = '')
    {
        return match ($table) {
            'satker', 'realisasi_anggaran' => $this->getSatkerMaster(),
            'prioritas_presiden' => $this->getPrioritasPresidenData(),
            'program_strategis' => $this->getProgramStrategisData(),
            'penanganan_bencana' => $this->getBencanaData(),
            'preservasi_jalan' => $this->getJalanData(),
            'bantuan_sosial' => $this->getBansosData(),
            'tkd_alokasi' => $this->getTKDData(),
            default => [],
        };
    }

    public function getExecutiveKpi(): array
    {
        $realKpi = app(\App\Services\SintesaImportService::class)->getSummary('executive_kpi');
        if (!empty($realKpi) && config('app.sintesa_real_data', true)) {
            return $realKpi;
        }

        return [
            'total_pagu' => 40243510054000,
            'total_pagu_formatted' => 'Rp 40.243.510.054.000',
            'total_realisasi' => 28778105929989,
            'total_realisasi_formatted' => 'Rp 28.778.105.929.989',
            'persen_realisasi' => 71.51,
            'total_blokir' => 118873405000,
            'total_blokir_formatted' => 'Rp 118.873.405.000',
            'persen_blokir' => 0.29,
            'sisa_pagu' => 11465404124011,
            'sisa_pagu_formatted' => 'Rp 11.465.404.124.011',
            'persen_sisa' => 28.49,
            'realisasi_sp2d_cutoff_formatted' => 'Rp 28.520.140.000.000',
            'cutoff_time' => '17 November 2026 pukul 09.12 WIB',
        ];
    }

    /**
     * Data Hero Banner Baru Versi Mentor (Total Regional Kanwil DJPb Aceh)
     */
    public function getKanwilAcehHero(): array
    {
        $realKpi = app(\App\Services\SintesaImportService::class)->getSummary('executive_kpi');
        if (!empty($realKpi) && config('app.sintesa_real_data', true)) {
            return [
                'total_pagu' => $realKpi['total_pagu'],
                'total_pagu_formatted' => $realKpi['total_pagu_formatted'],
                'realisasi_belanja' => $realKpi['total_realisasi'],
                'realisasi_belanja_formatted' => $realKpi['total_realisasi_formatted'],
                'sisa_pagu' => $realKpi['sisa_pagu'],
                'sisa_pagu_formatted' => $realKpi['sisa_pagu_formatted'],
                'persen_realisasi' => $realKpi['persen_realisasi'],
                'persen_sisa' => $realKpi['persen_sisa'],
                'realisasi_sp2d_cutoff' => $realKpi['total_realisasi'],
                'realisasi_sp2d_cutoff_formatted' => $realKpi['realisasi_sp2d_cutoff_formatted'],
                'cutoff_date' => $realKpi['cutoff_date'],
                'cutoff_time' => $realKpi['cutoff_time'],
            ];
        }

        return [
            'total_pagu' => 63845726882000,
            'total_pagu_formatted' => 'Rp 63.845.726.882.000',
            'realisasi_belanja' => 50590927757073,
            'realisasi_belanja_formatted' => 'Rp 50.590.927.757.073',
            'sisa_pagu' => 13254799124927,
            'sisa_pagu_formatted' => 'Rp 13.254.799.124.927',
            'persen_realisasi' => 79,
            'persen_sisa' => 21,
            'realisasi_sp2d_cutoff' => 50089642811893,
            'realisasi_sp2d_cutoff_formatted' => 'Rp 50.089.642.811.893',
            'cutoff_date' => '17 November 2026',
            'cutoff_time' => '17 November 2026 pukul 09.12 WIB',
        ];
    }

    /**
     * Kinerja 8 KPPN Lingkup Kanwil DJPb Provinsi Aceh
     */
    public function getKppnRegionalRanking(): array
    {
        $realKppn = app(\App\Services\SintesaImportService::class)->getSummary('kppn_ranking');
        if (!empty($realKppn) && config('app.sintesa_real_data', true)) {
            $labels = [];
            $persen = [];
            $pagu = [];
            $real = [];
            foreach ($realKppn as $k) {
                $labels[] = 'KPPN ' . ucwords(strtolower($k['nama']));
                $persen[] = $k['persentase'];
                $pagu[] = round($k['pagu'] / 1000000000000, 2);
                $real[] = round($k['realisasi'] / 1000000000000, 2);
            }
            return [
                'labels' => $labels,
                'persen' => $persen,
                'pagu' => $pagu,
                'realisasi' => $real,
                'details' => $realKppn
            ];
        }

        return [
            'labels' => [
                'KPPN Sabang',
                'KPPN Langsa',
                'KPPN Lhokseumawe',
                'KPPN Tapaktuan',
                'KPPN Meulaboh',
                'KPPN Takengon',
                'KPPN Kutacane',
                'KPPN Banda Aceh'
            ],
            'persen' => [84.50, 81.20, 78.40, 77.80, 76.15, 75.90, 74.30, 71.51],
            'pagu' => [1.45, 3.95, 8.45, 3.28, 4.12, 2.15, 1.65, 40.24],
            'realisasi' => [1.23, 3.21, 6.62, 2.55, 3.14, 1.63, 1.23, 28.78],
        ];
    }

    public function getSatkerKlSummary(): array
    {
        $realSummary = app(\App\Services\SintesaImportService::class)->getSummary('satker_kl_summary');
        if (!empty($realSummary) && config('app.sintesa_real_data', true)) {
            return $realSummary;
        }

        return [
            'pagu' => 27311589836000,
            'pagu_formatted' => 'Rp 27.311.589.836.000',
            'realisasi' => 16541068168452,
            'realisasi_formatted' => 'Rp 16.541.068.168.452',
            'persen' => 60.56,
            'blokir' => 118873405000,
            'blokir_formatted' => 'Rp 118.873.405.000',
            'persen_blokir' => 0.43,
            'satker_count' => 308,
            'kl_count' => 51
        ];
    }

    public function getTop10Kl(): array
    {
        $realTop = app(\App\Services\SintesaImportService::class)->getSummary('top_10_kl');
        if (!empty($realTop) && config('app.sintesa_real_data', true)) {
            return $realTop;
        }

        return [
            'labels' => [
                'Kementerian Pekerjaan Umum (BPJN/BWS)',
                'Kementerian Agama',
                'Kepolisian Negara Republik Indonesia',
                'Kementerian Pertahanan',
                'Kemendikbudristek',
                'Kementerian Perhubungan',
                'Kementerian Hukum dan HAM',
                'Kementerian Keuangan',
                'Komisi Pemilihan Umum (KPU)',
                'Mahkamah Agung'
            ],
            'pagu' => [2.12, 1.85, 1.36, 1.19, 0.86, 0.42, 0.31, 0.28, 0.21, 0.18],
            'realisasi' => [1.68, 1.65, 1.26, 1.12, 0.78, 0.35, 0.28, 0.26, 0.19, 0.16]
        ];
    }

    public function getBunRegionalData(): array
    {
        $realBun = app(\App\Services\SintesaImportService::class)->getSummary('bun_regional_data');
        if (!empty($realBun) && config('app.sintesa_real_data', true)) {
            return $realBun;
        }

        return [
            'ALL' => [
                'labels' => [
                    ['500133', 'DANA DESA & OTSUS'],
                    ['700254', 'TRANSFER UMUM (DAU & DBH)'],
                    ['600061', 'DAK FISIK & NONFISIK']
                ],
                'fullLabels' => [
                    '500133 PENYALUR DANA DESA, INSENTIF DESA, DAN OTSUS ACEH - KANWIL DJPB PROVINSI ACEH',
                    '700254 PENYALUR TRANSFER UMUM (DAU & DBH) - KANWIL DJPB PROVINSI ACEH',
                    '600061 PENYALUR DAK FISIK & NONFISIK - KANWIL DJPB PROVINSI ACEH'
                ],
                'pagu' => [6550.2, 4520.5, 1861.2],
                'realisasi' => [6125.8, 4460.2, 1651.0],
                'cardPagu' => 'Rp 12.931.920.218.000',
                'subPagu' => 'Alokasi 3 Satker Penyalur BUN (Kanwil DJPb Provinsi Aceh)',
                'cardRealisasi' => 'Rp 12.237.037.761.537',
                'subRealisasi' => '94,63% Capaian Penyaluran BUN',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Satker Penyalur Transfer Ke Daerah (TKD) · Kanwil DJPb Provinsi Aceh'
            ],
            'satker_500133' => [
                'labels' => [
                    ['500133', 'DANA DESA & OTSUS ACEH']
                ],
                'fullLabels' => [
                    '500133 PENYALUR DANA DESA, INSENTIF DESA, DAN OTSUS ACEH - KANWIL DJPB PROVINSI ACEH'
                ],
                'pagu' => [6550.2],
                'realisasi' => [6125.8],
                'cardPagu' => 'Rp 6.550.200.000.000',
                'subPagu' => 'Alokasi Satker 500133 (Dana Desa & Otsus)',
                'cardRealisasi' => 'Rp 6.125.800.000.000',
                'subRealisasi' => '93,52% Capaian Penyaluran Satker 500133',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Satker: 500133 Penyalur Dana Desa, Otsus & Insentif Desa · Kanwil DJPb Provinsi Aceh'
            ],
            'satker_700254' => [
                'labels' => [
                    ['700254', 'TRANSFER UMUM (DAU & DBH)']
                ],
                'fullLabels' => [
                    '700254 PENYALUR TRANSFER UMUM (DAU & DBH) - KANWIL DJPB PROVINSI ACEH'
                ],
                'pagu' => [4520.5],
                'realisasi' => [4460.2],
                'cardPagu' => 'Rp 4.520.500.000.000',
                'subPagu' => 'Alokasi Satker 700254 (Transfer Umum)',
                'cardRealisasi' => 'Rp 4.460.200.000.000',
                'subRealisasi' => '98,67% Capaian Penyaluran Satker 700254',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Satker: 700254 Penyalur Transfer Umum (DAU & DBH) · Kanwil DJPb Provinsi Aceh'
            ],
            'satker_600061' => [
                'labels' => [
                    ['600061', 'DAK FISIK & NONFISIK']
                ],
                'fullLabels' => [
                    '600061 PENYALUR DAK FISIK & NONFISIK - KANWIL DJPB PROVINSI ACEH'
                ],
                'pagu' => [1861.2],
                'realisasi' => [1651.0],
                'cardPagu' => 'Rp 1.861.200.000.000',
                'subPagu' => 'Alokasi Satker 600061 (DAK Fisik & Nonfisik)',
                'cardRealisasi' => 'Rp 1.651.000.000.000',
                'subRealisasi' => '88,71% Capaian Penyaluran Satker 600061',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Satker: 600061 Penyalur DAK Fisik & Nonfisik · Kanwil DJPb Provinsi Aceh'
            ],
            'prov_aceh' => [
                'labels' => [
                    ['500133', 'DANA OTSUS PROVINSI'],
                    ['700254', 'DBH & DAU PROVINSI'],
                    ['600061', 'DAK PROVINSI']
                ],
                'fullLabels' => [
                    '500133 - Dana Otonomi Khusus Aceh Porsi Provinsi',
                    '700254 - Dana Bagi Hasil (DBH Migas & Pajak) serta DAU Pemprov',
                    '600061 - Dana Alokasi Khusus (DAK Fisik & Nonfisik) Pemprov'
                ],
                'pagu' => [2484.0, 1620.0, 650.0],
                'realisasi' => [2400.0, 1605.0, 595.0],
                'cardPagu' => 'Rp 4.754.000.000.000',
                'subPagu' => 'Penyaluran ke Pemerintah Provinsi Aceh',
                'cardRealisasi' => 'Rp 4.600.000.000.000',
                'subRealisasi' => '96,76% Capaian Penyaluran Provinsi',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Wilayah: Pemerintah Provinsi Aceh · Kanwil DJPb Provinsi Aceh'
            ],
            'banda_aceh' => [
                'labels' => [
                    ['500133', 'DANA DESA (90 GAMPONG)'],
                    ['700254', 'DAU & DBH KOTA B.ACEH'],
                    ['600061', 'DAK KOTA B.ACEH']
                ],
                'fullLabels' => [
                    '500133 - Dana Desa 90 Gampong & Insentif Fiskal Kota Banda Aceh',
                    '700254 - DAU & DBH Pemko Banda Aceh',
                    '600061 - DAK Fisik/Nonfisik (Pendidikan & Kesehatan) Kota Banda Aceh'
                ],
                'pagu' => [385.2, 542.0, 182.5],
                'realisasi' => [362.5, 535.0, 168.2],
                'cardPagu' => 'Rp 1.109.700.000.000',
                'subPagu' => 'Penyaluran ke Kota Banda Aceh (90 Gampong)',
                'cardRealisasi' => 'Rp 1.065.700.000.000',
                'subRealisasi' => '96,03% Capaian Penyaluran Kota',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Wilayah: Kota Banda Aceh · Kanwil DJPb Provinsi Aceh'
            ],
            'aceh_besar' => [
                'labels' => [
                    ['500133', 'DANA DESA (604 GAMPONG)'],
                    ['700254', 'DAU & DBH ACEH BESAR'],
                    ['600061', 'DAK ACEH BESAR']
                ],
                'fullLabels' => [
                    '500133 - Dana Desa 604 Gampong & Insentif Aceh Besar',
                    '700254 - DAU & DBH Kabupaten Aceh Besar',
                    '600061 - DAK Fisik & Nonfisik Kabupaten Aceh Besar'
                ],
                'pagu' => [1285.0, 845.0, 385.0],
                'realisasi' => [1198.0, 832.0, 335.0],
                'cardPagu' => 'Rp 2.515.000.000.000',
                'subPagu' => 'Penyaluran ke Kab. Aceh Besar (604 Gampong)',
                'cardRealisasi' => 'Rp 2.365.000.000.000',
                'subRealisasi' => '94,04% Capaian Penyaluran Kab. Aceh Besar',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Wilayah: Kabupaten Aceh Besar · Kanwil DJPb Provinsi Aceh'
            ],
            'pidie' => [
                'labels' => [
                    ['500133', 'DANA DESA (730 GAMPONG)'],
                    ['700254', 'DAU & DBH PIDIE'],
                    ['600061', 'DAK PIDIE']
                ],
                'fullLabels' => [
                    '500133 - Dana Desa 730 Gampong & Otsus Kab. Pidie',
                    '700254 - DAU & DBH Kabupaten Pidie',
                    '600061 - DAK Fisik & Nonfisik Kabupaten Pidie'
                ],
                'pagu' => [1342.0, 895.0, 395.0],
                'realisasi' => [1245.0, 882.0, 342.0],
                'cardPagu' => 'Rp 2.632.000.000.000',
                'subPagu' => 'Penyaluran ke Kab. Pidie (730 Gampong)',
                'cardRealisasi' => 'Rp 2.469.000.000.000',
                'subRealisasi' => '93,81% Capaian Penyaluran Kab. Pidie',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Wilayah: Kabupaten Pidie · Kanwil DJPb Provinsi Aceh'
            ],
            'pidie_jaya' => [
                'labels' => [
                    ['500133', 'DANA DESA (222 GAMPONG)'],
                    ['700254', 'DAU & DBH PIDIE JAYA'],
                    ['600061', 'DAK PIDIE JAYA']
                ],
                'fullLabels' => [
                    '500133 - Dana Desa 222 Gampong & Otsus Pidie Jaya',
                    '700254 - DAU & DBH Kabupaten Pidie Jaya',
                    '600061 - DAK Fisik & Nonfisik Kabupaten Pidie Jaya'
                ],
                'pagu' => [645.0, 412.0, 145.0],
                'realisasi' => [598.3, 405.0, 124.5],
                'cardPagu' => 'Rp 1.202.000.000.000',
                'subPagu' => 'Penyaluran ke Kab. Pidie Jaya (222 Gampong)',
                'cardRealisasi' => 'Rp 1.127.800.000.000',
                'subRealisasi' => '93,83% Capaian Penyaluran Kab. Pidie Jaya',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Wilayah: Kabupaten Pidie Jaya · Kanwil DJPb Provinsi Aceh'
            ],
            'sabang' => [
                'labels' => [
                    ['500133', 'DANA DESA (18 GAMPONG)'],
                    ['700254', 'DAU & DBH SABANG'],
                    ['600061', 'DAK KOTA SABANG']
                ],
                'fullLabels' => [
                    '500133 - Dana Desa 18 Gampong & Otsus Kota Sabang',
                    '700254 - DAU & DBH Pemko Sabang',
                    '600061 - DAK Fisik & Nonfisik Kota Sabang'
                ],
                'pagu' => [409.0, 206.5, 103.72],
                'realisasi' => [322.0, 201.2, 86.338],
                'cardPagu' => 'Rp 719.220.218.000',
                'subPagu' => 'Penyaluran ke Kota Sabang (18 Gampong)',
                'cardRealisasi' => 'Rp 609.537.761.537',
                'subRealisasi' => '84,75% Capaian Penyaluran Kota Sabang',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Wilayah: Kota Sabang · Kanwil DJPb Provinsi Aceh'
            ],
            'kppn_001' => [
                'labels' => [
                    ['500133', 'DANA DESA & OTSUS'],
                    ['700254', 'TRANSFER UMUM (DAU & DBH)'],
                    ['600061', 'DAK FISIK & NONFISIK']
                ],
                'fullLabels' => [
                    '500133 PENYALUR DANA DESA, INSENTIF DESA, DAN OTSUS ACEH - KANWIL DJPB PROVINSI ACEH',
                    '700254 PENYALUR TRANSFER UMUM (DAU & DBH) - KANWIL DJPB PROVINSI ACEH',
                    '600061 PENYALUR DAK FISIK & NONFISIK - KANWIL DJPB PROVINSI ACEH'
                ],
                'pagu' => [6550.2, 4520.5, 1861.2],
                'realisasi' => [6125.8, 4460.2, 1651.0],
                'cardPagu' => 'Rp 12.931.920.218.000',
                'subPagu' => 'Alokasi 3 Satker Penyalur BUN (Kanwil DJPb Provinsi Aceh)',
                'cardRealisasi' => 'Rp 12.237.037.761.537',
                'subRealisasi' => '94,63% Capaian Penyaluran BUN',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Satker Penyalur Transfer Ke Daerah (TKD) · Kanwil DJPb Provinsi Aceh'
            ],
            'kppn_meulaboh' => [
                'labels' => [['DANA DESA', 'MEULABOH'], ['DAU & DBH', 'MEULABOH'], ['DAK FISIK/NON', 'MEULABOH']],
                'fullLabels' => ['Penyaluran Dana Desa & Otsus Meulaboh', 'Penyaluran Transfer Umum (DAU & DBH) Meulaboh', 'Penyaluran DAK Fisik/Nonfisik Meulaboh'],
                'pagu' => [1650.0, 1250.0, 520.0],
                'realisasi' => [1540.0, 1215.0, 465.0],
                'cardPagu' => 'Rp 3.420.000.000.000',
                'subPagu' => 'Lingkup KPPN Meulaboh (Aceh Barat, Nagan Raya, Aceh Jaya)',
                'cardRealisasi' => 'Rp 3.220.000.000.000',
                'subRealisasi' => '94,15% Capaian KPPN Meulaboh',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Wilayah Pembayaran KPPN Meulaboh · Kanwil DJPb Aceh'
            ],
            'kppn_lhokseumawe' => [
                'labels' => [['DANA DESA', 'LHOKSEUMAWE'], ['DAU & DBH', 'LHOKSEUMAWE'], ['DAK FISIK/NON', 'LHOKSEUMAWE']],
                'fullLabels' => ['Penyaluran Dana Desa & Otsus Lhokseumawe', 'Penyaluran Transfer Umum Lhokseumawe', 'Penyaluran DAK Fisik/Nonfisik Lhokseumawe'],
                'pagu' => [2850.0, 2180.0, 1120.0],
                'realisasi' => [2680.0, 2120.0, 995.0],
                'cardPagu' => 'Rp 6.150.000.000.000',
                'subPagu' => 'Lingkup KPPN Lhokseumawe (Lhokseumawe, Aceh Utara, Bireuen)',
                'cardRealisasi' => 'Rp 5.795.000.000.000',
                'subRealisasi' => '94,23% Capaian KPPN Lhokseumawe',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Wilayah Pembayaran KPPN Lhokseumawe · Kanwil DJPb Aceh'
            ],
            'kppn_langsa' => [
                'labels' => [['DANA DESA', 'LANGSA'], ['DAU & DBH', 'LANGSA'], ['DAK FISIK/NON', 'LANGSA']],
                'fullLabels' => ['Penyaluran Dana Desa & Otsus Langsa', 'Penyaluran Transfer Umum Langsa', 'Penyaluran DAK Fisik/Nonfisik Langsa'],
                'pagu' => [1780.0, 1420.0, 650.0],
                'realisasi' => [1690.0, 1380.0, 580.0],
                'cardPagu' => 'Rp 3.850.000.000.000',
                'subPagu' => 'Lingkup KPPN Langsa (Kota Langsa, Aceh Timur, Aceh Tamiang)',
                'cardRealisasi' => 'Rp 3.650.000.000.000',
                'subRealisasi' => '94,81% Capaian KPPN Langsa',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Wilayah Pembayaran KPPN Langsa · Kanwil DJPb Aceh'
            ],
            'kppn_tapaktuan' => [
                'labels' => [['DANA DESA', 'TAPAKTUAN'], ['DAU & DBH', 'TAPAKTUAN'], ['DAK FISIK/NON', 'TAPAKTUAN']],
                'fullLabels' => ['Penyaluran Dana Desa & Otsus Tapaktuan', 'Penyaluran Transfer Umum Tapaktuan', 'Penyaluran DAK Fisik/Nonfisik Tapaktuan'],
                'pagu' => [1950.0, 1480.0, 690.0],
                'realisasi' => [1820.0, 1435.0, 615.0],
                'cardPagu' => 'Rp 4.120.000.000.000',
                'subPagu' => 'Lingkup KPPN Tapaktuan (Aceh Selatan, Abdya, Singkil, Subulussalam)',
                'cardRealisasi' => 'Rp 3.870.000.000.000',
                'subRealisasi' => '93,93% Capaian KPPN Tapaktuan',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Wilayah Pembayaran KPPN Tapaktuan · Kanwil DJPb Aceh'
            ],
            'kppn_takengon' => [
                'labels' => [['DANA DESA', 'TAKENGON'], ['DAU & DBH', 'TAKENGON'], ['DAK FISIK/NON', 'TAKENGON']],
                'fullLabels' => ['Penyaluran Dana Desa & Otsus Takengon', 'Penyaluran Transfer Umum Takengon', 'Penyaluran DAK Fisik/Nonfisik Takengon'],
                'pagu' => [1320.0, 990.0, 470.0],
                'realisasi' => [1240.0, 965.0, 415.0],
                'cardPagu' => 'Rp 2.780.000.000.000',
                'subPagu' => 'Lingkup KPPN Takengon (Aceh Tengah & Bener Meriah)',
                'cardRealisasi' => 'Rp 2.620.000.000.000',
                'subRealisasi' => '94,24% Capaian KPPN Takengon',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Wilayah Pembayaran KPPN Takengon · Kanwil DJPb Aceh'
            ],
            'kppn_kutacane' => [
                'labels' => [['DANA DESA', 'KUTACANE'], ['DAU & DBH', 'KUTACANE'], ['DAK FISIK/NON', 'KUTACANE']],
                'fullLabels' => ['Penyaluran Dana Desa & Otsus Kutacane', 'Penyaluran Transfer Umum Kutacane', 'Penyaluran DAK Fisik/Nonfisik Kutacane'],
                'pagu' => [1050.0, 810.0, 380.0],
                'realisasi' => [980.0, 785.0, 335.0],
                'cardPagu' => 'Rp 2.240.000.000.000',
                'subPagu' => 'Lingkup KPPN Kutacane (Aceh Tenggara & Gayo Lues)',
                'cardRealisasi' => 'Rp 2.100.000.000.000',
                'subRealisasi' => '93,75% Capaian KPPN Kutacane',
                'cardBlokir' => 'Rp 0',
                'subBlokir' => 'Nihil / Tidak Ada Blokir',
                'footer' => 'Wilayah Pembayaran KPPN Kutacane · Kanwil DJPb Aceh'
            ]
        ];
    }

    public function getSatkerMaster(): array
    {
        $realSatkers = app(\App\Services\SintesaImportService::class)->getSummary('satker_master');
        if (!empty($realSatkers) && config('app.sintesa_real_data', true)) {
            return $realSatkers;
        }

        return [
            [
                'kode_satker' => '415301',
                'nama_satker' => 'KANWIL DJPB PROVINSI ACEH',
                'kode_kl' => '015',
                'nama_kl' => 'KEMENTERIAN KEUANGAN',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 15200000000, 'realisasi_51' => 13950000000,
                'pagu_52' => 8450000000,  'realisasi_52' => 7420000000,
                'pagu_53' => 1250000000,  'realisasi_53' => 1080000000,
                'pagu_57' => 0,           'realisasi_57' => 0,
                'total_pagu' => 24900000000, 'total_realisasi' => 22450000000,
                'persen' => 90.16, 'pagu_blokir' => 120000000
            ],
            [
                'kode_satker' => '415302',
                'nama_satker' => 'KPPN BANDA ACEH (001)',
                'kode_kl' => '015',
                'nama_kl' => 'KEMENTERIAN KEUANGAN',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 8600000000,  'realisasi_51' => 8150000000,
                'pagu_52' => 4350000000,  'realisasi_52' => 3980000000,
                'pagu_53' => 850000000,   'realisasi_53' => 790000000,
                'pagu_57' => 0,           'realisasi_57' => 0,
                'total_pagu' => 13800000000, 'total_realisasi' => 12920000000,
                'persen' => 93.62, 'pagu_blokir' => 45000000
            ],
            [
                'kode_satker' => '651234',
                'nama_satker' => 'BALAI PELAKSANAAN JALAN NASIONAL ACEH',
                'kode_kl' => '033',
                'nama_kl' => 'KEMENTERIAN PEKERJAAN UMUM',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 28400000000, 'realisasi_51' => 24800000000,
                'pagu_52' => 89500000000, 'realisasi_52' => 74200000000,
                'pagu_53' => 1520000000000, 'realisasi_53' => 1185000000000,
                'pagu_57' => 0, 'realisasi_57' => 0,
                'total_pagu' => 1637900000000, 'total_realisasi' => 1284000000000,
                'persen' => 78.39, 'pagu_blokir' => 24500000000
            ],
            [
                'kode_satker' => '651235',
                'nama_satker' => 'BALAI WILAYAH SUNGAI SUMATERA I',
                'kode_kl' => '033',
                'nama_kl' => 'KEMENTERIAN PEKERJAAN UMUM',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 19200000000, 'realisasi_51' => 17400000000,
                'pagu_52' => 48600000000, 'realisasi_52' => 41800000000,
                'pagu_53' => 462000000000, 'realisasi_53' => 395000000000,
                'pagu_57' => 0, 'realisasi_57' => 0,
                'total_pagu' => 529800000000, 'total_realisasi' => 454200000000,
                'persen' => 85.73, 'pagu_blokir' => 11200000000
            ],
            [
                'kode_satker' => '423110',
                'nama_satker' => 'UNIVERSITAS ISLAM NEGERI AR-RANIRY',
                'kode_kl' => '025',
                'nama_kl' => 'KEMENTERIAN AGAMA',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 128500000000, 'realisasi_51' => 118400000000,
                'pagu_52' => 88200000000,  'realisasi_52' => 76500000000,
                'pagu_53' => 46800000000,  'realisasi_53' => 41200000000,
                'pagu_57' => 0,            'realisasi_57' => 0,
                'total_pagu' => 263500000000, 'total_realisasi' => 236100000000,
                'persen' => 89.60, 'pagu_blokir' => 4800000000
            ],
            [
                'kode_satker' => '423111',
                'nama_satker' => 'UNIVERSITAS SYIAH KUALA',
                'kode_kl' => '023',
                'nama_kl' => 'KEMENDIKBUDRISTEK',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 285000000000, 'realisasi_51' => 264000000000,
                'pagu_52' => 198000000000, 'realisasi_52' => 174500000000,
                'pagu_53' => 86500000000,  'realisasi_53' => 79800000000,
                'pagu_57' => 0,            'realisasi_57' => 0,
                'total_pagu' => 569500000000, 'total_realisasi' => 518300000000,
                'persen' => 91.01, 'pagu_blokir' => 7800000000
            ],
            [
                'kode_satker' => '654321',
                'nama_satker' => 'KEPOLISIAN DAERAH ACEH',
                'kode_kl' => '060',
                'nama_kl' => 'KEPOLISIAN NEGARA REPUBLIK INDONESIA',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 985000000000, 'realisasi_51' => 932000000000,
                'pagu_52' => 315000000000, 'realisasi_52' => 284000000000,
                'pagu_53' => 78000000000,  'realisasi_53' => 71500000000,
                'pagu_57' => 0,            'realisasi_57' => 0,
                'total_pagu' => 1378000000000, 'total_realisasi' => 1287500000000,
                'persen' => 93.43, 'pagu_blokir' => 11500000000
            ],
            [
                'kode_satker' => '654322',
                'nama_satker' => 'KOMANDO DAERAH MILITER ISKANDAR MUDA',
                'kode_kl' => '012',
                'nama_kl' => 'KEMENTERIAN PERTAHANAN',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 860000000000, 'realisasi_51' => 818000000000,
                'pagu_52' => 285000000000, 'realisasi_52' => 258000000000,
                'pagu_53' => 62000000000,  'realisasi_53' => 56500000000,
                'pagu_57' => 0,            'realisasi_57' => 0,
                'total_pagu' => 1207000000000, 'total_realisasi' => 1132500000000,
                'persen' => 93.83, 'pagu_blokir' => 8600000000
            ],
            [
                'kode_satker' => '425001',
                'nama_satker' => 'SENTRA DARUSSAADAH ACEH BESAR',
                'kode_kl' => '027',
                'nama_kl' => 'KEMENTERIAN SOSIAL',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 3650000000, 'realisasi_51' => 3350000000,
                'pagu_52' => 2680000000, 'realisasi_52' => 2390000000,
                'pagu_53' => 520000000,  'realisasi_53' => 475000000,
                'pagu_57' => 6240000000, 'realisasi_57' => 5770000000,
                'total_pagu' => 13090000000, 'total_realisasi' => 11985000000,
                'persen' => 91.56, 'pagu_blokir' => 0
            ],
            [
                'kode_satker' => '415303',
                'nama_satker' => 'KANTOR WILAYAH DJBC ACEH',
                'kode_kl' => '015',
                'nama_kl' => 'KEMENTERIAN KEUANGAN',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 32800000000,  'realisasi_51' => 30600000000,
                'pagu_52' => 14800000000,  'realisasi_52' => 13450000000,
                'pagu_53' => 4600000000,   'realisasi_53' => 4250000000,
                'pagu_57' => 0,            'realisasi_57' => 0,
                'total_pagu' => 52200000000, 'total_realisasi' => 48300000000,
                'persen' => 92.53, 'pagu_blokir' => 380000000
            ],
            [
                'kode_satker' => '415304',
                'nama_satker' => 'KANTOR WILAYAH DJP ACEH',
                'kode_kl' => '015',
                'nama_kl' => 'KEMENTERIAN KEUANGAN',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 56400000000,  'realisasi_51' => 52800000000,
                'pagu_52' => 24600000000,  'realisasi_52' => 22400000000,
                'pagu_53' => 6400000000,   'realisasi_53' => 5950000000,
                'pagu_57' => 0,            'realisasi_57' => 0,
                'total_pagu' => 87400000000, 'total_realisasi' => 81150000000,
                'persen' => 92.85, 'pagu_blokir' => 610000000
            ],
            [
                'kode_satker' => '415305',
                'nama_satker' => 'KANTOR WILAYAH DJKN ACEH',
                'kode_kl' => '015',
                'nama_kl' => 'KEMENTERIAN KEUANGAN',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 22600000000,  'realisasi_51' => 20950000000,
                'pagu_52' => 9950000000,   'realisasi_52' => 9120000000,
                'pagu_53' => 2180000000,   'realisasi_53' => 2010000000,
                'pagu_57' => 0,            'realisasi_57' => 0,
                'total_pagu' => 34730000000, 'total_realisasi' => 32080000000,
                'persen' => 92.37, 'pagu_blokir' => 220000000
            ],
            // Data Satker Resmi KPPN 001 Banda Aceh (Persis Gambar 2)
            [
                'no' => 306,
                'kode_satker' => '019700',
                'nama_satker' => 'BADAN PUSAT STATISTIK KOTA SABANG',
                'kode_kl' => '054',
                'nama_kl' => 'BADAN PUSAT STATISTIK • KOTA SABANG',
                'wilayah' => 'KOTA SABANG',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 2850000000, 'realisasi_51' => 2520000000,
                'pagu_52' => 1952090000, 'realisasi_52' => 1623824677,
                'pagu_53' => 0,          'realisasi_53' => 0,
                'pagu_57' => 0,          'realisasi_57' => 0,
                'total_pagu' => 4802090000,
                'total_realisasi' => 4143824677,
                'persen' => 86.29,
                'pagu_blokir' => 83063000
            ],
            [
                'no' => 307,
                'kode_satker' => '061820',
                'nama_satker' => 'KANTOR PERTANAHAN KABUPATEN PIDIE JAYA',
                'kode_kl' => '056',
                'nama_kl' => 'KEMENTERIAN AGRARIA DAN TATA RUANG/BPN • KAB. PIDIE JAYA',
                'wilayah' => 'KAB. PIDIE JAYA',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 4820000000, 'realisasi_51' => 3850000000,
                'pagu_52' => 3544803000, 'realisasi_52' => 2288474671,
                'pagu_53' => 500000000,  'realisasi_53' => 250000000,
                'pagu_57' => 0,          'realisasi_57' => 0,
                'total_pagu' => 8864803000,
                'total_realisasi' => 6388474671,
                'persen' => 72.07,
                'pagu_blokir' => 215814000
            ],
            [
                'no' => 308,
                'kode_satker' => '061818',
                'nama_satker' => 'BADAN NARKOTIKA NASIONAL KABUPATEN PIDIE JAYA',
                'kode_kl' => '077',
                'nama_kl' => 'BADAN NARKOTIKA NASIONAL (BNN) • KAB. PIDIE JAYA',
                'wilayah' => 'KAB. PIDIE JAYA',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 950000000,  'realisasi_51' => 820000000,
                'pagu_52' => 759723000,  'realisasi_52' => 579565350,
                'pagu_53' => 0,          'realisasi_53' => 0,
                'pagu_57' => 0,          'realisasi_57' => 0,
                'total_pagu' => 1709723000,
                'total_realisasi' => 1399565350,
                'persen' => 81.86,
                'pagu_blokir' => 31610000
            ],
            [
                'no' => 309,
                'kode_satker' => '065137',
                'nama_satker' => 'BADAN NARKOTIKA NASIONAL KOTA BANDA ACEH',
                'kode_kl' => '077',
                'nama_kl' => 'BADAN NARKOTIKA NASIONAL (BNN) • KOTA BANDA ACEH',
                'wilayah' => 'KOTA BANDA ACEH',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 880000000,  'realisasi_51' => 690000000,
                'pagu_52' => 656505000,  'realisasi_52' => 438352615,
                'pagu_53' => 0,          'realisasi_53' => 0,
                'pagu_57' => 0,          'realisasi_57' => 0,
                'total_pagu' => 1536505000,
                'total_realisasi' => 1128352615,
                'persen' => 73.44,
                'pagu_blokir' => 11839000
            ],
            [
                'no' => 310,
                'kode_satker' => '018377',
                'nama_satker' => 'PERWAKILAN BADAN KEPENDUDUKAN DAN KELUARGA BERENCANA NASIONAL PROVINSI ACEH',
                'kode_kl' => '068',
                'nama_kl' => 'KEMENTERIAN KEPENDUDUKAN DAN PEMBANGUNAN KELUARGA / BKKBN • KOTA BANDA ACEH',
                'wilayah' => 'KOTA BANDA ACEH',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 48500000000, 'realisasi_51' => 43200000000,
                'pagu_52' => 72379916000, 'realisasi_52' => 61892534925,
                'pagu_53' => 9000000000,  'realisasi_53' => 6500000000,
                'pagu_57' => 0,           'realisasi_57' => 0,
                'total_pagu' => 129879916000,
                'total_realisasi' => 111592534925,
                'persen' => 86.05,
                'pagu_blokir' => 400796000
            ],
            [
                'no' => 311,
                'kode_satker' => '060053',
                'nama_satker' => 'DINAS PEMUDA DAN OLAHRAGA PROVINSI NANGGROE ACEH DARUSSALAM',
                'kode_kl' => '035',
                'nama_kl' => 'KEMENTERIAN PEMUDA DAN OLAHRAGA • ACEH',
                'wilayah' => 'ACEH',
                'kppn' => 'KPPN Banda Aceh (001)',
                'pagu_51' => 0, 'realisasi_51' => 0,
                'pagu_52' => 1000, 'realisasi_52' => 0,
                'pagu_53' => 0, 'realisasi_53' => 0,
                'pagu_57' => 0, 'realisasi_57' => 0,
                'total_pagu' => 1000,
                'total_realisasi' => 0,
                'persen' => 0.00,
                'pagu_blokir' => 1000
            ]
        ];
    }

    public function getJenisBelanjaCards(): array
    {
        $realJenisAll = app(\App\Services\SintesaImportService::class)->getSummary('jenis_belanja_all');
        $realJenis = app(\App\Services\SintesaImportService::class)->getSummary('jenis_belanja');
        $realMap = [];
        if (!empty($realJenisAll)) {
            $realMap = $realJenisAll;
        } elseif (!empty($realJenis)) {
            foreach ($realJenis as $rj) {
                $realMap[$rj['kode']] = $rj;
            }
        }

        $tkdSummary = app(\App\Services\SintesaImportService::class)->getSummary('tkd_summary');
        $ddPagu = $realMap['66']['pagu'] ?? ($tkdSummary['dana_desa_total_pagu'] ?? 1738584898000);
        $ddReal = $realMap['66']['realisasi'] ?? ($tkdSummary['dana_desa_total_realisasi'] ?? 652773329000);
        $ddPct = $ddPagu > 0 ? round(($ddReal / $ddPagu) * 100, 2) : 0;

        return [
            // Row 1: Belanja K/L (Pusat)
            [
                'kode' => '51',
                'nama' => '51 - BELANJA PEGAWAI',
                'kategori' => 'Belanja K/L',
                'pagu' => $realMap['51']['pagu'] ?? 10490946736000,
                'pagu_formatted' => $realMap['51']['pagu_formatted'] ?? 'Rp 10.490.946.736.000',
                'realisasi' => $realMap['51']['realisasi'] ?? 9437578548500,
                'realisasi_formatted' => $realMap['51']['realisasi_formatted'] ?? 'Rp 9.437.578.548.500',
                'persen' => $realMap['51']['persentase'] ?? 89.96,
                'color' => 'blue',
                'border_accent' => 'border-t-blue-500',
                'text_color' => 'text-blue-600',
                'bar_color' => 'bg-blue-600'
            ],
            [
                'kode' => '52',
                'nama' => '52 - BELANJA BARANG',
                'kategori' => 'Belanja K/L',
                'pagu' => $realMap['52']['pagu'] ?? 13445936306000,
                'pagu_formatted' => $realMap['52']['pagu_formatted'] ?? 'Rp 13.445.936.306.000',
                'realisasi' => $realMap['52']['realisasi'] ?? 8803686436287,
                'realisasi_formatted' => $realMap['52']['realisasi_formatted'] ?? 'Rp 8.803.686.436.287',
                'persen' => $realMap['52']['persentase'] ?? 65.47,
                'color' => 'amber',
                'border_accent' => 'border-t-amber-500',
                'text_color' => 'text-amber-600',
                'bar_color' => 'bg-amber-500'
            ],
            [
                'kode' => '53',
                'nama' => '53 - BELANJA MODAL',
                'kategori' => 'Belanja K/L',
                'pagu' => $realMap['53']['pagu'] ?? 10158883768000,
                'pagu_formatted' => $realMap['53']['pagu_formatted'] ?? 'Rp 10.158.883.768.000',
                'realisasi' => $realMap['53']['realisasi'] ?? 4799520972806,
                'realisasi_formatted' => $realMap['53']['realisasi_formatted'] ?? 'Rp 4.799.520.972.806',
                'persen' => $realMap['53']['persentase'] ?? 47.24,
                'color' => 'teal',
                'border_accent' => 'border-t-teal-500',
                'text_color' => 'text-teal-600',
                'bar_color' => 'bg-teal-500'
            ],
            [
                'kode' => '57',
                'nama' => '57 - BANTUAN SOSIAL',
                'kategori' => 'Belanja K/L',
                'pagu' => $realMap['57']['pagu'] ?? 6243900000,
                'pagu_formatted' => $realMap['57']['pagu_formatted'] ?? 'Rp 6.243.900.000',
                'realisasi' => $realMap['57']['realisasi'] ?? 54006716380,
                'realisasi_formatted' => $realMap['57']['realisasi_formatted'] ?? 'Rp 54.006.716.380',
                'persen' => $realMap['57']['persentase'] ?? 864.95,
                'color' => 'pink',
                'border_accent' => 'border-t-pink-500',
                'text_color' => 'text-pink-600',
                'bar_color' => 'bg-pink-500'
            ],
            [
                'kode' => '61',
                'nama' => '61 - DANA BAGI HASIL',
                'kategori' => 'Transfer Ke Daerah',
                'pagu' => $realMap['61']['pagu'] ?? 979414907000,
                'pagu_formatted' => $realMap['61']['pagu_formatted'] ?? 'Rp 979.414.907.000',
                'realisasi' => $realMap['61']['realisasi'] ?? 0,
                'realisasi_formatted' => $realMap['61']['realisasi_formatted'] ?? 'Rp 0',
                'persen' => $realMap['61']['persentase'] ?? 0,
                'color' => 'emerald',
                'border_accent' => 'border-t-emerald-500',
                'text_color' => 'text-emerald-600',
                'bar_color' => 'bg-emerald-500'
            ],
            // Row 2: Transfer Ke Daerah (BUN)
            [
                'kode' => '62',
                'nama' => '62 - DAU',
                'kategori' => 'Transfer Ke Daerah',
                'pagu' => $realMap['62']['pagu'] ?? 18134356324000,
                'pagu_formatted' => $realMap['62']['pagu_formatted'] ?? 'Rp 18.134.356.324.000',
                'realisasi' => $realMap['62']['realisasi'] ?? 0,
                'realisasi_formatted' => $realMap['62']['realisasi_formatted'] ?? 'Rp 0',
                'persen' => $realMap['62']['persentase'] ?? 0,
                'color' => 'indigo',
                'border_accent' => 'border-t-indigo-600',
                'text_color' => 'text-indigo-600',
                'bar_color' => 'bg-indigo-600'
            ],
            [
                'kode' => '63',
                'nama' => '63 - DAK FISIK',
                'kategori' => 'Transfer Ke Daerah',
                'pagu' => $realMap['63']['pagu'] ?? 388263417000,
                'pagu_formatted' => $realMap['63']['pagu_formatted'] ?? 'Rp 388.263.417.000',
                'realisasi' => $realMap['63']['realisasi'] ?? 0,
                'realisasi_formatted' => $realMap['63']['realisasi_formatted'] ?? 'Rp 0',
                'persen' => $realMap['63']['persentase'] ?? 0,
                'color' => 'rose',
                'border_accent' => 'border-t-rose-500',
                'text_color' => 'text-rose-600',
                'bar_color' => 'bg-rose-500'
            ],
            [
                'kode' => '64',
                'nama' => '64 - OTSUS & DID',
                'kategori' => 'Transfer Ke Daerah',
                'pagu' => $realMap['64']['pagu'] ?? 4076166626000,
                'pagu_formatted' => $realMap['64']['pagu_formatted'] ?? 'Rp 4.076.166.626.000',
                'realisasi' => $realMap['64']['realisasi'] ?? 0,
                'realisasi_formatted' => $realMap['64']['realisasi_formatted'] ?? 'Rp 0',
                'persen' => $realMap['64']['persentase'] ?? 0,
                'color' => 'amber',
                'border_accent' => 'border-t-amber-600',
                'text_color' => 'text-amber-700',
                'bar_color' => 'bg-amber-600'
            ],
            [
                'kode' => '65',
                'nama' => '65 - DAK NON FISIK',
                'kategori' => 'Transfer Ke Daerah',
                'pagu' => $realMap['65']['pagu'] ?? 4424703832000,
                'pagu_formatted' => $realMap['65']['pagu_formatted'] ?? 'Rp 4.424.703.832.000',
                'realisasi' => $realMap['65']['realisasi'] ?? 0,
                'realisasi_formatted' => $realMap['65']['realisasi_formatted'] ?? 'Rp 0',
                'persen' => $realMap['65']['persentase'] ?? 0,
                'color' => 'sky',
                'border_accent' => 'border-t-sky-500',
                'text_color' => 'text-sky-600',
                'bar_color' => 'bg-sky-500'
            ],
            [
                'kode' => '66',
                'nama' => '66 - DANA DESA',
                'kategori' => 'Transfer Ke Daerah',
                'pagu' => $ddPagu,
                'pagu_formatted' => 'Rp ' . number_format($ddPagu, 0, ',', '.'),
                'realisasi' => $ddReal,
                'realisasi_formatted' => 'Rp ' . number_format($ddReal, 0, ',', '.'),
                'persen' => $ddPct,
                'color' => 'emerald',
                'border_accent' => 'border-t-emerald-600',
                'text_color' => 'text-emerald-700',
                'bar_color' => 'bg-emerald-600'
            ]
        ];
    }

    public function getJenisBelanjaChart(): array
    {
        $realJenis = app(\App\Services\SintesaImportService::class)->getSummary('jenis_belanja');
        if (!empty($realJenis) && config('app.sintesa_real_data', true)) {
            $labels = [];
            $pagu = [];
            $real = [];
            $paguBadges = [];
            $realBadges = [];
            $details = [];
            foreach ($realJenis as $j) {
                $labels[] = $j['kode'] . ' ' . $j['nama'];
                $pTril = round($j['pagu'] / 1000000000000, 2);
                $rTril = round($j['realisasi'] / 1000000000000, 2);
                $pagu[] = $pTril > 0 ? $pTril : round($j['pagu'] / 1000000000, 2);
                $real[] = $rTril > 0 ? $rTril : round($j['realisasi'] / 1000000000, 2);
                $paguBadges[] = $j['pagu'] >= 1000000000000 ? number_format($pTril, 1, ',', '.') . ' T' : number_format($j['pagu'] / 1000000000, 1, ',', '.') . ' M';
                $realBadges[] = $j['realisasi'] >= 1000000000000 ? number_format($rTril, 1, ',', '.') . ' T' : number_format($j['realisasi'] / 1000000000, 1, ',', '.') . ' M';
                $details[] = [
                    'akun' => $j['kode'],
                    'nama' => $j['nama'],
                    'pagu' => $j['pagu_formatted'],
                    'realisasi' => $j['realisasi_formatted'],
                    'persen' => $j['persentase_formatted'],
                ];
            }
            return [
                'labels' => $labels,
                'pagu' => $pagu,
                'realisasi' => $real,
                'pagu_badges' => $paguBadges,
                'realisasi_badges' => $realBadges,
                'details' => $details
            ];
        }

        return [
            'labels' => ['51 Belanja Pegawai', '52 Belanja Barang', '53 Belanja Modal', '57 Bantuan Sosial'],
            'pagu' => [10.5, 13.4, 10.1, 0.0062],
            'realisasi' => [9.4, 7.9, 4.8, 0.0058],
            'pagu_badges' => ['10,5 T', '13,4 T', '10,1 T', '6,2 M'],
            'realisasi_badges' => ['9,4 T', '7,9 T', '4,8 T', '5,8 M'],
            'details' => [
                [
                    'akun' => '51',
                    'nama' => 'Belanja Pegawai',
                    'pagu' => 'Rp 10.514.860.791.000',
                    'realisasi' => 'Rp 9.387.238.283.000',
                    'persen' => '89,28%'
                ],
                [
                    'akun' => '52',
                    'nama' => 'Belanja Barang',
                    'pagu' => 'Rp 13.476.775.157.000',
                    'realisasi' => 'Rp 7.937.101.623.000',
                    'persen' => '58,89%'
                ],
                [
                    'akun' => '53',
                    'nama' => 'Belanja Modal',
                    'pagu' => 'Rp 10.089.479.239.000',
                    'realisasi' => 'Rp 4.793.189.510.000',
                    'persen' => '47,51%'
                ],
                [
                    'akun' => '57',
                    'nama' => 'Bantuan Sosial',
                    'pagu' => 'Rp 6.243.900.000',
                    'realisasi' => 'Rp 5.767.624.824',
                    'persen' => '92,37%'
                ]
            ]
        ];
    }

    public function getPrioritasPresidenSummary(): array
    {
        $realPrio = app(\App\Services\SintesaImportService::class)->getSummary('tematik_prioritas_summary');
        if (!empty($realPrio) && config('app.sintesa_real_data', true)) {
            return $realPrio;
        }

        return [
            'total_pagu' => 6855202222000,
            'total_pagu_formatted' => 'Rp 6.855.202.222.000',
            'total_pagu_short' => '6,9 T',
            'total_realisasi' => 4691977467364,
            'total_realisasi_formatted' => 'Rp 4.691.977.467.364',
            'sisa_pagu' => 2163224754636,
            'sisa_pagu_formatted' => 'Rp 2.163.224.754.636',
            'sisa_pagu_short' => 'Rp 2,16 T',
            'persen_realisasi' => 68.44,
            'persen_sisa' => 31.56,
            'total_satker' => 25,
            'total_jenis' => 9,
            'total_ro' => 38,
        ];
    }

    public function getPrioritasPresidenJenis(): array
    {
        $realJenis = app(\App\Services\SintesaImportService::class)->getSummary('tematik_prioritas_jenis');
        if (!empty($realJenis) && config('app.sintesa_real_data', true)) {
            return $realJenis;
        }

        return [
            [
                'kode' => '13',
                'nama' => '[13] Preservasi Jalan',
                'label' => 'Preservasi Jalan',
                'pagu' => 3248500000000,
                'realisasi' => 2143495000000,
                'persen' => 65.98,
                'satker_count' => 6,
                'color' => '#2563eb'
            ],
            [
                'kode' => '05',
                'nama' => '[05] Sekolah Rakyat',
                'label' => 'Sekolah Rakyat',
                'pagu' => 1285400000000,
                'realisasi' => 1069452800000,
                'persen' => 83.20,
                'satker_count' => 4,
                'color' => '#10b981'
            ],
            [
                'kode' => '10',
                'nama' => '[10] Revitalisasi Sekolah dan Madrasah',
                'label' => 'Revitalisasi Sekolah & Madrasah',
                'pagu' => 692800000000,
                'realisasi' => 273656000000,
                'persen' => 39.50,
                'satker_count' => 5,
                'color' => '#f43f5e'
            ],
            [
                'kode' => '08',
                'nama' => '[08] Peningkatan Produksi Tanaman Pangan',
                'label' => 'Peningkatan Produksi Tanaman Pangan',
                'pagu' => 548600000000,
                'realisasi' => 428456600000,
                'persen' => 78.10,
                'satker_count' => 5,
                'color' => '#3b82f6'
            ],
            [
                'kode' => '12',
                'nama' => '[12] Irigasi',
                'label' => 'Irigasi',
                'pagu' => 478900000000,
                'realisasi' => 306496000000,
                'persen' => 64.00,
                'satker_count' => 4,
                'color' => '#0ea5e9'
            ],
            [
                'kode' => '20',
                'nama' => '[20] Bantuan Stimulan Perumahan Swadaya',
                'label' => 'Bantuan Stimulan Perumahan Swadaya',
                'pagu' => 335200000000,
                'realisasi' => 318440000000,
                'persen' => 95.00,
                'satker_count' => 5,
                'color' => '#059669'
            ],
            [
                'kode' => '14',
                'nama' => '[14] Preservasi Jembatan',
                'label' => 'Preservasi Jembatan',
                'pagu' => 115400000000,
                'realisasi' => 71548000000,
                'persen' => 62.00,
                'satker_count' => 3,
                'color' => '#6366f1'
            ],
            [
                'kode' => '11',
                'nama' => '[11] Bendungan',
                'label' => 'Bendungan',
                'pagu' => 108200000000,
                'realisasi' => 55182000000,
                'persen' => 51.00,
                'satker_count' => 2,
                'color' => '#f59e0b'
            ],
            [
                'kode' => 'PJ',
                'nama' => '[PJ] Kegiatan Prioritas pada K/L yang Mendukung Prioritas Presiden',
                'label' => 'Kegiatan Prioritas K/L',
                'pagu' => 42202222000,
                'realisasi' => 25251067364,
                'persen' => 59.83,
                'satker_count' => 4,
                'color' => '#8b5cf6'
            ],
        ];
    }

    public function getPrioritasPresidenData(): array
    {
        $realItems = app(\App\Services\SintesaImportService::class)->getSummary('tematik_prioritas_items');
        if (!empty($realItems) && config('app.sintesa_real_data', true)) {
            return $realItems;
        }

        return [
            ['no' => 1, 'kode_satker' => '069264', 'nama_satker' => 'DINAS PERTANIAN DAN PERKEBUNAN ACEH', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'ACEH', 'kode_jenis' => '08', 'jenis' => 'Peningkatan Produksi Tanaman Pangan', 'kegiatan' => '[4579] Pengelolaan Sistem Perbenihan Tanaman Pangan', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 135812000, 'realisasi' => 110115310, 'pct' => 81.08],
            ['no' => 2, 'kode_satker' => '069392', 'nama_satker' => 'DINAS PERTANIAN DAN PERKEBUNAN ACEH', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'ACEH', 'kode_jenis' => 'PJ', 'jenis' => 'Kegiatan Prioritas pada K/L yang Mendukung Prioritas Presiden', 'kegiatan' => '[1762] Pengelolaan Produksi Tanaman Serealia Tanaman Pangan', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 3000000, 'realisasi' => 0, 'pct' => 0.00],
            ['no' => 3, 'kode_satker' => '239413', 'nama_satker' => 'BALAI PEMBIBITAN TERNAK UNGGUL DAN HIJAUAN PAKAN TERNAK INDRAPURI', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => 'PJ', 'jenis' => 'Kegiatan Prioritas pada K/L yang Mendukung Prioritas Presiden', 'kegiatan' => '[1785] Penyediaan Benih dan Bibit Serta Peningkatan Produksi Ternak', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 9089375000, 'realisasi' => 7329328179, 'pct' => 80.64],
            ['no' => 4, 'kode_satker' => '069265', 'nama_satker' => 'DINAS PERTANIAN DAN PERKEBUNAN ACEH', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'ACEH', 'kode_jenis' => '08', 'jenis' => 'Peningkatan Produksi Tanaman Pangan', 'kegiatan' => '[4580] Pengelolaan Produksi Padi & Palawija Unggul Wilayah Aceh', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 148520000000, 'realisasi' => 121340840000, 'pct' => 81.70],
            ['no' => 5, 'kode_satker' => '069410', 'nama_satker' => 'DINAS PERTANIAN KABUPATEN ACEH BESAR (TP)', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '08', 'jenis' => 'Peningkatan Produksi Tanaman Pangan', 'kegiatan' => '[4582] Optimalisasi Lahan Rawa dan Cetak Sawah Baru', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 165400000000, 'realisasi' => 129839000000, 'pct' => 78.50],
            ['no' => 6, 'kode_satker' => '069418', 'nama_satker' => 'DINAS PERTANIAN DAN PANGAN KABUPATEN PIDIE (TP)', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '08', 'jenis' => 'Peningkatan Produksi Tanaman Pangan', 'kegiatan' => '[4583] Dukungan Pompanisasi dan Perluasan Areal Tanam Padi', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 132644188000, 'realisasi' => 101074871256, 'pct' => 76.20],
            ['no' => 7, 'kode_satker' => '069422', 'nama_satker' => 'DINAS PERTANIAN DAN PANGAN KABUPATEN PIDIE JAYA (TP)', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. PIDIE JAYA', 'kode_jenis' => '08', 'jenis' => 'Peningkatan Produksi Tanaman Pangan', 'kegiatan' => '[4584] Bantuan Alat Mesin Pertanian Pra & Pasca Panen', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 101900000000, 'realisasi' => 76091773434, 'pct' => 74.67],
            ['no' => 8, 'kode_satker' => '498551', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH I PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '13', 'jenis' => 'Preservasi Jalan', 'kegiatan' => '[2408] Penyelenggaraan Jalan Nasional Wilayah I Aceh', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 895400000000, 'realisasi' => 648269600000, 'pct' => 72.40],
            ['no' => 9, 'kode_satker' => '498552', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH II PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '13', 'jenis' => 'Preservasi Jalan', 'kegiatan' => '[2408] Preservasi dan Pelebaran Jalan Lintas Timur & Barat Aceh', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 782600000000, 'realisasi' => 532168000000, 'pct' => 68.00],
            ['no' => 10, 'kode_satker' => '498553', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH III PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '13', 'jenis' => 'Preservasi Jalan', 'kegiatan' => '[2408] Penanganan Ruas Jalan Strategis Nasional Sigli - Geumpang', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 612500000000, 'realisasi' => 395675000000, 'pct' => 64.60],
            ['no' => 11, 'kode_satker' => '498554', 'nama_satker' => 'BALAI PELAKSANAAN JALAN NASIONAL ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '13', 'jenis' => 'Preservasi Jalan', 'kegiatan' => '[2409] Dukungan Manajemen & Preservasi Jalan Inpres Jalan Daerah', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 485000000000, 'realisasi' => 310400000000, 'pct' => 64.00],
            ['no' => 12, 'kode_satker' => '498555', 'nama_satker' => 'PELAKSANAAN JALAN BEBAS HAMBATAN PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '13', 'jenis' => 'Preservasi Jalan', 'kegiatan' => '[2410] Pembangunan Akses & Simpang Susun Tol Sigli - Banda Aceh', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 348000000000, 'realisasi' => 191400000000, 'pct' => 55.00],
            ['no' => 13, 'kode_satker' => '498556', 'nama_satker' => 'PERENCANAAN DAN PENGAWASAN JALAN NASIONAL PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '13', 'jenis' => 'Preservasi Jalan', 'kegiatan' => '[2411] Pengawasan Teknis Preservasi Jalan Wilayah Aceh', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 125000000000, 'realisasi' => 65582400000, 'pct' => 52.47],
            ['no' => 14, 'kode_satker' => '498551', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH I PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '14', 'jenis' => 'Preservasi Jembatan', 'kegiatan' => '[2412] Rehabilitasi & Penggantian Jembatan Krueng Cut & Lamnyong', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 54200000000, 'realisasi' => 37940000000, 'pct' => 70.00],
            ['no' => 15, 'kode_satker' => '498552', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH II PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '14', 'jenis' => 'Preservasi Jembatan', 'kegiatan' => '[2412] Pemeliharaan Berkala Jembatan Lintas Pantai Barat Aceh', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 38400000000, 'realisasi' => 22656000000, 'pct' => 59.00],
            ['no' => 16, 'kode_satker' => '498553', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH III PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '14', 'jenis' => 'Preservasi Jembatan', 'kegiatan' => '[2412] Penguatan Struktur Jembatan Ruas Pidie - Pidie Jaya', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 22800000000, 'realisasi' => 10952000000, 'pct' => 48.04],
            ['no' => 17, 'kode_satker' => '498077', 'nama_satker' => 'BALAI WILAYAH SUNGAI SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '12', 'jenis' => 'Irigasi', 'kegiatan' => '[5036] Pengembangan & Rehabilitasi Jaringan Irigasi Krueng Aceh', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 184500000000, 'realisasi' => 125460000000, 'pct' => 68.00],
            ['no' => 18, 'kode_satker' => '498078', 'nama_satker' => 'SNVT PELAKSANAAN JARINGAN PEMANFAATAN AIR SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '12', 'jenis' => 'Irigasi', 'kegiatan' => '[5036] Rehabilitasi Daerah Irigasi Baro Raya & Tiro Pidie', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 142800000000, 'realisasi' => 92820000000, 'pct' => 65.00],
            ['no' => 19, 'kode_satker' => '498079', 'nama_satker' => 'OPERASI DAN PEMELIHARAAN SUMBER DAYA AIR SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '12', 'jenis' => 'Irigasi', 'kegiatan' => '[5037] Program Percepatan Peningkatan Tata Guna Air Irigasi (P3-TGAI)', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 96400000000, 'realisasi' => 61696000000, 'pct' => 64.00],
            ['no' => 20, 'kode_satker' => '498078', 'nama_satker' => 'SNVT PELAKSANAAN JARINGAN PEMANFAATAN AIR SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. PIDIE JAYA', 'kode_jenis' => '12', 'jenis' => 'Irigasi', 'kegiatan' => '[5038] Pembangunan Saluran Primer Irigasi Meureudu', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 55200000000, 'realisasi' => 26520000000, 'pct' => 48.04],
            ['no' => 21, 'kode_satker' => '498081', 'nama_satker' => 'SNVT PEMBANGUNAN BENDUNGAN BWS SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '11', 'jenis' => 'Bendungan', 'kegiatan' => '[5039] Pembangunan Bendungan Rukoh & Pengarah Aliran Pidie', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 68400000000, 'realisasi' => 37620000000, 'pct' => 55.00],
            ['no' => 22, 'kode_satker' => '498081', 'nama_satker' => 'SNVT PEMBANGUNAN BENDUNGAN BWS SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '11', 'jenis' => 'Bendungan', 'kegiatan' => '[5039] Konservasi & Pengamanan Tubuh Bendungan Keliling', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 39800000000, 'realisasi' => 17562000000, 'pct' => 44.13],
            ['no' => 23, 'kode_satker' => '632411', 'nama_satker' => 'BALAI PRASARANA PERMUKIMAN WILAYAH ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '05', 'jenis' => 'Sekolah Rakyat', 'kegiatan' => '[6102] Pembangunan Gedung & Asrama Sekolah Rakyat Terpadu Aceh', 'program' => '[DI] Program Pendidikan & Perlindungan Sosial', 'pagu' => 542800000000, 'realisasi' => 461380000000, 'pct' => 85.00],
            ['no' => 24, 'kode_satker' => '632412', 'nama_satker' => 'PELAKSANAAN PRASARANA PERMUKIMAN PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '05', 'jenis' => 'Sekolah Rakyat', 'kegiatan' => '[6102] Penyediaan Sarana Kelas & Laboratorium Sekolah Rakyat Aceh Besar', 'program' => '[DI] Program Pendidikan & Perlindungan Sosial', 'pagu' => 386400000000, 'realisasi' => 324576000000, 'pct' => 84.00],
            ['no' => 25, 'kode_satker' => '690510', 'nama_satker' => 'SENTRA DARUSSA\'ADAH DI ACEH BESAR', 'kl' => 'KEMENTERIAN SOSIAL', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '05', 'jenis' => 'Sekolah Rakyat', 'kegiatan' => '[6105] Operasional & Beasiswa Siswa Sekolah Rakyat Aceh', 'program' => '[DI] Program Pendidikan & Perlindungan Sosial', 'pagu' => 218600000000, 'realisasi' => 181438000000, 'pct' => 83.00],
            ['no' => 26, 'kode_satker' => '632412', 'nama_satker' => 'PELAKSANAAN PRASARANA PERMUKIMAN PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '05', 'jenis' => 'Sekolah Rakyat', 'kegiatan' => '[6103] Pembangunan Fasilitas Olahraga & Perpustakaan Sekolah Rakyat Pidie', 'program' => '[DI] Program Pendidikan & Perlindungan Sosial', 'pagu' => 137600000000, 'realisasi' => 102058800000, 'pct' => 74.17],
            ['no' => 27, 'kode_satker' => '418012', 'nama_satker' => 'KANWIL KEMENTERIAN AGAMA PROVINSI ACEH', 'kl' => 'KEMENTERIAN AGAMA', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '10', 'jenis' => 'Revitalisasi Sekolah dan Madrasah', 'kegiatan' => '[2129] Revitalisasi Ruang Kelas Madrasah Ibtidaiyah & Tsanawiyah Aceh', 'program' => '[DL] Program PAUD dan Wajib Belajar 12 Tahun', 'pagu' => 214500000000, 'realisasi' => 98670000000, 'pct' => 46.00],
            ['no' => 28, 'kode_satker' => '418015', 'nama_satker' => 'KANTOR KEMENTERIAN AGAMA KABUPATEN ACEH BESAR', 'kl' => 'KEMENTERIAN AGAMA', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '10', 'jenis' => 'Revitalisasi Sekolah dan Madrasah', 'kegiatan' => '[2129] Rehabilitasi Gedung Madrasah Aliyah & Sanitasi Pendidikan', 'program' => '[DL] Program PAUD dan Wajib Belajar 12 Tahun', 'pagu' => 128400000000, 'realisasi' => 53928000000, 'pct' => 42.00],
            ['no' => 29, 'kode_satker' => '418020', 'nama_satker' => 'KANTOR KEMENTERIAN AGAMA KABUPATEN PIDIE', 'kl' => 'KEMENTERIAN AGAMA', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '10', 'jenis' => 'Revitalisasi Sekolah dan Madrasah', 'kegiatan' => '[2129] Rehabilitasi Sarana Prasarana Madrasah Wilayah Pidie', 'program' => '[DL] Program PAUD dan Wajib Belajar 12 Tahun', 'pagu' => 112600000000, 'realisasi' => 42788000000, 'pct' => 38.00],
            ['no' => 30, 'kode_satker' => '419501', 'nama_satker' => 'BALAI PENJAMINAN MUTU PENDIDIKAN (BPMP) PROVINSI ACEH', 'kl' => 'KEMENTERIAN PENDIDIKAN DASAR DAN MENENGAH', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '10', 'jenis' => 'Revitalisasi Sekolah dan Madrasah', 'kegiatan' => '[4421] Revitalisasi SD, SMP, dan SMA Prioritas Wilayah Aceh', 'program' => '[DL] Program PAUD dan Wajib Belajar 12 Tahun', 'pagu' => 165800000000, 'realisasi' => 56372000000, 'pct' => 34.00],
            ['no' => 31, 'kode_satker' => '418024', 'nama_satker' => 'KANTOR KEMENTERIAN AGAMA KOTA SABANG', 'kl' => 'KEMENTERIAN AGAMA', 'wilayah' => 'KOTA SABANG', 'kode_jenis' => '10', 'jenis' => 'Revitalisasi Sekolah dan Madrasah', 'kegiatan' => '[2129] Revitalisasi Madrasah Terpadu Pulau Weh', 'program' => '[DL] Program PAUD dan Wajib Belajar 12 Tahun', 'pagu' => 71500000000, 'realisasi' => 21898000000, 'pct' => 30.63],
            ['no' => 32, 'kode_satker' => '415901', 'nama_satker' => 'RSUD DR. ZAINOEL ABIDIN / DEKONSENTRASI KESEHATAN ACEH', 'kl' => 'KEMENTERIAN KESEHATAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => 'PJ', 'jenis' => 'Kegiatan Prioritas pada K/L yang Mendukung Prioritas Presiden', 'kegiatan' => '[2088] Penguatan Layanan Cek Kesehatan Gratis & Pencegahan Stunting', 'program' => '[DG] Program Pelayanan Kesehatan dan JKN', 'pagu' => 19650000000, 'realisasi' => 11397000000, 'pct' => 58.00],
            ['no' => 33, 'kode_satker' => '018377', 'nama_satker' => 'PERWAKILAN BKKBN PROVINSI ACEH', 'kl' => 'KEMENTERIAN KEPENDUDUKAN DAN PEMBANGUNAN KELUARGA', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => 'PJ', 'jenis' => 'Kegiatan Prioritas pada K/L yang Mendukung Prioritas Presiden', 'kegiatan' => '[3152] Percepatan Penurunan Stunting dan Gizi Keluarga Aceh', 'program' => '[DH] Program Pembangunan Keluarga & Kependudukan', 'pagu' => 13459847000, 'realisasi' => 6524739185, 'pct' => 48.48],
            ['no' => 34, 'kode_satker' => '691487', 'nama_satker' => 'Balai Pelaksana Penyediaan Perumahan dan Kawasan Permukiman Sumatera I', 'kl' => 'KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '20', 'jenis' => 'Bantuan Stimulan Perumahan Swadaya', 'kegiatan' => '[7658] Dukungan Peningkatan Kualitas Rumah Swadaya Aceh Besar & Pidie', 'program' => '[IA] Program Perumahan dan Kawasan Permukiman', 'pagu' => 24366007000, 'realisasi' => 14436725071, 'pct' => 59.25],
            ['no' => 35, 'kode_satker' => '691434', 'nama_satker' => 'Perumahan dan Kawasan Permukiman Provinsi Aceh', 'kl' => 'KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '20', 'jenis' => 'Bantuan Stimulan Perumahan Swadaya', 'kegiatan' => '[7659] Penyelenggaraan Infrastruktur Kawasan Permukiman', 'program' => '[IA] Program Perumahan dan Kawasan Permukiman', 'pagu' => 88791761000, 'realisasi' => 87691995974, 'pct' => 98.78],
            ['no' => 36, 'kode_satker' => '691487', 'nama_satker' => 'Balai Pelaksana Penyediaan Perumahan dan Kawasan Permukiman Sumatera I', 'kl' => 'KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '20', 'jenis' => 'Bantuan Stimulan Perumahan Swadaya', 'kegiatan' => '[7660] Penyelenggaraan Perumahan Perdesaan', 'program' => '[IA] Program Perumahan dan Kawasan Permukiman', 'pagu' => 57669000, 'realisasi' => 0, 'pct' => 0.00],
            ['no' => 37, 'kode_satker' => '691488', 'nama_satker' => 'Perumahan dan Kawasan Permukiman Provinsi Aceh', 'kl' => 'KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '20', 'jenis' => 'Bantuan Stimulan Perumahan Swadaya', 'kegiatan' => '[7660] Penyelenggaraan Perumahan Perdesaan', 'program' => '[IA] Program Perumahan dan Kawasan Permukiman', 'pagu' => 162176833000, 'realisasi' => 158015474487, 'pct' => 97.43],
            ['no' => 38, 'kode_satker' => '691542', 'nama_satker' => 'Perumahan dan Kawasan Permukiman Provinsi Aceh', 'kl' => 'KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '20', 'jenis' => 'Bantuan Stimulan Perumahan Swadaya', 'kegiatan' => '[7661] Penyelenggaraan Perumahan Perkotaan', 'program' => '[IA] Program Perumahan dan Kawasan Permukiman', 'pagu' => 59807730000, 'realisasi' => 58295804468, 'pct' => 97.47],
        ];
    }

    public function getProgramStrategisSummary(): array
    {
        $real = app(\App\Services\SintesaImportService::class)->getSummary('program_strategis_summary');
        if (!empty($real) && config('app.sintesa_real_data', true)) {
            return $real;
        }
        return [
            'total_pagu' => 4258377584000,
            'total_pagu_formatted' => 'Rp 4.258.377.584.000',
            'total_pagu_short' => '4,3 T',
            'total_realisasi' => 2415976027885,
            'total_realisasi_formatted' => 'Rp 2.415.976.027.885',
            'sisa_pagu' => 1842401556115,
            'sisa_pagu_formatted' => 'Rp 1.842.401.556.115',
            'sisa_pagu_short' => 'Rp 1,84 T',
            'persen_realisasi' => 56.73,
            'persen_sisa' => 43.27,
            'total_satker' => 27,
            'total_jenis' => 6,
            'total_ro' => 52,
        ];
    }

    public function getProgramStrategisJenis(): array
    {
        $real = app(\App\Services\SintesaImportService::class)->getSummary('program_strategis_jenis');
        if (!empty($real) && config('app.sintesa_real_data', true)) {
            return $real;
        }
        return [
            [
                'kode' => '11',
                'nama' => '[11] Pembangunan 3 juta rumah',
                'label' => 'Pembangunan 3 Juta Rumah',
                'pagu' => 1816746981000,
                'realisasi' => 383339813089,
                'persen' => 21.10,
                'ro_count' => 2,
                'color' => '#f43f5e'
            ],
            [
                'kode' => '04',
                'nama' => '[04] Sekolah Rakyat',
                'label' => 'Sekolah Rakyat',
                'pagu' => 1302518068000,
                'realisasi' => 1081812812890,
                'persen' => 83.06,
                'ro_count' => 1,
                'color' => '#10b981'
            ],
            [
                'kode' => '23',
                'nama' => '[23] Ketahanan Pangan',
                'label' => 'Ketahanan Pangan',
                'pagu' => 558420000000,
                'realisasi' => 431658660000,
                'persen' => 77.30,
                'ro_count' => 22,
                'color' => '#2563eb'
            ],
            [
                'kode' => '27',
                'nama' => '[27] Padat Karya',
                'label' => 'Padat Karya',
                'pagu' => 304850000000,
                'realisasi' => 288083250000,
                'persen' => 94.50,
                'ro_count' => 13,
                'color' => '#059669'
            ],
            [
                'kode' => '02',
                'nama' => '[02] Lumbung Pangan',
                'label' => 'Lumbung Pangan',
                'pagu' => 260420000000,
                'realisasi' => 219534060000,
                'persen' => 84.30,
                'ro_count' => 10,
                'color' => '#0ea5e9'
            ],
            [
                'kode' => '03',
                'nama' => '[03] Irigasi',
                'label' => 'Irigasi',
                'pagu' => 15422535000,
                'realisasi' => 11547431906,
                'persen' => 74.87,
                'ro_count' => 4,
                'color' => '#6366f1'
            ],
        ];
    }

    public function getProgramStrategisData(): array
    {
        $real = app(\App\Services\SintesaImportService::class)->getSummary('program_strategis_items');
        if (!empty($real) && config('app.sintesa_real_data', true)) {
            return $real;
        }
        return [
            ['no' => 1, 'kode_satker' => '069264', 'nama_satker' => 'DINAS PERTANIAN DAN PERKEBUNAN ACEH', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'ACEH', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[4579] Pengelolaan Sistem Perbenihan Tanaman Pangan', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 116670000, 'realisasi' => 96435310, 'pct' => 82.66, 'blokir' => 0],
            ['no' => 2, 'kode_satker' => '069392', 'nama_satker' => 'DINAS PERTANIAN DAN PERKEBUNAN ACEH', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'ACEH', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[1761] Pengelolaan Produksi Aneka Kacang dan Umbi Tanaman Pangan', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 633000, 'realisasi' => 0, 'pct' => 0.00, 'blokir' => 0],
            ['no' => 3, 'kode_satker' => '069392', 'nama_satker' => 'DINAS PERTANIAN DAN PERKEBUNAN ACEH', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'ACEH', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[1762] Pengelolaan Produksi Tanaman Serealia Tanaman Pangan', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 3000000, 'realisasi' => 0, 'pct' => 0.00, 'blokir' => 0],
            ['no' => 4, 'kode_satker' => '239413', 'nama_satker' => 'BALAI PEMBIBITAN TERNAK UNGGUL DAN HIJAUAN PAKAN TERNAK INDRAPURI', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[1785] Penyediaan Benih dan Bibit Serta Peningkatan Produksi Ternak', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 9089375000, 'realisasi' => 7329328179, 'pct' => 80.64, 'blokir' => 0],
            ['no' => 5, 'kode_satker' => '069265', 'nama_satker' => 'DINAS PERTANIAN DAN PERKEBUNAN ACEH', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'ACEH', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[4580] Pengelolaan Produksi Padi & Jagung Unggul Wilayah Aceh', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 42850000000, 'realisasi' => 34708500000, 'pct' => 81.00, 'blokir' => 0],
            ['no' => 6, 'kode_satker' => '069410', 'nama_satker' => 'DINAS PERTANIAN KABUPATEN ACEH BESAR (TP)', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[4582] Optimalisasi Lahan Pertanian dan Cetak Sawah Baru', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 54200000000, 'realisasi' => 43902000000, 'pct' => 81.00, 'blokir' => 0],
            ['no' => 7, 'kode_satker' => '069418', 'nama_satker' => 'DINAS PERTANIAN DAN PANGAN KABUPATEN PIDIE (TP)', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[4583] Penguatan Pompanisasi dan Perluasan Areal Tanam Padi', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 48600000000, 'realisasi' => 38880000000, 'pct' => 80.00, 'blokir' => 0],
            ['no' => 8, 'kode_satker' => '069422', 'nama_satker' => 'DINAS PERTANIAN DAN PANGAN KABUPATEN PIDIE JAYA (TP)', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. PIDIE JAYA', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[4584] Bantuan Alat Mesin Pertanian Pra & Pasca Panen', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 36400000000, 'realisasi' => 28392000000, 'pct' => 78.00, 'blokir' => 0],
            ['no' => 9, 'kode_satker' => '069501', 'nama_satker' => 'BALAI PENERAPAN STANDAR INSTRUMEN PERTANIAN ACEH', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[4590] Standardisasi Benih Sumber dan Teknologi Pertanian Modern', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 18750000000, 'realisasi' => 15187500000, 'pct' => 81.00, 'blokir' => 0],
            ['no' => 10, 'kode_satker' => '412510', 'nama_satker' => 'DINAS KELAUTAN DAN PERIKANAN PROVINSI ACEH (TP)', 'kl' => 'KEMENTERIAN KELAUTAN DAN PERIKANAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[2361] Pengembangan Perikanan Budidaya & Tambak Pesisir', 'program' => '[HB] Program Pengelolaan Perikanan dan Kelautan', 'pagu' => 34200000000, 'realisasi' => 26676000000, 'pct' => 78.00, 'blokir' => 0],
            ['no' => 11, 'kode_satker' => '412512', 'nama_satker' => 'PELABUHAN PERIKANAN SAMUDERA KUTARAJA LAMPULO', 'kl' => 'KEMENTERIAN KELAUTAN DAN PERIKANAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[2364] Penguatan Rantai Dingin & Logistik Ikan Nasional', 'program' => '[HB] Program Pengelolaan Perikanan dan Kelautan', 'pagu' => 42500000000, 'realisasi' => 33575000000, 'pct' => 79.00, 'blokir' => 425000000],
            ['no' => 12, 'kode_satker' => '412518', 'nama_satker' => 'BALAI PERIKANAN BUDIDAYA AIR PAYAU UJUNG BATEE', 'kl' => 'KEMENTERIAN KELAUTAN DAN PERIKANAN', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[2365] Produksi Benih Udang Vaname & Bandeng Unggul Aceh', 'program' => '[HB] Program Pengelolaan Perikanan dan Kelautan', 'pagu' => 29800000000, 'realisasi' => 24436000000, 'pct' => 82.00, 'blokir' => 0],
            ['no' => 13, 'kode_satker' => '648201', 'nama_satker' => 'DINAS PANGAN PROVINSI ACEH (DEKON)', 'kl' => 'BADAN PANGAN NASIONAL', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[6812] Stabilisasi Pasokan dan Harga Pangan Strategis Daerah', 'program' => '[HC] Program Dukungan Ketahanan Pangan Nasional', 'pagu' => 26400000000, 'realisasi' => 21648000000, 'pct' => 82.00, 'blokir' => 0],
            ['no' => 14, 'kode_satker' => '648205', 'nama_satker' => 'DINAS PANGAN KABUPATEN ACEH BESAR (TP)', 'kl' => 'BADAN PANGAN NASIONAL', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[6814] Penguatan Cadangan Pangan Pemerintah Daerah', 'program' => '[HC] Program Dukungan Ketahanan Pangan Nasional', 'pagu' => 19500000000, 'realisasi' => 15210000000, 'pct' => 78.00, 'blokir' => 0],
            ['no' => 15, 'kode_satker' => '648209', 'nama_satker' => 'DINAS PANGAN KABUPATEN PIDIE (TP)', 'kl' => 'BADAN PANGAN NASIONAL', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[6814] Diversifikasi Pangan Lokal dan Penanganan Daerah Rawan Pangan', 'program' => '[HC] Program Dukungan Ketahanan Pangan Nasional', 'pagu' => 17850000000, 'realisasi' => 13744500000, 'pct' => 77.00, 'blokir' => 0],
            ['no' => 16, 'kode_satker' => '069430', 'nama_satker' => 'BALAI KARANTINA HEWAN, IKAN, DAN TUMBUHAN ACEH', 'kl' => 'BADAN KARANTINA INDONESIA', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[5120] Pengawasan Mutu dan Keamanan Komoditas Pangan Ekspor/Impor', 'program' => '[HD] Program Sistem Perkarantinaan Nasional', 'pagu' => 24600000000, 'realisasi' => 19926000000, 'pct' => 81.00, 'blokir' => 0],
            ['no' => 17, 'kode_satker' => '069445', 'nama_satker' => 'SMK PEMBANGUNAN PERTANIAN NEGERI SAREE', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[1790] Penumbuhan Wirausahawan Muda Pertanian & Petani Milenial', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 16200000000, 'realisasi' => 13284000000, 'pct' => 82.00, 'blokir' => 0],
            ['no' => 18, 'kode_satker' => '498077', 'nama_satker' => 'BALAI WILAYAH SUNGAI SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[5036] Dukungan Sumber Air Baku Pertanian Wilayah Aceh', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 38500000000, 'realisasi' => 28490000000, 'pct' => 74.00, 'blokir' => 0],
            ['no' => 19, 'kode_satker' => '498078', 'nama_satker' => 'SNVT PELAKSANAAN JARINGAN PEMANFAATAN AIR SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[5038] Pembangunan Embung Pertanian Sentra Padi Pidie', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 32400000000, 'realisasi' => 23328000000, 'pct' => 72.00, 'blokir' => 0],
            ['no' => 20, 'kode_satker' => '069264', 'nama_satker' => 'DINAS PERTANIAN DAN PERKEBUNAN ACEH', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'ACEH', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[4586] Pengendalian Organisme Pengganggu Tumbuhan (OPT) & Dampak Iklim', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 24100000000, 'realisasi' => 17111000000, 'pct' => 71.00, 'blokir' => 0],
            ['no' => 21, 'kode_satker' => '069265', 'nama_satker' => 'DINAS PERTANIAN DAN PERKEBUNAN ACEH', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'ACEH', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[4588] Rehabilitasi Tanaman Perkebunan Rakyat Kopi & Kakao Aceh', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 25600000000, 'realisasi' => 16896000000, 'pct' => 66.00, 'blokir' => 0],
            ['no' => 22, 'kode_satker' => '069422', 'nama_satker' => 'DINAS PERTANIAN DAN PANGAN KABUPATEN PIDIE JAYA (TP)', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. PIDIE JAYA', 'kode_jenis' => '23', 'jenis' => 'Ketahanan Pangan', 'kegiatan' => '[4589] Penguatan Infrastruktur Jalan Usaha Tani Wilayah Pidie Jaya', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 16760322000, 'realisasi' => 9838396511, 'pct' => 58.70, 'blokir' => 0],
            ['no' => 23, 'kode_satker' => '069264', 'nama_satker' => 'DINAS PERTANIAN DAN PERKEBUNAN ACEH', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'ACEH', 'kode_jenis' => '02', 'jenis' => 'Lumbung Pangan', 'kegiatan' => '[4601] Pengembangan Kawasan Food Estate / Lumbung Pangan Aceh Besar', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 46800000000, 'realisasi' => 41184000000, 'pct' => 88.00, 'blokir' => 0],
            ['no' => 24, 'kode_satker' => '069410', 'nama_satker' => 'DINAS PERTANIAN KABUPATEN ACEH BESAR (TP)', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '02', 'jenis' => 'Lumbung Pangan', 'kegiatan' => '[4602] Pembangunan Rice Milling Unit (RMU) Modern & Silo Gabah', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 38200000000, 'realisasi' => 33234000000, 'pct' => 87.00, 'blokir' => 0],
            ['no' => 25, 'kode_satker' => '069418', 'nama_satker' => 'DINAS PERTANIAN DAN PANGAN KABUPATEN PIDIE (TP)', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '02', 'jenis' => 'Lumbung Pangan', 'kegiatan' => '[4603] Klasterisasi Sentra Beras Unggul Kabupaten Pidie', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 34500000000, 'realisasi' => 29670000000, 'pct' => 86.00, 'blokir' => 0],
            ['no' => 26, 'kode_satker' => '069422', 'nama_satker' => 'DINAS PERTANIAN DAN PANGAN KABUPATEN PIDIE JAYA (TP)', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. PIDIE JAYA', 'kode_jenis' => '02', 'jenis' => 'Lumbung Pangan', 'kegiatan' => '[4604] Pembangunan Gudang Penyangga & Pengering Gabah (Dryer)', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 28400000000, 'realisasi' => 24140000000, 'pct' => 85.00, 'blokir' => 0],
            ['no' => 27, 'kode_satker' => '239413', 'nama_satker' => 'BALAI PEMBIBITAN TERNAK UNGGUL DAN HIJAUAN PAKAN TERNAK INDRAPURI', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '02', 'jenis' => 'Lumbung Pangan', 'kegiatan' => '[4605] Sentra Pembibitan Sapi Aceh & Lumbung Pakan Ternak', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 24600000000, 'realisasi' => 20910000000, 'pct' => 85.00, 'blokir' => 0],
            ['no' => 28, 'kode_satker' => '498077', 'nama_satker' => 'BALAI WILAYAH SUNGAI SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '02', 'jenis' => 'Lumbung Pangan', 'kegiatan' => '[5040] Infrastruktur Tata Air Kawasan Lumbung Pangan Aceh', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 29400000000, 'realisasi' => 24402000000, 'pct' => 83.00, 'blokir' => 0],
            ['no' => 29, 'kode_satker' => '648201', 'nama_satker' => 'DINAS PANGAN PROVINSI ACEH (DEKON)', 'kl' => 'BADAN PANGAN NASIONAL', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '02', 'jenis' => 'Lumbung Pangan', 'kegiatan' => '[6820] Penguatan Lumbung Pangan Masyarakat Gampong', 'program' => '[HC] Program Dukungan Ketahanan Pangan Nasional', 'pagu' => 18500000000, 'realisasi' => 15170000000, 'pct' => 82.00, 'blokir' => 0],
            ['no' => 30, 'kode_satker' => '069501', 'nama_satker' => 'BALAI PENERAPAN STANDAR INSTRUMEN PERTANIAN ACEH', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '02', 'jenis' => 'Lumbung Pangan', 'kegiatan' => '[4606] Mekanisasi Pertanian Presisi Kawasan Lumbung Pangan', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 16200000000, 'realisasi' => 12960000000, 'pct' => 80.00, 'blokir' => 0],
            ['no' => 31, 'kode_satker' => '412512', 'nama_satker' => 'PELABUHAN PERIKANAN SAMUDERA KUTARAJA LAMPULO', 'kl' => 'KEMENTERIAN KELAUTAN DAN PERIKANAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '02', 'jenis' => 'Lumbung Pangan', 'kegiatan' => '[2370] Sentra Lumbung Ikan Nasional Wilayah Barat Sumatera', 'program' => '[HB] Program Pengelolaan Perikanan dan Kelautan', 'pagu' => 14120000000, 'realisasi' => 10872400000, 'pct' => 77.00, 'blokir' => 0],
            ['no' => 32, 'kode_satker' => '069265', 'nama_satker' => 'DINAS PERTANIAN DAN PERKEBUNAN ACEH', 'kl' => 'KEMENTERIAN PERTANIAN', 'wilayah' => 'ACEH', 'kode_jenis' => '02', 'jenis' => 'Lumbung Pangan', 'kegiatan' => '[4608] Sertifikasi Lahan Pangan Berkelanjutan (LP2B)', 'program' => '[HA] Program Ketersediaan, Akses dan Konsumsi Pangan Berkualitas', 'pagu' => 9700000000, 'realisasi' => 6991660000, 'pct' => 72.08, 'blokir' => 0],
            ['no' => 33, 'kode_satker' => '498079', 'nama_satker' => 'OPERASI DAN PEMELIHARAAN SUMBER DAYA AIR SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '03', 'jenis' => 'Irigasi', 'kegiatan' => '[5036] Rehabilitasi Saluran Irigasi Tersier Krueng Baro', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 5620000000, 'realisasi' => 4496000000, 'pct' => 80.00, 'blokir' => 0],
            ['no' => 34, 'kode_satker' => '498078', 'nama_satker' => 'SNVT PELAKSANAAN JARINGAN PEMANFAATAN AIR SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '03', 'jenis' => 'Irigasi', 'kegiatan' => '[5036] Normalisasi Jaringan Irigasi Tiro Kabupaten Pidie', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 4280000000, 'realisasi' => 3252800000, 'pct' => 76.00, 'blokir' => 0],
            ['no' => 35, 'kode_satker' => '498077', 'nama_satker' => 'BALAI WILAYAH SUNGAI SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '03', 'jenis' => 'Irigasi', 'kegiatan' => '[5037] Pemeliharaan Pintu Air & Bendung Irigasi Aceh Besar', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 3150000000, 'realisasi' => 2268000000, 'pct' => 72.00, 'blokir' => 0],
            ['no' => 36, 'kode_satker' => '498078', 'nama_satker' => 'SNVT PELAKSANAAN JARINGAN PEMANFAATAN AIR SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. PIDIE JAYA', 'kode_jenis' => '03', 'jenis' => 'Irigasi', 'kegiatan' => '[5038] Perbaikan Saluran Irigasi Sekunder Meureudu', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 2372535000, 'realisasi' => 1530631906, 'pct' => 64.51, 'blokir' => 0],
            ['no' => 37, 'kode_satker' => '498551', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH I PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '27', 'jenis' => 'Padat Karya', 'kegiatan' => '[7696] Padat Karya Tunai Pemeliharaan Rutin Jalan & Drainase Wil. I', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 48500000000, 'realisasi' => 46560000000, 'pct' => 96.00, 'blokir' => 0],
            ['no' => 38, 'kode_satker' => '498552', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH II PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '27', 'jenis' => 'Padat Karya', 'kegiatan' => '[7696] Padat Karya Tunai Pemeliharaan Bahu Jalan Lintas Barat', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 42600000000, 'realisasi' => 40896000000, 'pct' => 96.00, 'blokir' => 0],
            ['no' => 39, 'kode_satker' => '498553', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH III PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '27', 'jenis' => 'Padat Karya', 'kegiatan' => '[7696] Padat Karya Tunai Pengecatan & Pembersihan Jembatan Sigli', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 36800000000, 'realisasi' => 35328000000, 'pct' => 96.00, 'blokir' => 0],
            ['no' => 40, 'kode_satker' => '498079', 'nama_satker' => 'OPERASI DAN PEMELIHARAAN SUMBER DAYA AIR SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '27', 'jenis' => 'Padat Karya', 'kegiatan' => '[5037] Padat Karya P3-TGAI Swakelola Kelompok Petani Pemakai Air', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 44200000000, 'realisasi' => 42432000000, 'pct' => 96.00, 'blokir' => 0],
            ['no' => 41, 'kode_satker' => '632411', 'nama_satker' => 'BALAI PRASARANA PERMUKIMAN WILAYAH ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '27', 'jenis' => 'Padat Karya', 'kegiatan' => '[6110] Program Pengembangan Infrastruktur Sosial Ekonomi Wilayah (PISEW)', 'program' => '[IA] Program Perumahan dan Kawasan Permukiman', 'pagu' => 31500000000, 'realisasi' => 29925000000, 'pct' => 95.00, 'blokir' => 0],
            ['no' => 42, 'kode_satker' => '632412', 'nama_satker' => 'PELAKSANAAN PRASARANA PERMUKIMAN PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '27', 'jenis' => 'Padat Karya', 'kegiatan' => '[6111] Sanitasi Berbasis Masyarakat (SANIMAS) & TPS3R Padat Karya', 'program' => '[IA] Program Perumahan dan Kawasan Permukiman', 'pagu' => 26400000000, 'realisasi' => 24816000000, 'pct' => 94.00, 'blokir' => 0],
            ['no' => 43, 'kode_satker' => '632412', 'nama_satker' => 'PELAKSANAAN PRASARANA PERMUKIMAN PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. PIDIE', 'kode_jenis' => '27', 'jenis' => 'Padat Karya', 'kegiatan' => '[6112] Penyediaan Air Minum dan Sanitasi Berbasis Masyarakat (PAMSIMAS)', 'program' => '[IA] Program Perumahan dan Kawasan Permukiman', 'pagu' => 21800000000, 'realisasi' => 20492000000, 'pct' => 94.00, 'blokir' => 0],
            ['no' => 44, 'kode_satker' => '287911', 'nama_satker' => 'BALAI PENGELOLA TRANSPORTASI DARAT KELAS II ACEH', 'kl' => 'KEMENTERIAN PERHUBUNGAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '27', 'jenis' => 'Padat Karya', 'kegiatan' => '[4650] Padat Karya Pemeliharaan Terminal Tipe A & Pelabuhan Penyeberangan', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 14500000000, 'realisasi' => 13485000000, 'pct' => 93.00, 'blokir' => 0],
            ['no' => 45, 'kode_satker' => '413621', 'nama_satker' => 'KANTOR KESYAHBANDARAN DAN OTORITAS PELABUHAN MALAHAYATI', 'kl' => 'KEMENTERIAN PERHUBUNGAN', 'wilayah' => 'KAB. ACEH BESAR', 'kode_jenis' => '27', 'jenis' => 'Padat Karya', 'kegiatan' => '[4654] Padat Karya Kebersihan & Perawatan Dermaga Pelabuhan Malahayati', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 9800000000, 'realisasi' => 9114000000, 'pct' => 93.00, 'blokir' => 0],
            ['no' => 46, 'kode_satker' => '413625', 'nama_satker' => 'KANTOR UNIT PENYELENGGARA PELABUHAN SABANG', 'kl' => 'KEMENTERIAN PERHUBUNGAN', 'wilayah' => 'KOTA SABANG', 'kode_jenis' => '27', 'jenis' => 'Padat Karya', 'kegiatan' => '[4655] Padat Karya Pemeliharaan Fasilitas Pelabuhan Balohan Sabang', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 8400000000, 'realisasi' => 7728000000, 'pct' => 92.00, 'blokir' => 0],
            ['no' => 47, 'kode_satker' => '452101', 'nama_satker' => 'BALAI PELATIHAN VOKASI DAN PRODUKTIVITAS (BPVP) BANDA ACEH', 'kl' => 'KEMENTERIAN KETENAGAKERJAAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '27', 'jenis' => 'Padat Karya', 'kegiatan' => '[3912] Program Padat Karya Produktif & Tenaga Kerja Mandiri Aceh', 'program' => '[DK] Program Pelatihan Vokasi dan Produktivitas', 'pagu' => 11200000000, 'realisasi' => 9968000000, 'pct' => 89.00, 'blokir' => 0],
            ['no' => 48, 'kode_satker' => '452105', 'nama_satker' => 'DINAS TENAGA KERJA DAN MOBILITAS PENDUDUK ACEH (TP)', 'kl' => 'KEMENTERIAN KETENAGAKERJAAN', 'wilayah' => 'ACEH', 'kode_jenis' => '27', 'jenis' => 'Padat Karya', 'kegiatan' => '[3915] Padat Karya Infrastruktur Desa & Perluasan Kesempatan Kerja', 'program' => '[DK] Program Pelatihan Vokasi dan Produktivitas', 'pagu' => 8806220000, 'realisasi' => 6995476700, 'pct' => 79.44, 'blokir' => 0],
            ['no' => 49, 'kode_satker' => '693754', 'nama_satker' => 'DINAS PEKERJAAN UMUM DAN PENATAAN RUANG ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_jenis' => '27', 'jenis' => 'Padat Karya', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 343780000, 'realisasi' => 343773300, 'pct' => 100.00, 'blokir' => 0],
            ['no' => 50, 'kode_satker' => '691282', 'nama_satker' => 'PELAKSANAAN PRASARANA STRATEGIS ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '04', 'jenis' => 'Sekolah Rakyat', 'kegiatan' => '[7717] Penyediaan dan Peningkatan Kualitas Sarana Prasarana Pendidikan', 'program' => '[GM] Program Prasarana Strategis', 'pagu' => 1302518068000, 'realisasi' => 1081812812890, 'pct' => 83.06, 'blokir' => 136891000],
            ['no' => 51, 'kode_satker' => '691434', 'nama_satker' => 'Perumahan dan Kawasan Permukiman Provinsi Aceh', 'kl' => 'KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '11', 'jenis' => 'Pembangunan 3 juta rumah', 'kegiatan' => '[7659] Penyelenggaraan Infrastruktur Kawasan Permukiman', 'program' => '[IA] Program Perumahan dan Kawasan Permukiman', 'pagu' => 1756939251000, 'realisasi' => 325044008621, 'pct' => 18.50, 'blokir' => 0],
            ['no' => 52, 'kode_satker' => '691542', 'nama_satker' => 'Perumahan dan Kawasan Permukiman Provinsi Aceh', 'kl' => 'KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN', 'wilayah' => 'KOTA BANDA ACEH', 'kode_jenis' => '11', 'jenis' => 'Pembangunan 3 juta rumah', 'kegiatan' => '[7661] Penyelenggaraan Perumahan Perkotaan', 'program' => '[IA] Program Perumahan dan Kawasan Permukiman', 'pagu' => 59807730000, 'realisasi' => 58295804468, 'pct' => 97.47, 'blokir' => 0],
        ];
    }

    public function getBencanaSummary(): array
    {
        return [
            'total_pagu' => 1346348051000,
            'total_pagu_formatted' => 'Rp 1.346.348.051.000',
            'total_pagu_short' => '1,3 T',
            'total_realisasi' => 783174209061,
            'total_realisasi_formatted' => 'Rp 783.174.209.061',
            'sisa_pagu' => 563173841939,
            'sisa_pagu_formatted' => 'Rp 563.173.841.939',
            'sisa_pagu_short' => 'Rp 563,17 M',
            'persen_realisasi' => 58.17,
            'persen_sisa' => 41.83,
            'total_satker' => 6,
            'total_output' => 19,
            'total_rows' => 22,
            // Layer 2: RKA-K/L Tagging Bencana
            'rkkl_satker' => 47,
            'rkkl_output' => 46,
            'rkkl_pagu_short' => '10,6 T',
        ];
    }

    public function getBencanaOutputs(): array
    {
        return [
            ['kode' => 'BHC-1', 'tag' => 'BHC', 'nama' => '[BHC] Operasi Bidang Penanggulangan Bencana', 'label' => 'Operasi Penanggulangan Bencana', 'pagu' => 520450000000, 'realisasi' => 426769000000, 'persen' => 82.00],
            ['kode' => 'CBH', 'tag' => 'CBH', 'nama' => '[CBH] Prasarana Bidang Pengendalian Bencana', 'label' => 'Prasarana Pengendalian Bencana', 'pagu' => 468200000000, 'realisasi' => 98322000000, 'persen' => 21.00],
            ['kode' => 'RDG', 'tag' => 'RDG', 'nama' => '[RDG] OP Prasarana Bidang SDA dan Irigasi', 'label' => 'OP Prasarana SDA & Irigasi (RDG)', 'pagu' => 242500000000, 'realisasi' => 181875000000, 'persen' => 75.00],
            ['kode' => 'CDR', 'tag' => 'CDR', 'nama' => '[CDR] OM Prasarana Jaringan Sumber Daya Air', 'label' => 'OM Prasarana Jaringan SDA', 'pagu' => 58472465000, 'realisasi' => 35956510794, 'persen' => 61.49],
            ['kode' => 'CDG', 'tag' => 'CDG', 'nama' => '[CDG] OP Prasarana Bidang SDA dan Irigasi', 'label' => 'OP Prasarana SDA & Irigasi (CDG)', 'pagu' => 28600000000, 'realisasi' => 18590000000, 'persen' => 65.00],
            ['kode' => 'AEA', 'tag' => 'AEA', 'nama' => '[AEA] Koordinasi', 'label' => 'Koordinasi Penanganan Bencana', 'pagu' => 4850000000, 'realisasi' => 4268000000, 'persen' => 88.00],
            ['kode' => 'RCM', 'tag' => 'RCM', 'nama' => '[RCM] OM Sarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'label' => 'OM Sarana SAR & Bencana', 'pagu' => 4079494000, 'realisasi' => 3278739856, 'persen' => 80.37],
            ['kode' => 'CDH', 'tag' => 'CDH', 'nama' => '[CDH] OM Prasarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'label' => 'OM Prasarana SAR & Bencana', 'pagu' => 3420000000, 'realisasi' => 2223000000, 'persen' => 65.00],
            ['kode' => 'QHC', 'tag' => 'QHC', 'nama' => '[QHC] Operasi Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'label' => 'Operasi SAR & Bencana', 'pagu' => 2039360000, 'realisasi' => 1417129900, 'persen' => 69.49],
            ['kode' => 'BKA', 'tag' => 'BKA', 'nama' => '[BKA] Pemantauan masyarakat dan kelompok masyarakat', 'label' => 'Pemantauan Masyarakat', 'pagu' => 1850000000, 'realisasi' => 943500000, 'persen' => 51.00],
            ['kode' => 'CBR', 'tag' => 'CBR', 'nama' => '[CBR] Dukungan Teknis', 'label' => 'Dukungan Teknis Darurat', 'pagu' => 1753303000, 'realisasi' => 1493486380, 'persen' => 85.18],
            ['kode' => 'BHC-2', 'tag' => 'BHC', 'nama' => '[BHC] Operasi Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'label' => 'Operasi SAR & Cipta Karya', 'pagu' => 1293289000, 'realisasi' => 1170503861, 'persen' => 90.51],
            ['kode' => 'DCK', 'tag' => 'DCK', 'nama' => '[DCK] Pelatihan Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'label' => 'Pelatihan Tenaga SAR', 'pagu' => 1077440000, 'realisasi' => 906606800, 'persen' => 84.14],
            ['kode' => 'BHD', 'tag' => 'BHD', 'nama' => '[BHD] Operasi Pengawasan Sumber Daya Alam', 'label' => 'Operasi Pengawasan SDA', 'pagu' => 1180000000, 'realisasi' => 944000000, 'persen' => 80.00],
            ['kode' => 'BAH', 'tag' => 'BAH', 'nama' => '[BAH] Pelayanan Publik Lainnya', 'label' => 'Pelayanan Publik Darurat', 'pagu' => 835644000, 'realisasi' => 767513500, 'persen' => 91.85],
            ['kode' => 'CCM', 'tag' => 'CCM', 'nama' => '[CCM] OP Sarana Bidang Pencarian dan Penanggulangan Bencana', 'label' => 'OP Sarana SAR', 'pagu' => 540000000, 'realisasi' => 486000000, 'persen' => 90.00],
            ['kode' => 'RCL-1', 'tag' => 'RCL', 'nama' => '[RCL] OM Sarana Bidang Teknologi Informasi dan Komunikasi', 'label' => 'OM Sarana TIK SAR', 'pagu' => 165000000, 'realisasi' => 141900000, 'persen' => 86.00],
            ['kode' => 'RDH', 'tag' => 'RDH', 'nama' => '[RDH] OM Prasarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'label' => 'OM Prasarana Pos SAR', 'pagu' => 60000000, 'realisasi' => 60000000, 'persen' => 100.00],
            ['kode' => 'RCL-2', 'tag' => 'RCL', 'nama' => '[RCL] OP Sarana Bidang Teknologi Informasi dan Komunikasi', 'label' => 'OP Sarana TIK Darurat', 'pagu' => 4982056000, 'realisasi' => 3561317970, 'persen' => 71.48],
        ];
    }

    public function getBencanaData(): array
    {
        return [
            ['no' => 1, 'kode_satker' => '614794', 'nama_satker' => 'KANTOR PENCARIAN DAN PERTOLONGAN BANDA ACEH', 'kl' => 'BADAN SAR NASIONAL', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'RCM', 'kegiatan' => '[3946] Pengelolaan Sarana dan Prasarana SAR', 'output' => '[RCM] OM Sarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'program' => '[BM] Program Pencarian dan Pertolongan pada Kecelakaan dan Bencana', 'pagu' => 4079494000, 'realisasi' => 3278739856, 'pct' => 80.37],
            ['no' => 2, 'kode_satker' => '614794', 'nama_satker' => 'KANTOR PENCARIAN DAN PERTOLONGAN BANDA ACEH', 'kl' => 'BADAN SAR NASIONAL', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'RDH', 'kegiatan' => '[3946] Pengelolaan Sarana dan Prasarana SAR', 'output' => '[RDH] OM Prasarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'program' => '[BM] Program Pencarian dan Pertolongan pada Kecelakaan dan Bencana', 'pagu' => 60000000, 'realisasi' => 60000000, 'pct' => 100.00],
            ['no' => 3, 'kode_satker' => '614794', 'nama_satker' => 'KANTOR PENCARIAN DAN PERTOLONGAN BANDA ACEH', 'kl' => 'BADAN SAR NASIONAL', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'DCK', 'kegiatan' => '[3947] Pembinaan Tenaga SAR', 'output' => '[DCK] Pelatihan Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'program' => '[BM] Program Pencarian dan Pertolongan pada Kecelakaan dan Bencana', 'pagu' => 1077440000, 'realisasi' => 906606800, 'pct' => 84.14],
            ['no' => 4, 'kode_satker' => '614794', 'nama_satker' => 'KANTOR PENCARIAN DAN PERTOLONGAN BANDA ACEH', 'kl' => 'BADAN SAR NASIONAL', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'QHC', 'kegiatan' => '[3948] Pengelolaan Operasi SAR', 'output' => '[QHC] Operasi Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'program' => '[BM] Program Pencarian dan Pertolongan pada Kecelakaan dan Bencana', 'pagu' => 2039360000, 'realisasi' => 1417129900, 'pct' => 69.49],
            ['no' => 5, 'kode_satker' => '614794', 'nama_satker' => 'KANTOR PENCARIAN DAN PERTOLONGAN BANDA ACEH', 'kl' => 'BADAN SAR NASIONAL', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'RCL', 'kegiatan' => '[3949] Pengelolaan Sistem Komunikasi SAR', 'output' => '[RCL] OM Sarana Bidang Teknologi Informasi dan Komunikasi', 'program' => '[BM] Program Pencarian dan Pertolongan pada Kecelakaan dan Bencana', 'pagu' => 165000000, 'realisasi' => 141900000, 'pct' => 86.00],
            ['no' => 6, 'kode_satker' => '614794', 'nama_satker' => 'KANTOR PENCARIAN DAN PERTOLONGAN BANDA ACEH', 'kl' => 'BADAN SAR NASIONAL', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'CCM', 'kegiatan' => '[3946] Pengelolaan Sarana dan Prasarana SAR', 'output' => '[CCM] OP Sarana Bidang Pencarian dan Penanggulangan Bencana', 'program' => '[BM] Program Pencarian dan Pertolongan pada Kecelakaan dan Bencana', 'pagu' => 540000000, 'realisasi' => 486000000, 'pct' => 90.00],
            ['no' => 7, 'kode_satker' => '614794', 'nama_satker' => 'KANTOR PENCARIAN DAN PERTOLONGAN BANDA ACEH', 'kl' => 'BADAN SAR NASIONAL', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'CDH', 'kegiatan' => '[3946] Pengelolaan Sarana dan Prasarana SAR', 'output' => '[CDH] OM Prasarana Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'program' => '[BM] Program Pencarian dan Pertolongan pada Kecelakaan dan Bencana', 'pagu' => 3420000000, 'realisasi' => 2223000000, 'pct' => 65.00],
            ['no' => 8, 'kode_satker' => '694269', 'nama_satker' => 'BALAI WILAYAH SUNGAI SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'BHC', 'kegiatan' => '[7695] Operasi dan Pemeliharaan Sarana Prasarana SDA serta Penanggulangan Darurat Akibat Bencana', 'output' => '[BHC] Operasi Bidang Penanggulangan Bencana', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 342800000000, 'realisasi' => 287952000000, 'pct' => 84.00],
            ['no' => 9, 'kode_satker' => '694269', 'nama_satker' => 'BALAI WILAYAH SUNGAI SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'CBH', 'kegiatan' => '[7694] Pembangunan Pengendali Banjir, Lahar, dan Pengaman Pantai', 'output' => '[CBH] Prasarana Bidang Pengendalian Bencana', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 318500000000, 'realisasi' => 66885000000, 'pct' => 21.00],
            ['no' => 10, 'kode_satker' => '694269', 'nama_satker' => 'BALAI WILAYAH SUNGAI SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'RDG', 'kegiatan' => '[7695] Operasi dan Pemeliharaan Sarana Prasarana SDA serta Penanggulangan Darurat Akibat Bencana', 'output' => '[RDG] OP Prasarana Bidang SDA dan Irigasi', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 185400000000, 'realisasi' => 140904000000, 'pct' => 76.00],
            ['no' => 11, 'kode_satker' => '694269', 'nama_satker' => 'BALAI WILAYAH SUNGAI SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'CDG', 'kegiatan' => '[7695] Operasi dan Pemeliharaan Sarana Prasarana SDA serta Penanggulangan Darurat Akibat Bencana', 'output' => '[CDG] OP Prasarana Bidang SDA dan Irigasi', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 28600000000, 'realisasi' => 18590000000, 'pct' => 65.00],
            ['no' => 12, 'kode_satker' => '694081', 'nama_satker' => 'DINAS PENGAIRAN PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_out' => 'RDG', 'kegiatan' => '[7695] Operasi dan Pemeliharaan Sarana Prasarana SDA serta Penanggulangan Darurat Akibat Bencana', 'output' => '[RDG] OP Prasarana Bidang SDA dan Irigasi', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 57100000000, 'realisasi' => 40971000000, 'pct' => 71.75],
            ['no' => 13, 'kode_satker' => '694081', 'nama_satker' => 'DINAS PENGAIRAN PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_out' => 'CDR', 'kegiatan' => '[7695] Operasi dan Pemeliharaan Sarana Prasarana SDA serta Penanggulangan Darurat Akibat Bencana', 'output' => '[CDR] OM Prasarana Jaringan Sumber Daya Air', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 49000000000, 'realisasi' => 26500000000, 'pct' => 54.08],
            ['no' => 14, 'kode_satker' => '498551', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH I PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'BHC', 'kegiatan' => '[2408] Penanganan Darurat Longsoran & Putusnya Ruas Jalan Nasional', 'output' => '[BHC] Operasi Bidang Penanggulangan Bencana', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 177650000000, 'realisasi' => 138817000000, 'pct' => 78.14],
            ['no' => 15, 'kode_satker' => '498551', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH I PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'CBH', 'kegiatan' => '[2408] Pembangunan Dinding Penahan Tebing & Pengaman Lereng Geurutee', 'output' => '[CBH] Prasarana Bidang Pengendalian Bencana', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 149700000000, 'realisasi' => 31437000000, 'pct' => 21.00],
            ['no' => 16, 'kode_satker' => '690510', 'nama_satker' => 'DINAS SOSIAL PROVINSI ACEH (DEKON PENANGGULANGAN BENCANA)', 'kl' => 'KEMENTERIAN SOSIAL', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'AEA', 'kegiatan' => '[5812] Perlindungan Sosial Korban Bencana Alam & Logistik Buffer Stock', 'output' => '[AEA] Koordinasi', 'program' => '[DK] Program Perlindungan Sosial', 'pagu' => 4850000000, 'realisasi' => 4268000000, 'pct' => 88.00],
            ['no' => 17, 'kode_satker' => '690510', 'nama_satker' => 'DINAS SOSIAL PROVINSI ACEH (DEKON PENANGGULANGAN BENCANA)', 'kl' => 'KEMENTERIAN SOSIAL', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'BKA', 'kegiatan' => '[5812] Pemberdayaan Kampung Siaga Bencana (KSB) & TAGANA Aceh', 'output' => '[BKA] Pemantauan masyarakat dan kelompok masyarakat', 'program' => '[DK] Program Perlindungan Sosial', 'pagu' => 1850000000, 'realisasi' => 943500000, 'pct' => 51.00],
            ['no' => 18, 'kode_satker' => '694269', 'nama_satker' => 'BALAI WILAYAH SUNGAI SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'BHD', 'kegiatan' => '[7695] Operasi dan Pemeliharaan Sarana Prasarana SDA serta Penanggulangan Darurat Akibat Bencana', 'output' => '[BHD] Operasi Pengawasan Sumber Daya Alam', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 7362056000, 'realisasi' => 5505317970, 'pct' => 74.78],
            ['no' => 19, 'kode_satker' => '694269', 'nama_satker' => 'BALAI WILAYAH SUNGAI SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'BAH', 'kegiatan' => '[7695] Operasi dan Pemeliharaan Sarana Prasarana SDA serta Penanggulangan Darurat Akibat Bencana', 'output' => '[BAH] Pelayanan Publik Lainnya', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 835644000, 'realisasi' => 767513500, 'pct' => 91.85],
            ['no' => 20, 'kode_satker' => '694269', 'nama_satker' => 'BALAI WILAYAH SUNGAI SUMATERA I', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'CBR', 'kegiatan' => '[7695] Operasi dan Pemeliharaan Sarana Prasarana SDA serta Penanggulangan Darurat Akibat Bencana', 'output' => '[CBR] Dukungan Teknis', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 553303000, 'realisasi' => 493486380, 'pct' => 89.19],
            ['no' => 21, 'kode_satker' => '694081', 'nama_satker' => 'DINAS PENGAIRAN PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_out' => 'CDR', 'kegiatan' => '[7695] Operasi dan Pemeliharaan Sarana Prasarana SDA serta Penanggulangan Darurat Akibat Bencana', 'output' => '[CDR] OM Prasarana Jaringan Sumber Daya Air', 'program' => '[FC] Program Ketahanan Sumber Daya Air', 'pagu' => 9472465000, 'realisasi' => 9456510794, 'pct' => 99.83],
            ['no' => 22, 'kode_satker' => '694003', 'nama_satker' => 'BALAI PENATAAN BANGUNAN, PRASARANA DAN KAWASAN ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'BHC', 'kegiatan' => '[7716] Penyelenggaraan Pembinaan Infrastruktur Cipta Karya', 'output' => '[BHC] Operasi Bidang Pencarian, Pertolongan, dan Penanganan Bencana', 'program' => '[IA] Program Perumahan dan Kawasan Permukiman', 'pagu' => 1293289000, 'realisasi' => 1170503861, 'pct' => 90.51],
        ];
    }

    public function getBencanaRkklOutputs(): array
    {
        return [
            ['nama' => 'Prasarana Bidang Perumahan dan Pemukiman', 'pagu' => 2680000000000, 'satker_count' => 6],
            ['nama' => 'OM Prasarana Bidang Konektivitas Darat (Jalan)', 'pagu' => 2150000000000, 'satker_count' => 5],
            ['nama' => 'OM Prasarana Bidang Konektivitas Darat (Jembatan)', 'pagu' => 1420000000000, 'satker_count' => 4],
            ['nama' => 'Prasarana Bidang Pendidikan Dasar dan Menengah', 'pagu' => 980000000000, 'satker_count' => 8],
            ['nama' => 'Operasi Bidang Penanggulangan Bencana', 'pagu' => 845000000000, 'satker_count' => 5],
            ['nama' => 'Prasarana Bidang Pengendalian Bencana', 'pagu' => 760000000000, 'satker_count' => 4],
            ['nama' => 'OP Prasarana Bidang SDA dan Irigasi', 'pagu' => 610000000000, 'satker_count' => 4],
            ['nama' => 'Prasarana Bidang Kesehatan', 'pagu' => 420000000000, 'satker_count' => 3],
            ['nama' => 'Prasarana Pengembangan Kawasan', 'pagu' => 340000000000, 'satker_count' => 3],
            ['nama' => 'Pemantauan dan Evaluasi serta Pelaporan', 'pagu' => 210000000000, 'satker_count' => 3],
            ['nama' => 'Prasarana Jaringan Sumber Daya Air', 'pagu' => 185000000000, 'satker_count' => 2],
        ];
    }

    public function getBencanaRkklItems(): array
    {
        return [
            ['no' => 1, 'kode_satker' => '019649', 'nama_satker' => 'BADAN PUSAT STATISTIK PROVINSI ACEH', 'kegiatan' => 'Penyediaan dan Pengembangan Statistik Ketahanan Sosial', 'output' => 'Data dan Informasi Publik', 'ro' => 'PUBLIKASI/LAPORAN DATA TUNGGAL BENCANA', 'komponen' => 'Persiapan', 'pagu' => 142500000],
            ['no' => 2, 'kode_satker' => '019649', 'nama_satker' => 'BADAN PUSAT STATISTIK PROVINSI ACEH', 'kegiatan' => 'Penyediaan dan Pengembangan Statistik Ketahanan Sosial', 'output' => 'Data dan Informasi Publik', 'ro' => 'PUBLIKASI/LAPORAN DATA TUNGGAL BENCANA', 'komponen' => 'Pengumpulan Data', 'pagu' => 385400000],
            ['no' => 3, 'kode_satker' => '019660', 'nama_satker' => 'BADAN PUSAT STATISTIK KAB. ACEH BESAR', 'kegiatan' => 'Penyediaan dan Pengembangan Statistik Ketahanan Sosial', 'output' => 'Data dan Informasi Publik', 'ro' => 'PUBLIKASI/LAPORAN DATA TUNGGAL BENCANA', 'komponen' => 'Persiapan', 'pagu' => 68200000],
            ['no' => 4, 'kode_satker' => '019660', 'nama_satker' => 'BADAN PUSAT STATISTIK KAB. ACEH BESAR', 'kegiatan' => 'Penyediaan dan Pengembangan Statistik Ketahanan Sosial', 'output' => 'Data dan Informasi Publik', 'ro' => 'PUBLIKASI/LAPORAN DATA TUNGGAL BENCANA', 'komponen' => 'Pengumpulan Data', 'pagu' => 194800000],
            ['no' => 5, 'kode_satker' => '019672', 'nama_satker' => 'BADAN PUSAT STATISTIK KAB. PIDIE', 'kegiatan' => 'Penyediaan dan Pengembangan Statistik Ketahanan Sosial', 'output' => 'Data dan Informasi Publik', 'ro' => 'PUBLIKASI/LAPORAN DATA TUNGGAL BENCANA', 'komponen' => 'Persiapan & Pengolahan Data', 'pagu' => 215600000],
            ['no' => 6, 'kode_satker' => '019700', 'nama_satker' => 'BADAN PUSAT STATISTIK KOTA SABANG', 'kegiatan' => 'Penyediaan dan Pengembangan Statistik Ketahanan Sosial', 'output' => 'Data dan Informasi Publik', 'ro' => 'PUBLIKASI/LAPORAN DATA TUNGGAL BENCANA', 'komponen' => 'Pengumpulan Data Wilayah Pesisir', 'pagu' => 112400000],
            ['no' => 7, 'kode_satker' => '691434', 'nama_satker' => 'PERUMAHAN DAN KAWASAN PERMUKIMAN PROVINSI ACEH', 'kegiatan' => 'Penyelenggaraan Infrastruktur Kawasan Permukiman', 'output' => 'Prasarana Bidang Perumahan dan Pemukiman', 'ro' => 'HUNIAN TETAP (HUNTAP) PASCA BENCANA ACEH', 'komponen' => 'Konstruksi Fisik & Prasarana Dasar', 'pagu' => 1420000000000],
            ['no' => 8, 'kode_satker' => '691488', 'nama_satker' => 'PERUMAHAN DAN KAWASAN PERMUKIMAN PROVINSI ACEH', 'kegiatan' => 'Penyelenggaraan Perumahan Perdesaan', 'output' => 'Prasarana Bidang Perumahan dan Pemukiman', 'ro' => 'REHABILITASI RUMAH TERDAMPAK BANJIR & LONGSOR', 'komponen' => 'Bantuan Stimulan & Pendampingan Teknis', 'pagu' => 1260000000000],
            ['no' => 9, 'kode_satker' => '498551', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH I PROVINSI ACEH', 'kegiatan' => 'Penyelenggaraan Jalan Nasional Wilayah Aceh', 'output' => 'OM Prasarana Bidang Konektivitas Darat (Jalan)', 'ro' => 'PENANGANAN DARURAT LONGSORAN RUAS GEURUTEE', 'komponen' => 'Mobilisasi Alat Berat & Perkuatan Lereng', 'pagu' => 1280000000000],
            ['no' => 10, 'kode_satker' => '498552', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH II PROVINSI ACEH', 'kegiatan' => 'Penyelenggaraan Jalan Nasional Wilayah Aceh', 'output' => 'OM Prasarana Bidang Konektivitas Darat (Jalan)', 'ro' => 'REKONSTRUKSI BADAN JALAN LINTAS BARAT ACEH', 'komponen' => 'Konstruksi Jalan & Drainase Darurat', 'pagu' => 870000000000],
            ['no' => 11, 'kode_satker' => '498553', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH III PROVINSI ACEH', 'kegiatan' => 'Penyelenggaraan Jembatan Nasional Aceh', 'output' => 'OM Prasarana Bidang Konektivitas Darat (Jembatan)', 'ro' => 'PENGGANTIAN JEMBATAN BAILEY & OPRIT PASCA BANJIR', 'komponen' => 'Pemasangan Rangka Baja & Pengamanan Pilar', 'pagu' => 1420000000000],
            ['no' => 12, 'kode_satker' => '694269', 'nama_satker' => 'BALAI WILAYAH SUNGAI SUMATERA I', 'kegiatan' => 'Pembangunan Pengendali Banjir, Lahar, dan Pengaman Pantai', 'output' => 'Prasarana Bidang Pengendalian Bencana', 'ro' => 'PENGAMANAN TANGGUL BANJIR KRUENG ACEH & TEUNOM', 'komponen' => 'Pembangunan Tanggul & Normalisasi Alur Sungai', 'pagu' => 760000000000],
            ['no' => 13, 'kode_satker' => '694081', 'nama_satker' => 'DINAS PENGAIRAN PROVINSI ACEH', 'kegiatan' => 'Operasi & Pemeliharaan Sarana Prasarana SDA Darurat', 'output' => 'OP Prasarana Bidang SDA dan Irigasi', 'ro' => 'PERBAIKAN BENDUNG & SALURAN IRIGASI TERDAMPAK', 'komponen' => 'Pengerukan Sedimen & Perbaikan Pintu Air', 'pagu' => 610000000000],
            ['no' => 14, 'kode_satker' => '418012', 'nama_satker' => 'KANWIL KEMENTERIAN AGAMA PROVINSI ACEH', 'kegiatan' => 'Pengelolaan dan Pembinaan Pendidikan Madrasah', 'output' => 'Prasarana Bidang Pendidikan Dasar dan Menengah', 'ro' => 'REHABILITASI MADRASAH TERDAMPAK BENCANA ALAM', 'komponen' => 'Perbaikan Ruang Kelas & Meubelair Darurat', 'pagu' => 540000000000],
            ['no' => 15, 'kode_satker' => '419501', 'nama_satker' => 'BALAI PENJAMINAN MUTU PENDIDIKAN (BPMP) ACEH', 'kegiatan' => 'Penjaminan Mutu & Sarpras Pendidikan Dasar Menengah', 'output' => 'Prasarana Bidang Pendidikan Dasar dan Menengah', 'ro' => 'SEKOLAH AMAN BENCANA & REHABILITASI SD/SMP/SMA', 'komponen' => 'Konstruksi Tahan Gempa & Peralatan Belajar', 'pagu' => 440000000000],
            ['no' => 16, 'kode_satker' => '415901', 'nama_satker' => 'RSUD DR. ZAINOEL ABIDIN / DEKON KESEHATAN ACEH', 'kegiatan' => 'Pengelolaan Krisis Kesehatan & Tanggap Darurat Medis', 'output' => 'Prasarana Bidang Kesehatan', 'ro' => 'PENYEDIAAN OBAT DARURAT, AMBULANS & TENDA MEDIS', 'komponen' => 'Pengadaan Alkes Darurat & Operasional PSC 119', 'pagu' => 420000000000],
            ['no' => 17, 'kode_satker' => '614794', 'nama_satker' => 'KANTOR PENCARIAN DAN PERTOLONGAN BANDA ACEH', 'kegiatan' => 'Pengelolaan Operasi & Sarana Prasarana SAR', 'output' => 'Operasi Bidang Penanggulangan Bencana', 'ro' => 'SIAGA SAR KHUSUS & EVAKUASI KEDARURATAN ACEH', 'komponen' => 'Operasional Kapal KN SAR, Perahu Karet & Helipad', 'pagu' => 845000000000],
            ['no' => 18, 'kode_satker' => '694003', 'nama_satker' => 'BALAI PENATAAN BANGUNAN, PRASARANA DAN KAWASAN ACEH', 'kegiatan' => 'Penyelenggaraan Pembinaan Infrastruktur Cipta Karya', 'output' => 'Prasarana Pengembangan Kawasan', 'ro' => 'PENYEDIAAN AIR BERSIH & SANITASI DARURAT PENGUNGSI', 'komponen' => 'Mobil Tangki Air, Hidran Umum & WC Portable', 'pagu' => 340000000000],
        ];
    }

    public function getJalanSummary(): array
    {
        $real = app(\App\Services\SintesaImportService::class)->getSummary('jalan_summary');
        if (!empty($real) && config('app.sintesa_real_data', true)) {
            return $real;
        }
        return [
            'total_pagu' => 5790873002000,
            'total_pagu_formatted' => 'Rp 5.790.873.002.000',
            'total_pagu_short' => '5,8 T',
            'total_realisasi' => 3443083844208,
            'total_realisasi_formatted' => 'Rp 3.443.083.844.208',
            'sisa_pagu' => 2347789157792,
            'sisa_pagu_formatted' => 'Rp 2.347.789.157.792',
            'sisa_pagu_short' => 'Rp 2,35 T',
            'persen_realisasi' => 59.46,
            'persen_sisa' => 40.54,
            'total_satker' => 6,
            'total_output' => 9,
            'total_rows' => 26,
        ];
    }

    public function getJalanOutputs(): array
    {
        $real = app(\App\Services\SintesaImportService::class)->getSummary('jalan_outputs');
        if (!empty($real) && config('app.sintesa_real_data', true)) {
            return $real;
        }
        return [
            ['kode' => 'CDC', 'nama' => '[CDC] OM Prasarana Bidang Konektivitas Darat (Jalan)', 'label' => 'OM Konektivitas Darat (Jalan)', 'pagu' => 3052400000000, 'realisasi' => 1921623200000, 'persen' => 62.95, 'row_count' => 5],
            ['kode' => 'CDF', 'nama' => '[CDF] OM Prasarana Bidang Konektivitas Darat (Jembatan)', 'label' => 'OM Konektivitas Darat (Jembatan)', 'pagu' => 1948500000000, 'realisasi' => 915795000000, 'persen' => 47.00, 'row_count' => 5],
            ['kode' => 'RDC', 'nama' => '[RDC] OM Prasarana Bidang Konektivitas Darat (Jalan)', 'label' => 'OM Jalan Wilayah Strategis (RDC)', 'pagu' => 356800000000, 'realisasi' => 278304000000, 'persen' => 78.00, 'row_count' => 3],
            ['kode' => 'RBC', 'nama' => '[RBC] Prasarana Bidang Konektivitas Darat (Jalan)', 'label' => 'Pembangunan Jalan Nasional (RBC)', 'pagu' => 178400000000, 'realisasi' => 156992000000, 'persen' => 88.00, 'row_count' => 3],
            ['kode' => 'CBR', 'nama' => '[CBR] Dukungan Teknis', 'label' => 'Dukungan Teknis Preservasi', 'pagu' => 160107551000, 'realisasi' => 102965798039, 'persen' => 64.31, 'row_count' => 4],
            ['kode' => 'RDF', 'nama' => '[RDF] OP Prasarana Bidang Konektivitas Darat (Jembatan)', 'label' => 'OP Jembatan Nasional (RDF)', 'pagu' => 72500000000, 'realisasi' => 48575000000, 'persen' => 67.00, 'row_count' => 2],
            ['kode' => 'RBF', 'nama' => '[RBF] Prasarana Bidang Konektivitas Darat (Jembatan)', 'label' => 'Pembangunan Jembatan (RBF)', 'pagu' => 17639849000, 'realisasi' => 16002648169, 'persen' => 90.72, 'row_count' => 2],
            ['kode' => 'CBC', 'nama' => '[CBC] Prasarana Bidang Konektivitas Darat (Jalan)', 'label' => 'Rehabilitasi Jalan (CBC)', 'pagu' => 4265602000, 'realisasi' => 2566198000, 'persen' => 60.16, 'row_count' => 1],
            ['kode' => 'CBF', 'nama' => '[CBF] Prasarana Bidang Konektivitas Darat (Jembatan)', 'label' => 'Rehabilitasi Jembatan (CBF)', 'pagu' => 260000000, 'realisasi' => 260000000, 'persen' => 100.00, 'row_count' => 1],
        ];
    }

    public function getJalanData(): array
    {
        $real = app(\App\Services\SintesaImportService::class)->getSummary('jalan_items');
        if (!empty($real) && config('app.sintesa_real_data', true)) {
            return $real;
        }
        return [
            ['no' => 1, 'kode_satker' => '693818', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH III PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_out' => 'CBC', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CBC] Prasarana Bidang Konektivitas Darat (Jalan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 4265602000, 'realisasi' => 2566198000, 'pct' => 60.16],
            ['no' => 2, 'kode_satker' => '693818', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH III PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_out' => 'CBF', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CBF] Prasarana Bidang Konektivitas Darat (Jembatan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 260000000, 'realisasi' => 260000000, 'pct' => 100.00],
            ['no' => 3, 'kode_satker' => '693818', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH III PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_out' => 'CBR', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CBR] Dukungan Teknis', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 5138349000, 'realisasi' => 4880245329, 'pct' => 94.98],
            ['no' => 4, 'kode_satker' => '693818', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH III PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_out' => 'CDC', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CDC] OM Prasarana Bidang Konektivitas Darat (Jalan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 2169396398000, 'realisasi' => 1324602808611, 'pct' => 61.06],
            ['no' => 5, 'kode_satker' => '693818', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH III PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_out' => 'CDF', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CDF] OM Prasarana Bidang Konektivitas Darat (Jembatan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 682400000000, 'realisasi' => 313904000000, 'pct' => 46.00],
            ['no' => 6, 'kode_satker' => '693818', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH III PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_out' => 'RDC', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[RDC] OM Prasarana Bidang Konektivitas Darat (Jalan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 132400000000, 'realisasi' => 103272000000, 'pct' => 78.00],
            ['no' => 7, 'kode_satker' => '693818', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH III PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_out' => 'RDF', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[RDF] OP Prasarana Bidang Konektivitas Darat (Jembatan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 38500000000, 'realisasi' => 26180000000, 'pct' => 68.00],
            ['no' => 8, 'kode_satker' => '693816', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH I PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'CDC', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CDC] OM Prasarana Bidang Konektivitas Darat (Jalan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 412800000000, 'realisasi' => 280704000000, 'pct' => 68.00],
            ['no' => 9, 'kode_satker' => '693816', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH I PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'CDF', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CDF] OM Prasarana Bidang Konektivitas Darat (Jembatan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 542600000000, 'realisasi' => 265874000000, 'pct' => 49.00],
            ['no' => 10, 'kode_satker' => '693816', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH I PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'RDC', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[RDC] OM Prasarana Bidang Konektivitas Darat (Jalan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 118600000000, 'realisasi' => 94880000000, 'pct' => 80.00],
            ['no' => 11, 'kode_satker' => '693816', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH I PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'RBC', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[RBC] Prasarana Bidang Konektivitas Darat (Jalan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 76400000000, 'realisasi' => 68760000000, 'pct' => 90.00],
            ['no' => 12, 'kode_satker' => '693816', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH I PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'RBF', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[RBF] Prasarana Bidang Konektivitas Darat (Jembatan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 9840000000, 'realisasi' => 9052800000, 'pct' => 92.00],
            ['no' => 13, 'kode_satker' => '693817', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH II PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_out' => 'CDC', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CDC] OM Prasarana Bidang Konektivitas Darat (Jalan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 324600000000, 'realisasi' => 210990000000, 'pct' => 65.00],
            ['no' => 14, 'kode_satker' => '693817', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH II PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_out' => 'CDF', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CDF] OM Prasarana Bidang Konektivitas Darat (Jembatan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 448200000000, 'realisasi' => 206172000000, 'pct' => 46.00],
            ['no' => 15, 'kode_satker' => '693817', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH II PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_out' => 'RDC', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[RDC] OM Prasarana Bidang Konektivitas Darat (Jalan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 105800000000, 'realisasi' => 80152000000, 'pct' => 75.76],
            ['no' => 16, 'kode_satker' => '693817', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH II PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_out' => 'RBC', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[RBC] Prasarana Bidang Konektivitas Darat (Jalan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 62500000000, 'realisasi' => 53750000000, 'pct' => 86.00],
            ['no' => 17, 'kode_satker' => '693817', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH II PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_out' => 'RDF', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[RDF] OP Prasarana Bidang Konektivitas Darat (Jembatan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 34000000000, 'realisasi' => 22395000000, 'pct' => 65.87],
            ['no' => 18, 'kode_satker' => '693817', 'nama_satker' => 'PELAKSANAAN JALAN NASIONAL WILAYAH II PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KAB. ACEH BESAR', 'kode_out' => 'RBF', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[RBF] Prasarana Bidang Konektivitas Darat (Jembatan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 7799849000, 'realisasi' => 6949848169, 'pct' => 89.10],
            ['no' => 19, 'kode_satker' => '693753', 'nama_satker' => 'BALAI PELAKSANAAN JALAN NASIONAL ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'CDC', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CDC] OM Prasarana Bidang Konektivitas Darat (Jalan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 127791359000, 'realisasi' => 87514155869, 'pct' => 68.48],
            ['no' => 20, 'kode_satker' => '693753', 'nama_satker' => 'BALAI PELAKSANAAN JALAN NASIONAL ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'CDF', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CDF] OM Prasarana Bidang Konektivitas Darat (Jembatan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 274993070000, 'realisasi' => 129540076700, 'pct' => 47.11],
            ['no' => 21, 'kode_satker' => '693753', 'nama_satker' => 'BALAI PELAKSANAAN JALAN NASIONAL ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'RBC', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[RBC] Prasarana Bidang Konektivitas Darat (Jalan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 39500000000, 'realisasi' => 34482000000, 'pct' => 87.30],
            ['no' => 22, 'kode_satker' => '693753', 'nama_satker' => 'BALAI PELAKSANAAN JALAN NASIONAL ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'CBR', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CBR] Dukungan Teknis', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 8593962000, 'realisasi' => 6609365047, 'pct' => 76.91],
            ['no' => 23, 'kode_satker' => '693838', 'nama_satker' => 'PERENCANAAN DAN PENGAWASAN JALAN NASIONAL PROVINSI ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'KOTA BANDA ACEH', 'kode_out' => 'CBR', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CBR] Dukungan Teknis', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 146333111000, 'realisasi' => 91434059628, 'pct' => 62.48],
            ['no' => 24, 'kode_satker' => '693754', 'nama_satker' => 'DINAS PEKERJAAN UMUM DAN PENATAAN RUANG ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_out' => 'CBR', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CBR] Dukungan Teknis', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 42129000, 'realisasi' => 42128035, 'pct' => 100.00],
            ['no' => 25, 'kode_satker' => '693754', 'nama_satker' => 'DINAS PEKERJAAN UMUM DAN PENATAAN RUANG ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_out' => 'CDC', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CDC] OM Prasarana Bidang Konektivitas Darat (Jalan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 17812243000, 'realisasi' => 17812235520, 'pct' => 100.00],
            ['no' => 26, 'kode_satker' => '693754', 'nama_satker' => 'DINAS PEKERJAAN UMUM DAN PENATAAN RUANG ACEH', 'kl' => 'KEMENTERIAN PEKERJAAN UMUM', 'wilayah' => 'ACEH', 'kode_out' => 'CDF', 'kegiatan' => '[7696] Pembangunan dan Preservasi Jalan Nasional', 'output' => '[CDF] OM Prasarana Bidang Konektivitas Darat (Jembatan)', 'program' => '[GA] Program Infrastruktur Konektivitas', 'pagu' => 306930000, 'realisasi' => 306923300, 'pct' => 100.00],
        ];
    }

    public function getBansosSummary(): array
    {
        $real = app(\App\Services\SintesaImportService::class)->getSummary('bansos_summary');
        if (!empty($real) && config('app.sintesa_real_data', true)) {
            return $real;
        }
        return [
            'total_pagu' => 6243900000,
            'total_realisasi' => 5767624824,
            'total_sisa' => 476275176,
            'persen_realisasi' => 92.37,
            'persen_sisa' => 7.63,
            'total_satker' => 1,
            'total_output' => 2,
            'total_paket' => 6,
            'total_pagu_formatted' => 'Rp 6.243.900.000',
            'total_realisasi_formatted' => 'Rp 5.767.624.824',
            'sisa_pagu_formatted' => 'Rp 476.275.176',
            'total_pagu_short' => '6.2 M',
            'total_realisasi_short' => '5.8 M',
            'sisa_pagu_short' => '476,3 Jt',
            'satker_kode' => '690568',
            'satker_nama' => "SENTRA \"DARUSSA'ADAH\" DI ACEH BESAR",
            'satker_kl' => 'KEMENTERIAN SOSIAL',
            'satker_wilayah' => 'KAB. ACEH BESAR',
            'cut_off' => '19 November 2026',
        ];
    }

    public function getBansosOutputs(): array
    {
        $real = app(\App\Services\SintesaImportService::class)->getSummary('bansos_outputs');
        if (!empty($real) && config('app.sintesa_real_data', true)) {
            return $real;
        }
        return [
            [
                'kode_out' => 'QAA',
                'output' => '[QAA] Pelayanan Publik kepada masyarakat',
                'output_singkat' => 'Pelayanan Publik kepada Masyarakat',
                'pagu' => 5533120000,
                'realisasi' => 5065328224,
                'sisa' => 467791776,
                'pct' => 91.55,
                'jumlah_paket' => 5,
                'paket_names' => 'Disabilitas, Lansia, Anak, Kelompok Rentan, Napza & ODHIV',
                'deskripsi' => 'Layanan rehabilitasi sosial komprehensif, asistensi pengasuhan anak, nutrisi lansia, dan terapi adiksi residensial.',
            ],
            [
                'kode_out' => 'QEG',
                'output' => '[QEG] Bantuan Peralatan / Sarana',
                'output_singkat' => 'Bantuan Peralatan / Sarana',
                'pagu' => 710780000,
                'realisasi' => 702296600,
                'sisa' => 8483400,
                'pct' => 98.81,
                'jumlah_paket' => 1,
                'paket_names' => 'Bantuan Sarana Aksesibilitas & Alat Bantu Disabilitas',
                'deskripsi' => 'Pengadaan kursi roda adaptif, kaki palsu, tongkat pintar, dan alat bantu mobilitas mandiri penyandang disabilitas.',
            ],
        ];
    }

    public function getBansosData(): array
    {
        $real = app(\App\Services\SintesaImportService::class)->getSummary('bansos_items');
        if (!empty($real) && config('app.sintesa_real_data', true)) {
            return $real;
        }
        return [
            [
                'no' => 1,
                'kode_satker' => '690568',
                'nama_satker' => 'SENTRA "DARUSSA\'ADAH" DI ACEH BESAR',
                'kl' => 'KEMENTERIAN SOSIAL',
                'wilayah' => 'KAB. ACEH BESAR',
                'kegiatan_kode' => '6278',
                'kegiatan' => '[6278] Asistensi Rehabilitasi Sosial Penyandang Disabilitas',
                'kegiatan_nama' => 'Asistensi Rehabilitasi Sosial Penyandang Disabilitas',
                'kode_out' => 'QAA',
                'output' => '[QAA] Pelayanan Publik kepada masyarakat',
                'output_nama' => 'Pelayanan Publik kepada masyarakat',
                'program' => '[DQ] Program Perlindungan Sosial',
                'pagu' => 1116000000,
                'realisasi' => 1014095014,
                'sisa' => 101904986,
                'pct' => 90.87,
                'status' => 'On Track',
                'icon' => 'wheelchair',
                'label_singkat' => 'Rehabilitasi Disabilitas',
                'deskripsi' => 'Layanan rehabilitasi vokasi, terapi fisik, home care, dan advokasi sosial bagi penyandang disabilitas fisik maupun sensorik di wilayah Aceh.'
            ],
            [
                'no' => 2,
                'kode_satker' => '690568',
                'nama_satker' => 'SENTRA "DARUSSA\'ADAH" DI ACEH BESAR',
                'kl' => 'KEMENTERIAN SOSIAL',
                'wilayah' => 'KAB. ACEH BESAR',
                'kegiatan_kode' => '6278',
                'kegiatan' => '[6278] Asistensi Rehabilitasi Sosial Penyandang Disabilitas',
                'kegiatan_nama' => 'Asistensi Rehabilitasi Sosial Penyandang Disabilitas',
                'kode_out' => 'QEG',
                'output' => '[QEG] Bantuan Peralatan / Sarana',
                'output_nama' => 'Bantuan Peralatan / Sarana',
                'program' => '[DQ] Program Perlindungan Sosial',
                'pagu' => 710780000,
                'realisasi' => 702296600,
                'sisa' => 8483400,
                'pct' => 98.81,
                'status' => 'Optimal',
                'icon' => 'tools',
                'label_singkat' => 'Sarana & Alat Bantu Disabilitas',
                'deskripsi' => 'Penyaluran sarana aksesibilitas mobilitas: kursi roda adaptif, tongkat pintar disabilitas netra, alat bantu dengar, dan perlengkapan kemandirian usaha.'
            ],
            [
                'no' => 3,
                'kode_satker' => '690568',
                'nama_satker' => 'SENTRA "DARUSSA\'ADAH" DI ACEH BESAR',
                'kl' => 'KEMENTERIAN SOSIAL',
                'wilayah' => 'KAB. ACEH BESAR',
                'kegiatan_kode' => '6280',
                'kegiatan' => '[6280] Asistensi Rehabilitasi Sosial Lanjut Usia',
                'kegiatan_nama' => 'Asistensi Rehabilitasi Sosial Lanjut Usia',
                'kode_out' => 'QAA',
                'output' => '[QAA] Pelayanan Publik kepada masyarakat',
                'output_nama' => 'Pelayanan Publik kepada masyarakat',
                'program' => '[DQ] Program Perlindungan Sosial',
                'pagu' => 1242120000,
                'realisasi' => 1136089500,
                'sisa' => 106030500,
                'pct' => 91.46,
                'status' => 'On Track',
                'icon' => 'heart',
                'label_singkat' => 'Rehabilitasi Lanjut Usia',
                'deskripsi' => 'Bantuan pemenuhan nutrisi dan hidup layak, perawatan keluarga (home care), day care lansia terlantar, serta pemeriksaan kesehatan lansia secara berkala.'
            ],
            [
                'no' => 4,
                'kode_satker' => '690568',
                'nama_satker' => 'SENTRA "DARUSSA\'ADAH" DI ACEH BESAR',
                'kl' => 'KEMENTERIAN SOSIAL',
                'wilayah' => 'KAB. ACEH BESAR',
                'kegiatan_kode' => '6279',
                'kegiatan' => '[6279] Asistensi Rehabilitasi Sosial Anak',
                'kegiatan_nama' => 'Asistensi Rehabilitasi Sosial Anak',
                'kode_out' => 'QAA',
                'output' => '[QAA] Pelayanan Publik kepada masyarakat',
                'output_nama' => 'Pelayanan Publik kepada masyarakat',
                'program' => '[DQ] Program Perlindungan Sosial',
                'pagu' => 1601600000,
                'realisasi' => 1469262040,
                'sisa' => 132337960,
                'pct' => 91.74,
                'status' => 'On Track',
                'icon' => 'child',
                'label_singkat' => 'Rehabilitasi Sosial Anak',
                'deskripsi' => 'Perlindungan anak membutuhkan perlindungan khusus (AMPK), penanganan anak berhadapan dengan hukum, bantuan dukungan pengasuhan anak, dan trauma healing.'
            ],
            [
                'no' => 5,
                'kode_satker' => '690568',
                'nama_satker' => 'SENTRA "DARUSSA\'ADAH" DI ACEH BESAR',
                'kl' => 'KEMENTERIAN SOSIAL',
                'wilayah' => 'KAB. ACEH BESAR',
                'kegiatan_kode' => '6277',
                'kegiatan' => '[6277] Asistensi Rehabilitasi Sosial Kelompok Rentan',
                'kegiatan_nama' => 'Asistensi Rehabilitasi Sosial Kelompok Rentan',
                'kode_out' => 'QAA',
                'output' => '[QAA] Pelayanan Publik kepada masyarakat',
                'output_nama' => 'Pelayanan Publik kepada masyarakat',
                'program' => '[DQ] Program Perlindungan Sosial',
                'pagu' => 731000000,
                'realisasi' => 724281670,
                'sisa' => 6718330,
                'pct' => 99.08,
                'status' => 'Optimal',
                'icon' => 'shield',
                'label_singkat' => 'Rehabilitasi Kelompok Rentan',
                'deskripsi' => 'Bantuan permakanan kebutuhan hidup dasar, sandang, tempat tinggal darurat, dan pendampingan resosialisasi kelompok marjinal / rentan.'
            ],
            [
                'no' => 6,
                'kode_satker' => '690568',
                'nama_satker' => 'SENTRA "DARUSSA\'ADAH" DI ACEH BESAR',
                'kl' => 'KEMENTERIAN SOSIAL',
                'wilayah' => 'KAB. ACEH BESAR',
                'kegiatan_kode' => '7992',
                'kegiatan' => '[7992] Asistensi Rehabilitasi Sosial Korban Penyalahgunaan Napza dan ODHIV',
                'kegiatan_nama' => 'Asistensi Rehabilitasi Sosial Korban Napza & ODHIV',
                'kode_out' => 'QAA',
                'output' => '[QAA] Pelayanan Publik kepada masyarakat',
                'output_nama' => 'Pelayanan Publik kepada masyarakat',
                'program' => '[DQ] Program Perlindungan Sosial',
                'pagu' => 842400000,
                'realisasi' => 721600000,
                'sisa' => 120800000,
                'pct' => 85.66,
                'status' => 'Perhatian',
                'icon' => 'bandage',
                'label_singkat' => 'Korban Napza & ODHIV',
                'deskripsi' => 'Terapi pemulihan sosial medis residensial bagi korban penyalahgunaan zat adiktif, konseling pencegahan kekambuhan, dan asistensi orang dengan HIV (ODHIV).'
            ],
        ];
    }

    public function getTKDSummary(): array
    {
        $realTkd = app(\App\Services\SintesaImportService::class)->getSummary('tkd_bun_detail');
        if (!empty($realTkd['summary']) && config('app.sintesa_real_data', true)) {
            return $realTkd['summary'];
        }

        return [
            'total_pagu' => 12931920218000,
            'total_realisasi' => 12237037761537,
            'total_sisa' => 694882456463,
            'persen_realisasi' => 94.63,
            'persen_sisa' => 5.37,
            'total_satker' => 3,
            'total_jenis_dbh' => 16,
            'total_uraian_otsus' => 3,
            'total_pagu_formatted' => 'Rp 12.931.920.218.000',
            'total_realisasi_formatted' => 'Rp 12.237.037.761.537',
            'sisa_pagu_formatted' => 'Rp 694.882.456.463',
            'total_pagu_short' => '12.93 T',
            'total_realisasi_short' => '12.24 T',
            'sisa_pagu_short' => '694,9 M',
            'cut_off' => '19 November 2026',
        ];
    }

    public function getTKDSatkerBUN(): array
    {
        $realTkd = app(\App\Services\SintesaImportService::class)->getSummary('tkd_bun_detail');
        if (!empty($realTkd['satker_bun']) && config('app.sintesa_real_data', true)) {
            return $realTkd['satker_bun'];
        }

        return [
            [
                'no' => 1,
                'kode' => '500133',
                'nama' => 'KPPN BANDA ACEH PENYALUR DANA TRANSFER UMUM',
                'sub' => 'BAGIAN ANGGARAN BENDAHARA UMUM NEGARA',
                'pagu' => 6573988103000,
                'realisasi' => 6142768247800,
                'sisa' => 431219855200,
                'pct' => 93.44,
                'status' => 'On Track',
                'icon' => 'building-library',
                'keterangan' => 'Penyaluran Dana Alokasi Umum (DAU) dan Dana Bagi Hasil (DBH Pajak & SDA) untuk Pemerintah Provinsi Aceh dan Kab/Kota.'
            ],
            [
                'no' => 2,
                'kode' => '600061',
                'nama' => 'KPPN BANDA ACEH PENYALUR DANA TRANSFER KHUSUS',
                'sub' => 'BAGIAN ANGGARAN BENDAHARA UMUM NEGARA',
                'pagu' => 1853449657000,
                'realisasi' => 1638721472337,
                'sisa' => 214728184663,
                'pct' => 88.41,
                'status' => 'Perhatian',
                'icon' => 'briefcase',
                'keterangan' => 'Penyaluran Dana Alokasi Khusus (DAK) Fisik untuk infrastruktur serta DAK Non-Fisik (BOS, BOSP, BOK Puskesmas).'
            ],
            [
                'no' => 3,
                'kode' => '700254',
                'nama' => 'KPPN BANDA ACEH PENYALUR DANA DESA, INSENTIF FISKAL, OTONOMI KHUSUS DAN KEISTIMEWAAN',
                'sub' => 'BAGIAN ANGGARAN BENDAHARA UMUM NEGARA',
                'pagu' => 4504482458000,
                'realisasi' => 4455548041400,
                'sisa' => 48934416600,
                'pct' => 98.91,
                'status' => 'Optimal',
                'icon' => 'sparkles',
                'keterangan' => 'Penyaluran Dana Desa (Gampong), Dana Alokasi Khusus Aceh (DOKA / Otsus), Insentif Fiskal Daerah, dan Dana Tambahan Otsus.'
            ],
        ];
    }

    public function getTKDOtsusItems(): array
    {
        $realTkd = app(\App\Services\SintesaImportService::class)->getSummary('tkd_bun_detail');
        if (!empty($realTkd['otsus_items']) && config('app.sintesa_real_data', true)) {
            return $realTkd['otsus_items'];
        }

        return [
            [
                'no' => 1,
                'uraian' => 'Belanja Dana Otonomi Khusus Provinsi Aceh',
                'kode_satker' => '700254',
                'nama_satker' => 'KPPN BANDA ACEH PENYALUR DANA DESA, INSENTIF FISKAL, OTONOMI KHUSUS DAN KEISTIMEWAAN',
                'pagu' => 4076166626000,
                'realisasi' => 4030869296000,
                'sisa' => 45297330000,
                'pct' => 98.89,
                'porsi' => 98.5,
                'status' => 'Optimal',
                'keterangan' => 'Dana Otonomi Khusus Aceh (DOKA) untuk pembangunan dan pemeliharaan infrastruktur, pemberdayaan ekonomi rakyat, pengentasan kemiskinan, serta pendidikan dan kesehatan.'
            ],
            [
                'no' => 2,
                'uraian' => 'Belanja Tambahan Dana Bagi Hasil Gas Bumi Dalam Rangka Otonomi Khusus',
                'kode_satker' => '500133',
                'nama_satker' => 'KPPN BANDA ACEH PENYALUR DANA TRANSFER UMUM',
                'pagu' => 31064664000,
                'realisasi' => 27934527450,
                'sisa' => 3130136550,
                'pct' => 89.92,
                'porsi' => 0.8,
                'status' => 'Perhatian',
                'keterangan' => 'Tambahan DBH Gas Alam untuk Aceh sesuai amanat UU No. 11/2006 (UUPA) guna percepatan pembangunan daerah penghasil dan sekitarnya.'
            ],
            [
                'no' => 3,
                'uraian' => 'Belanja Tambahan Dana Bagi Hasil Minyak Bumi Dalam Rangka Otonomi Khusus',
                'kode_satker' => '500133',
                'nama_satker' => 'KPPN BANDA ACEH PENYALUR DANA TRANSFER UMUM',
                'pagu' => 29006980000,
                'realisasi' => 26184601900,
                'sisa' => 2822378100,
                'pct' => 90.27,
                'porsi' => 0.7,
                'status' => 'On Track',
                'keterangan' => 'Tambahan DBH Minyak Bumi khusus untuk Provinsi Aceh dalam skema transfer otonomi khusus.'
            ],
        ];
    }

    public function getTKDDbhItems(): array
    {
        $realTkd = app(\App\Services\SintesaImportService::class)->getSummary('tkd_bun_detail');
        if (!empty($realTkd['dbh_items']) && config('app.sintesa_real_data', true)) {
            return $realTkd['dbh_items'];
        }

        return [
            ['no' => 1, 'uraian' => 'Belanja Dana Bagi Hasil Cukai Hasil Tembakau', 'kategori' => 'Cukai', 'pagu' => 6058901000, 'realisasi' => 5798978150, 'sisa' => 259922850, 'pct' => 95.71, 'status' => 'Optimal'],
            ['no' => 2, 'uraian' => 'Belanja Dana Bagi Hasil Gas Bumi', 'kategori' => 'SDA Migas', 'pagu' => 5478149000, 'realisasi' => 5478149000, 'sisa' => 0, 'pct' => 100.00, 'status' => 'Optimal'],
            ['no' => 3, 'uraian' => 'Belanja Dana Bagi Hasil Kehutanan - Dana Reboisasi', 'kategori' => 'SDA Kehutanan', 'pagu' => 825166000, 'realisasi' => 825166000, 'sisa' => 0, 'pct' => 100.00, 'status' => 'Optimal'],
            ['no' => 4, 'uraian' => 'Belanja Dana Bagi Hasil Kehutanan - PSDH', 'kategori' => 'SDA Kehutanan', 'pagu' => 129281000, 'realisasi' => 117534400, 'sisa' => 11746600, 'pct' => 90.91, 'status' => 'On Track'],
            ['no' => 5, 'uraian' => 'Belanja Dana Bagi Hasil Minyak Bumi', 'kategori' => 'SDA Migas', 'pagu' => 1544482000, 'realisasi' => 1544482000, 'sisa' => 0, 'pct' => 100.00, 'status' => 'Optimal'],
            ['no' => 6, 'uraian' => 'Belanja Dana Bagi Hasil PBB untuk Kabupaten/Kota', 'kategori' => 'Pajak Daerah', 'pagu' => 17710035000, 'realisasi' => 17710035000, 'sisa' => 0, 'pct' => 100.00, 'status' => 'Optimal'],
            ['no' => 7, 'uraian' => 'Belanja Dana Bagi Hasil PBB untuk Propinsi', 'kategori' => 'Pajak Daerah', 'pagu' => 53311099000, 'realisasi' => 53311099000, 'sisa' => 0, 'pct' => 100.00, 'status' => 'Optimal'],
            ['no' => 8, 'uraian' => 'Belanja Dana Bagi Hasil PPh Pasal 21', 'kategori' => 'Pajak Penghasilan', 'pagu' => 164754401000, 'realisasi' => 164754401000, 'sisa' => 0, 'pct' => 100.00, 'status' => 'Optimal'],
            ['no' => 9, 'uraian' => 'Belanja Dana Bagi Hasil PPh Pasal 25/29 OP', 'kategori' => 'Pajak Penghasilan', 'pagu' => 6229904000, 'realisasi' => 6229904000, 'sisa' => 0, 'pct' => 100.00, 'status' => 'Optimal'],
            ['no' => 10, 'uraian' => 'Belanja Dana Bagi Hasil Perikanan', 'kategori' => 'SDA Perikanan', 'pagu' => 2231541000, 'realisasi' => 2231541000, 'sisa' => 0, 'pct' => 100.00, 'status' => 'Optimal'],
            ['no' => 11, 'uraian' => 'Belanja Dana Bagi Hasil Pertambangan Panas Bumi - Iuran Tetap', 'kategori' => 'SDA Minerba', 'pagu' => 753676000, 'realisasi' => 753676000, 'sisa' => 0, 'pct' => 100.00, 'status' => 'Optimal'],
            ['no' => 12, 'uraian' => 'Belanja Dana Bagi Hasil Pertambangan Umum - Iuran Tetap', 'kategori' => 'SDA Minerba', 'pagu' => 4224544000, 'realisasi' => 4224544000, 'sisa' => 0, 'pct' => 100.00, 'status' => 'Optimal'],
            ['no' => 13, 'uraian' => 'Belanja Dana Bagi Hasil Pertambangan Umum - Royalti', 'kategori' => 'SDA Minerba', 'pagu' => 74830315000, 'realisasi' => 74830315000, 'sisa' => 0, 'pct' => 100.00, 'status' => 'Optimal'],
            ['no' => 14, 'uraian' => 'Belanja Tambahan Dana Bagi Hasil Gas Bumi Dalam Rangka Otonomi Khusus', 'kategori' => 'Otsus Migas', 'pagu' => 31064664000, 'realisasi' => 27934527450, 'sisa' => 3130136550, 'pct' => 89.92, 'status' => 'Perhatian'],
            ['no' => 15, 'uraian' => 'Belanja Tambahan Dana Bagi Hasil Minyak Bumi Dalam Rangka Otonomi Khusus', 'kategori' => 'Otsus Migas', 'pagu' => 29006980000, 'realisasi' => 26184601900, 'sisa' => 2822378100, 'pct' => 90.27, 'status' => 'On Track'],
            ['no' => 16, 'uraian' => 'Dana Bagi Hasil Perkebunan Sawit', 'kategori' => 'SDA Perkebunan', 'pagu' => 11097043000, 'realisasi' => 10777043000, 'sisa' => 320000000, 'pct' => 97.12, 'status' => 'Optimal'],
        ];
    }

    public function getTKDDbhDaerah(): array
    {
        return [
            'total' => [
                'kode' => 'TOTAL',
                'nama' => 'TOTAL DBH',
                'pagu' => 409250181000,
                'realisasi' => 402705996900,
                'pct' => 98.40,
                'porsi' => 100.0,
                'color' => '#1e40af',
            ],
            'aceh' => [
                'kode' => 'ACEH',
                'nama' => 'Pemerintah Provinsi Aceh',
                'pagu' => 295878630000,
                'realisasi' => 291156435759,
                'pct' => 98.40,
                'porsi' => 72.3,
                'color' => '#3b82f6',
            ],
            'banda_aceh' => [
                'kode' => 'BANDA_ACEH',
                'nama' => 'Kota Banda Aceh',
                'pagu' => 52793773000,
                'realisasi' => 51949073600,
                'pct' => 98.40,
                'porsi' => 12.9,
                'color' => '#ef4444',
            ],
            'aceh_besar' => [
                'kode' => 'ACEH_BESAR',
                'nama' => 'Kabupaten Aceh Besar',
                'pagu' => 22918220000,
                'realisasi' => 22551535826,
                'pct' => 98.40,
                'porsi' => 5.6,
                'color' => '#10b981',
            ],
            'pidie' => [
                'kode' => 'PIDIE',
                'nama' => 'Kabupaten Pidie',
                'pagu' => 16779418000,
                'realisasi' => 16510945873,
                'pct' => 98.40,
                'porsi' => 4.1,
                'color' => '#f59e0b',
            ],
            'pidie_jaya' => [
                'kode' => 'PIDIE_JAYA',
                'nama' => 'Kabupaten Pidie Jaya',
                'pagu' => 16370150000,
                'realisasi' => 16108239876,
                'pct' => 98.40,
                'porsi' => 4.0,
                'color' => '#8b5cf6',
            ],
            'sabang' => [
                'kode' => 'SABANG',
                'nama' => 'Kota Sabang',
                'pagu' => 4510000000,
                'realisasi' => 4429765966,
                'pct' => 98.22,
                'porsi' => 1.1,
                'color' => '#06b6d4',
            ],
        ];
    }

    public function getTKDOtsusPorsi(): array
    {
        return [
            [
                'label' => 'Belanja Dana Otonomi Khusus Provinsi Aceh',
                'label_singkat' => 'Dana Otsus Aceh (DOKA)',
                'pagu' => 4076166626000,
                'realisasi' => 4030869296000,
                'porsi' => 98.5,
                'color' => '#f59e0b',
            ],
            [
                'label' => 'Belanja Tambahan DBH Gas Bumi (Otsus)',
                'label_singkat' => 'Tambahan DBH Gas Bumi',
                'pagu' => 31064664000,
                'realisasi' => 27934527450,
                'porsi' => 0.8,
                'color' => '#10b981',
            ],
            [
                'label' => 'Belanja Tambahan DBH Minyak Bumi (Otsus)',
                'label_singkat' => 'Tambahan DBH Minyak Bumi',
                'pagu' => 29006980000,
                'realisasi' => 26184601900,
                'porsi' => 0.7,
                'color' => '#3b82f6',
            ],
        ];
    }

    public function getTKDData(): array
    {
        return [
            'total' => ['nama' => 'TOTAL PROVINSI ACEH', 'dbh_alokasi' => 409250181000, 'dbh_realisasi' => 402705996900, 'otsus_alokasi' => 4136238270000, 'otsus_realisasi' => 4084988425350, 'desa_alokasi' => 4560000000000, 'desa_realisasi' => 4420000000000, 'dau_alokasi' => 14850000000000, 'dau_realisasi' => 14120000000000],
            'banda_aceh' => ['nama' => 'KOTA BANDA ACEH', 'dbh_alokasi' => 52793773000, 'dbh_realisasi' => 51949073600, 'otsus_alokasi' => 240000000000, 'otsus_realisasi' => 235000000000, 'desa_alokasi' => 78000000000, 'desa_realisasi' => 78000000000, 'dau_alokasi' => 550000000000, 'dau_realisasi' => 520000000000],
            'aceh_besar' => ['nama' => 'KAB. ACEH BESAR', 'dbh_alokasi' => 22918220000, 'dbh_realisasi' => 22551535826, 'otsus_alokasi' => 320000000000, 'otsus_realisasi' => 310000000000, 'desa_alokasi' => 410000000000, 'desa_realisasi' => 395000000000, 'dau_alokasi' => 850000000000, 'dau_realisasi' => 810000000000],
            'pidie' => ['nama' => 'KAB. PIDIE', 'dbh_alokasi' => 16779418000, 'dbh_realisasi' => 16510945873, 'otsus_alokasi' => 290000000000, 'otsus_realisasi' => 280000000000, 'desa_alokasi' => 512000000000, 'desa_realisasi' => 501000000000, 'dau_alokasi' => 780000000000, 'dau_realisasi' => 740000000000],
            'pidie_jaya' => ['nama' => 'KAB. PIDIE JAYA', 'dbh_alokasi' => 16370150000, 'dbh_realisasi' => 16108239876, 'otsus_alokasi' => 180000000000, 'otsus_realisasi' => 174000000000, 'desa_alokasi' => 189000000000, 'desa_realisasi' => 185000000000, 'dau_alokasi' => 410000000000, 'dau_realisasi' => 392000000000],
            'sabang' => ['nama' => 'KOTA SABANG', 'dbh_alokasi' => 4510000000, 'dbh_realisasi' => 4429765966, 'otsus_alokasi' => 110000000000, 'otsus_realisasi' => 108000000000, 'desa_alokasi' => 21000000000, 'desa_realisasi' => 21000000000, 'dau_alokasi' => 310000000000, 'dau_realisasi' => 298000000000]
        ];
    }
}
