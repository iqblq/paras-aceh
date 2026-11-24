<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SintesaImportService;

class SyncSintesaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sintesa:sync {folder? : Custom path folder CSV SINTESA}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi dataset resmi SINTESA Kementerian Keuangan (Pagu, Belanja, Tematik, TKD) ke SQLite';

    /**
     * Execute the console command.
     */
    public function handle(SintesaImportService $service)
    {
        $this->info("==================================================================");
        $this->info("  🚀 PARAS SINTESA DATA SYNCHRONIZER (KANWIL DJPB ACEH) ");
        $this->info("==================================================================");

        $folder = $this->argument('folder') ?? 'c:\UIN\DJPb Aceh\dataset csv excel';
        $this->line("📂 Memindai folder: <comment>{$folder}</comment>\n");

        $startTime = microtime(true);
        $result = $service->syncAllFromFolder($folder);
        $duration = round(microtime(true) - $startTime, 2);

        if ($result['status'] === 'error') {
            $this->error("❌ {$result['message']}");
            return 1;
        }

        $details = $result['details'];

        if (isset($details['pagu'])) {
            $p = $details['pagu'];
            $this->info("  [1/5] Inquiry Pagu & Blokir Satker : {$p['rows']} baris diimpor ({$p['file']})");
            $this->line("        Total Pagu   : Rp " . number_format($p['total_pagu'], 0, ',', '.'));
            $this->line("        Total Blokir : Rp " . number_format($p['total_blokir'], 0, ',', '.'));
        }

        if (isset($details['belanja'])) {
            $b = $details['belanja'];
            $this->info("  [2/5] Belanja Kewilayahan Bulanan   : {$b['rows']} baris diimpor ({$b['file']})");
            $this->line("        Total Realisasi : Rp " . number_format($b['total_realisasi'], 0, ',', '.'));
        }

        if (isset($details['tematik'])) {
            $t = $details['tematik'];
            $this->info("  [3/5] Prioritas Presiden 2026       : {$t['rows']} paket diimpor ({$t['file']})");
            $this->line("        Total Realisasi : Rp " . number_format($t['total_realisasi'], 0, ',', '.'));
        }

        if (isset($details['dana_desa'])) {
            $d = $details['dana_desa'];
            $this->info("  [4/5] TKD Dana Desa 2026            : {$d['rows']} Pemda diimpor ({$d['file']})");
            $this->line("        Pagu Dana Desa  : Rp " . number_format($d['total_pagu'], 0, ',', '.'));
            $this->line("        Salur Dana Desa : Rp " . number_format($d['total_realisasi'], 0, ',', '.'));
        }

        if (isset($details['dak_fisik'])) {
            $k = $details['dak_fisik'];
            $this->info("  [5/5] TKD DAK Fisik 2025 Baseline   : {$k['rows']} sub-bidang diimpor ({$k['file']})");
            $this->line("        Salur DAK Fisik : Rp " . number_format($k['total_penyaluran'], 0, ',', '.'));
        }

        $this->info("\n==================================================================");
        $this->info("  ✔ SINKRONISASI SUKSES DALAM {$duration} DETIK! ");
        $this->info("  Ringkasan eksekutif telah diperbarui di tabel sintesa_summary.");
        $this->info("==================================================================");

        return 0;
    }
}
