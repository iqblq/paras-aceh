<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Master Pagu DIPA & Blokir Satker (18.503 baris)
        Schema::create('sintesa_satker_pagu', function (Blueprint $table) {
            $table->id();
            $table->string('kementerian_kode', 10)->index();
            $table->string('kementerian_uraian', 255);
            $table->string('kanwil_kode', 10)->default('01');
            $table->string('kanwil_uraian', 100)->default('BANDA ACEH');
            $table->string('kppn_kode', 10)->index();
            $table->string('kppn_uraian', 100);
            $table->string('satker_kode', 20)->index();
            $table->string('satker_uraian', 255);
            $table->string('akun_kode', 20)->index();
            $table->string('akun_uraian', 255);
            $table->unsignedBigInteger('pagu')->default(0);
            $table->unsignedBigInteger('blokir')->default(0);
            $table->timestamps();
        });

        // 2. Tabel Belanja Kewilayahan Bulanan Real1 s/d Real12 (17.921 baris)
        Schema::create('sintesa_belanja_bulanan', function (Blueprint $table) {
            $table->id();
            $table->string('kementerian_kode', 10)->index();
            $table->string('kementerian_uraian', 255);
            $table->string('kanwil_kode', 10)->default('01');
            $table->string('kanwil_uraian', 100)->default('BANDA ACEH');
            $table->string('kppn_kode', 10)->index();
            $table->string('kppn_uraian', 100);
            $table->string('satker_kode', 20)->index();
            $table->string('satker_uraian', 255);
            $table->string('akun_kode', 20)->index();
            $table->string('akun_uraian', 255);
            $table->unsignedBigInteger('real1')->default(0);
            $table->unsignedBigInteger('real2')->default(0);
            $table->unsignedBigInteger('real3')->default(0);
            $table->unsignedBigInteger('real4')->default(0);
            $table->unsignedBigInteger('real5')->default(0);
            $table->unsignedBigInteger('real6')->default(0);
            $table->unsignedBigInteger('real7')->default(0);
            $table->unsignedBigInteger('real8')->default(0);
            $table->unsignedBigInteger('real9')->default(0);
            $table->unsignedBigInteger('real10')->default(0);
            $table->unsignedBigInteger('real11')->default(0);
            $table->unsignedBigInteger('real12')->default(0);
            $table->unsignedBigInteger('total_realisasi')->default(0);
            $table->timestamps();
        });

        // 3. Tabel Tematik Prioritas Presiden 2026 (73 baris)
        Schema::create('sintesa_tematik_prioritas', function (Blueprint $table) {
            $table->id();
            $table->string('kementerian_kode', 10);
            $table->string('kementerian_uraian', 255);
            $table->string('satker_kode', 20)->index();
            $table->string('satker_uraian', 255);
            $table->string('program_kode', 20)->nullable();
            $table->string('program_uraian', 255)->nullable();
            $table->string('kegiatan_kode', 20)->nullable();
            $table->string('kegiatan_uraian', 255)->nullable();
            $table->string('kdpriopres', 20)->index();
            $table->string('nmpriopres', 255);
            $table->unsignedBigInteger('real1')->default(0);
            $table->unsignedBigInteger('real2')->default(0);
            $table->unsignedBigInteger('real3')->default(0);
            $table->unsignedBigInteger('real4')->default(0);
            $table->unsignedBigInteger('real5')->default(0);
            $table->unsignedBigInteger('real6')->default(0);
            $table->unsignedBigInteger('real7')->default(0);
            $table->unsignedBigInteger('real8')->default(0);
            $table->unsignedBigInteger('real9')->default(0);
            $table->unsignedBigInteger('real10')->default(0);
            $table->unsignedBigInteger('real11')->default(0);
            $table->unsignedBigInteger('real12')->default(0);
            $table->unsignedBigInteger('total_realisasi')->default(0);
            $table->timestamps();
        });

        // 4. Tabel TKD Dana Desa 2026 (22 Pemda Kab/Kota)
        Schema::create('sintesa_tkd_dana_desa', function (Blueprint $table) {
            $table->id();
            $table->string('tahun', 4)->default('2026');
            $table->string('kppn_kode', 10)->index();
            $table->string('kppn_nama', 100);
            $table->string('lokasi_kode', 10)->index();
            $table->string('pemda_nama', 150);
            $table->unsignedBigInteger('pagu')->default(0);
            $table->unsignedBigInteger('realisasi')->default(0);
            $table->decimal('persentase', 5, 2)->default(0);
            $table->timestamps();
        });

        // 5. Tabel TKD DAK Fisik 2025 Baseline (113 Sub Bidang)
        Schema::create('sintesa_tkd_dak_fisik', function (Blueprint $table) {
            $table->id();
            $table->string('tahun', 4)->default('2025');
            $table->string('lokasi_kode', 10)->index();
            $table->string('pemda_nama', 150);
            $table->string('kppn_kode', 10)->nullable();
            $table->string('kppn_nama', 100)->nullable();
            $table->string('jenis_dana', 100)->nullable();
            $table->string('bidang_kode', 20)->nullable();
            $table->string('bidang_nama', 150)->nullable();
            $table->string('sub_bidang_kode', 20)->nullable();
            $table->string('sub_bidang_nama', 150)->nullable();
            $table->unsignedBigInteger('pagu')->default(0);
            $table->unsignedBigInteger('penyaluran')->default(0);
            $table->unsignedBigInteger('sisa_pagu')->default(0);
            $table->decimal('persentase', 5, 2)->default(0);
            $table->timestamps();
        });

        // 6. Tabel High-Speed Pre-Aggregated Summary (Zero-Latency Dashboard)
        Schema::create('sintesa_summary', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->longText('payload');
            $table->timestamps();
        });

        // 7. Tabel Log Sinkronisasi SINTESA
        Schema::create('sintesa_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename', 255);
            $table->string('file_type', 50);
            $table->integer('total_rows')->default(0);
            $table->string('status', 50)->default('SUCCESS');
            $table->text('message')->nullable();
            $table->string('synced_by', 100)->default('Syahrial (Kanwil DJPb)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sintesa_sync_logs');
        Schema::dropIfExists('sintesa_summary');
        Schema::dropIfExists('sintesa_tkd_dak_fisik');
        Schema::dropIfExists('sintesa_tkd_dana_desa');
        Schema::dropIfExists('sintesa_tematik_prioritas');
        Schema::dropIfExists('sintesa_belanja_bulanan');
        Schema::dropIfExists('sintesa_satker_pagu');
    }
};
