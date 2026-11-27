<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Tampilan Utama Executive Dashboard Kanwil DJPb Provinsi Aceh
     */
    public function index(): View
    {
        $kpi = $this->supabase->getExecutiveKpi();
        $kppnRanking = $this->supabase->getKppnRegionalRanking();
        $satkerKl = $this->supabase->getSatkerKlSummary();
        $topKl = $this->supabase->getTop10Kl();
        $bunData = $this->supabase->getBunRegionalData();
        $jenisBelanjaChart = $this->supabase->getJenisBelanjaChart();
        $regionalMatrix = $this->buildRegionalMatrix();

        return view('dashboard.index', compact(
            'kpi',
            'kppnRanking',
            'satkerKl',
            'topKl',
            'bunData',
            'jenisBelanjaChart',
            'regionalMatrix'
        ));
    }

    /**
     * Susun matriks Pagu, Realisasi & Blokir gabungan (K/L + BA BUN) per 7 KPPN & total se-Aceh.
     */
    private function buildRegionalMatrix(): array
    {
        $paguRows = \Illuminate\Support\Facades\DB::table('sintesa_satker_pagu')
            ->selectRaw('kppn_kode, MAX(kppn_uraian) as nama')
            ->selectRaw("SUM(CASE WHEN kementerian_kode != '999' THEN pagu ELSE 0 END) as pagu_kl")
            ->selectRaw("SUM(CASE WHEN kementerian_kode = '999' THEN pagu ELSE 0 END) as pagu_bun")
            ->selectRaw("SUM(CASE WHEN kementerian_kode != '999' THEN blokir ELSE 0 END) as blokir_kl")
            ->selectRaw("SUM(CASE WHEN kementerian_kode = '999' THEN blokir ELSE 0 END) as blokir_bun")
            ->selectRaw("COUNT(DISTINCT CASE WHEN kementerian_kode != '999' THEN satker_kode END) as cnt_kl")
            ->selectRaw("COUNT(DISTINCT CASE WHEN kementerian_kode = '999' THEN satker_kode END) as cnt_bun")
            ->groupBy('kppn_kode')
            ->get()
            ->keyBy('kppn_kode');

        $realRows = \Illuminate\Support\Facades\DB::table('sintesa_belanja_bulanan')
            ->selectRaw('kppn_kode')
            ->selectRaw("SUM(CASE WHEN kementerian_kode != '999' THEN total_realisasi ELSE 0 END) as real_kl")
            ->selectRaw("SUM(CASE WHEN kementerian_kode = '999' THEN total_realisasi ELSE 0 END) as real_bun")
            ->groupBy('kppn_kode')
            ->get()
            ->keyBy('kppn_kode');

        $jbPaguRows = \Illuminate\Support\Facades\DB::table('sintesa_satker_pagu')
            ->selectRaw("kppn_kode, SUBSTR(akun_kode, 1, 2) as prefix, SUM(pagu) as pagu")
            ->groupBy('kppn_kode', \Illuminate\Support\Facades\DB::raw('SUBSTR(akun_kode, 1, 2)'))
            ->get();
        $jbRealRows = \Illuminate\Support\Facades\DB::table('sintesa_belanja_bulanan')
            ->selectRaw("kppn_kode, SUBSTR(akun_kode, 1, 2) as prefix, SUM(total_realisasi) as realisasi")
            ->groupBy('kppn_kode', \Illuminate\Support\Facades\DB::raw('SUBSTR(akun_kode, 1, 2)'))
            ->get();

        $jbRealMap = [];
        foreach ($jbRealRows as $jr) {
            $jbRealMap[$jr->kppn_kode][$jr->prefix] = (float) $jr->realisasi;
        }

        $jbLabels = [
            '51' => 'Belanja Pegawai (51)',
            '52' => 'Belanja Barang (52)',
            '53' => 'Belanja Modal (53)',
            '57' => 'Belanja Bantuan Sosial (57)',
        ];
        $jbPerKppn = [];
        foreach ($jbPaguRows as $jp) {
            $k = $jp->kppn_kode;
            $pfx = str_starts_with((string) $jp->prefix, '6') ? '6x' : (string) $jp->prefix;
            if (!isset($jbPerKppn[$k][$pfx])) {
                $jbPerKppn[$k][$pfx] = [
                    'kode' => $pfx,
                    'label' => $jbLabels[$pfx] ?? ($pfx === '6x' ? 'Transfer Ke Daerah / TKD (6x)' : 'Belanja Lainnya (' . $pfx . ')'),
                    'pagu' => 0.0,
                    'realisasi' => 0.0,
                ];
            }
            $jbPerKppn[$k][$pfx]['pagu'] += (float) $jp->pagu;
        }
        foreach ($jbRealMap as $k => $pfxMap) {
            foreach ($pfxMap as $origPfx => $val) {
                $pfx = str_starts_with((string) $origPfx, '6') ? '6x' : (string) $origPfx;
                if (!isset($jbPerKppn[$k][$pfx])) {
                    $jbPerKppn[$k][$pfx] = [
                        'kode' => $pfx,
                        'label' => $jbLabels[$pfx] ?? ($pfx === '6x' ? 'Transfer Ke Daerah / TKD (6x)' : 'Belanja Lainnya (' . $pfx . ')'),
                        'pagu' => 0.0,
                        'realisasi' => 0.0,
                    ];
                }
                $jbPerKppn[$k][$pfx]['realisasi'] += $val;
            }
        }

        $satkerPaguRows = \Illuminate\Support\Facades\DB::table('sintesa_satker_pagu')
            ->selectRaw('kppn_kode, satker_kode, MAX(satker_uraian) as nama_satker, MAX(kementerian_kode) as kl_kode, MAX(kementerian_uraian) as kl_nama, SUM(pagu) as pagu')
            ->groupBy('kppn_kode', 'satker_kode')
            ->orderByDesc('pagu')
            ->get();
        $satkerRealRows = \Illuminate\Support\Facades\DB::table('sintesa_belanja_bulanan')
            ->selectRaw('kppn_kode, satker_kode, SUM(total_realisasi) as realisasi')
            ->groupBy('kppn_kode', 'satker_kode')
            ->get();
        $satkerRealMap = [];
        foreach ($satkerRealRows as $sr) {
            $satkerRealMap[$sr->kppn_kode . '|' . $sr->satker_kode] = (float) $sr->realisasi;
        }
        $topSatkersPerKppn = [];
        foreach ($satkerPaguRows as $sp) {
            $k = $sp->kppn_kode;
            if (!isset($topSatkersPerKppn[$k])) {
                $topSatkersPerKppn[$k] = [];
            }
            if (count($topSatkersPerKppn[$k]) >= 8) {
                continue;
            }
            $sPagu = (float) $sp->pagu;
            $sReal = $satkerRealMap[$k . '|' . $sp->satker_kode] ?? 0.0;
            $sPct = $sPagu > 0 ? round(($sReal / $sPagu) * 100, 2) : 0.0;
            $topSatkersPerKppn[$k][] = [
                'kode' => $sp->satker_kode,
                'nama' => $sp->nama_satker,
                'kl' => $sp->kl_nama,
                'jenis' => $sp->kl_kode === '999' ? 'BA BUN / TKD' : 'K/L',
                'pagu' => $sPagu,
                'pagu_formatted' => 'Rp ' . number_format($sPagu, 0, ',', '.'),
                'realisasi' => $sReal,
                'realisasi_formatted' => 'Rp ' . number_format($sReal, 0, ',', '.'),
                'persen' => $sPct,
                'persen_formatted' => number_format($sPct, 2, ',', '.') . '%',
            ];
        }

        $formatBucket = static function (float $pagu, float $real, float $blokir, int $satkers): array {
            $sisa = max(0, $pagu - $real);
            $pctReal = $pagu > 0 ? round(($real / $pagu) * 100, 2) : 0.0;
            $pctSisa = $pagu > 0 ? round(max(0, 100 - $pctReal), 2) : 0.0;
            $pctBlokir = $pagu > 0 ? round(($blokir / $pagu) * 100, 2) : 0.0;
            return [
                'pagu' => $pagu,
                'pagu_formatted' => 'Rp ' . number_format($pagu, 0, ',', '.'),
                'realisasi' => $real,
                'realisasi_formatted' => 'Rp ' . number_format($real, 0, ',', '.'),
                'sisa' => $sisa,
                'sisa_formatted' => 'Rp ' . number_format($sisa, 0, ',', '.'),
                'blokir' => $blokir,
                'blokir_formatted' => 'Rp ' . number_format($blokir, 0, ',', '.'),
                'persen_realisasi' => $pctReal,
                'persen_realisasi_formatted' => number_format($pctReal, 2, ',', '.') . '%',
                'persen_sisa' => $pctSisa,
                'persen_sisa_formatted' => number_format($pctSisa, 2, ',', '.') . '%',
                'persen_blokir' => $pctBlokir,
                'satker_count' => $satkers,
            ];
        };

        $kppns = [];
        $totPkl = 0.0; $totRkl = 0.0; $totBkl = 0.0; $totCkl = 0;
        $totPbun = 0.0; $totRbun = 0.0; $totBbun = 0.0; $totCbun = 0;

        foreach ($paguRows as $kode => $p) {
            $namaClean = ucwords(strtolower(str_replace('L A N G S A', 'Langsa', trim($p->nama))));
            $pKl = (float) $p->pagu_kl;
            $rKl = (float) ($realRows[$kode]->real_kl ?? 0);
            $bKl = (float) $p->blokir_kl;
            $cKl = (int) $p->cnt_kl;

            $pBun = (float) $p->pagu_bun;
            $rBun = (float) ($realRows[$kode]->real_bun ?? 0);
            $bBun = (float) $p->blokir_bun;
            $cBun = (int) $p->cnt_bun;

            $totPkl += $pKl; $totRkl += $rKl; $totBkl += $bKl; $totCkl += $cKl;
            $totPbun += $pBun; $totRbun += $rBun; $totBbun += $bBun; $totCbun += $cBun;

            $jbList = array_values(array_map(function ($row) {
                $pct = $row['pagu'] > 0 ? round(($row['realisasi'] / $row['pagu']) * 100, 2) : 0.0;
                return [
                    'kode' => $row['kode'],
                    'label' => $row['label'],
                    'pagu' => $row['pagu'],
                    'pagu_formatted' => 'Rp ' . number_format($row['pagu'], 0, ',', '.'),
                    'realisasi' => $row['realisasi'],
                    'realisasi_formatted' => 'Rp ' . number_format($row['realisasi'], 0, ',', '.'),
                    'persen' => $pct,
                    'persen_formatted' => number_format($pct, 2, ',', '.') . '%',
                ];
            }, $jbPerKppn[$kode] ?? []));
            usort($jbList, fn($a, $b) => strcmp($a['kode'], $b['kode']));

            $kppns['kppn_' . $kode] = [
                'kode' => $kode,
                'nama' => 'KPPN ' . $namaClean,
                'all' => $formatBucket($pKl + $pBun, $rKl + $rBun, $bKl + $bBun, $cKl + $cBun),
                'kl' => $formatBucket($pKl, $rKl, $bKl, $cKl),
                'bun' => $formatBucket($pBun, $rBun, $bBun, $cBun),
                'jenis_belanja' => $jbList,
                'top_satkers' => $topSatkersPerKppn[$kode] ?? [],
            ];
        }

        return [
            'ALL' => [
                'kode' => 'ALL',
                'nama' => 'Semua 7 KPPN Se-Aceh',
                'all' => $formatBucket($totPkl + $totPbun, $totRkl + $totRbun, $totBkl + $totBbun, $totCkl + $totCbun),
                'kl' => $formatBucket($totPkl, $totRkl, $totBkl, $totCkl),
                'bun' => $formatBucket($totPbun, $totRbun, $totBbun, $totCbun),
            ],
            'kppns' => $kppns,
        ];
    }
}
