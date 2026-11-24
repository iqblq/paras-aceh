@extends('layouts.app')

@section('title', 'Penanganan Bencana - SIPANTAS Kanwil DJPb Provinsi Aceh')

@section('content')
<div class="space-y-5 sm:space-y-6">

  <!-- 1. HEADER BRANDING BANNER -->
  <div class="bg-white rounded-2xl border border-slate-200/90 p-3.5 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white p-1 border border-slate-200/90 flex items-center justify-center shadow-xs shrink-0 overflow-hidden">
        <img src="{{ asset('images/paras.png') }}" alt="Logo SIPANTAS" class="w-full h-full object-contain">
      </div>
      <div>
        <div class="flex items-center gap-1.5 leading-none">
          <span class="text-xs sm:text-sm font-black text-[#1e40af] tracking-tight">Kanwil DJPb Provinsi Aceh</span>
        </div>
        <div class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider mt-1">Indonesian Treasury · Pengawasan Anggaran Penanganan Bencana</div>
      </div>
    </div>

    <div class="text-left sm:text-right border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-100">
      <div class="flex flex-wrap items-center sm:justify-end gap-1.5">
        <h1 class="text-sm sm:text-lg font-black text-slate-900 tracking-tight">Penanganan Bencana</h1>
        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-300">{{ $summary['total_rows'] }} Output</span>
        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">{{ $summary['rkkl_satker'] }} Satker RKA-K/L</span>
      </div>
      <p class="text-[10px] sm:text-[11px] text-slate-500 font-mono mt-0.5">T.A. 2026 · Tanggap Darurat, SAR, Pengendalian Banjir & Pemulihan Infrastruktur</p>
    </div>
  </div>

  <!-- 2. TOP 3 EXECUTIVE KPI CARDS (RP SEJAJAR 1 BARIS + CIRCULAR RING) -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">

    <!-- Card 1: TOTAL PAGU -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-blue-600 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
      <div>
        <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1.5">
          TOTAL PAGU
        </div>
        <div class="flex items-baseline gap-1 text-blue-700 font-mono font-black tracking-tight whitespace-nowrap">
          <span class="text-xs sm:text-sm font-bold opacity-75">Rp</span>
          <span class="text-lg sm:text-2xl">{{ trim(str_replace('Rp', '', $summary['total_pagu_formatted'])) }}</span>
        </div>
      </div>
      <div class="text-[11px] text-slate-400 font-medium flex items-center justify-between pt-2.5 mt-2.5 border-t border-slate-100">
        <span>Alokasi Penanganan Bencana (6 Satker)</span>
        <span class="font-bold text-blue-600 font-mono">100%</span>
      </div>
    </div>

    <!-- Card 2: TOTAL REALISASI -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-emerald-500 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
      <div>
        <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1.5">
          TOTAL REALISASI
        </div>
        <div class="flex items-baseline gap-1 text-emerald-600 font-mono font-black tracking-tight whitespace-nowrap">
          <span class="text-xs sm:text-sm font-bold opacity-75">Rp</span>
          <span class="text-lg sm:text-2xl">{{ trim(str_replace('Rp', '', $summary['total_realisasi_formatted'])) }}</span>
        </div>
      </div>
      <div class="text-[11px] text-slate-400 font-medium flex items-center justify-between pt-2.5 mt-2.5 border-t border-slate-100">
        <span>Sisa: {{ $summary['sisa_pagu_short'] }} ({{ number_format($summary['persen_sisa'], 2, ',', '.') }}%)</span>
        <span class="font-bold text-emerald-600 font-mono">SP2D Online</span>
      </div>
    </div>

    <!-- Card 3: PERSENTASE + CIRCULAR PROGRESS RING -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-amber-500 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
      <div class="flex items-center justify-between gap-3">
        <div>
          <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1">
            PERSENTASE
          </div>
          <div class="text-xl sm:text-3xl font-black text-amber-500 font-mono tracking-tight leading-none my-1">
            {{ number_format($summary['persen_realisasi'], 2, '.', ',') }}%
          </div>
        </div>
        <div class="relative w-14 h-14 sm:w-16 sm:h-16 shrink-0 flex items-center justify-center">
          <svg class="w-full h-full -rotate-90" viewBox="0 0 64 64">
            <circle cx="32" cy="32" r="26" fill="none" stroke="#fef3c7" stroke-width="6"></circle>
            <circle cx="32" cy="32" r="26" fill="none" stroke="#f59e0b" stroke-width="6"
                    stroke-linecap="round"
                    stroke-dasharray="163.36"
                    stroke-dashoffset="{{ round(163.36 * (1 - ($summary['persen_realisasi'] / 100)), 2) }}"></circle>
          </svg>
          <span class="absolute text-[10px] font-mono font-black text-amber-700">{{ number_format($summary['persen_realisasi'], 1) }}%</span>
        </div>
      </div>
      <div class="text-[11px] text-slate-400 font-medium flex items-center justify-between pt-2 mt-2 border-t border-slate-100">
        <span>Target Serapan Q4: 90%</span>
        <span class="font-bold text-rose-600">Fokus Akselerasi</span>
      </div>
    </div>

  </div>

  <!-- 3. EXECUTIVE 2-TAB NAVIGATION BAR (REALISASI OUTPUT vs RKA-K/L BENCANA) -->
  <div class="bg-white rounded-2xl border border-slate-200/90 p-1.5 shadow-xs flex flex-col sm:flex-row gap-1.5">
    <button type="button" id="tabBtnRealisasi" onclick="switchBencanaTab('realisasi')"
            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold bg-rose-600 text-white shadow-sm transition-all">
      <span>🚨 Realisasi Output Penanganan Bencana</span>
      <span class="px-2 py-0.5 rounded-full bg-white/20 font-mono text-[10px]">6 Satker · Rp 1,3 T</span>
    </button>
    <button type="button" id="tabBtnRkkl" onclick="switchBencanaTab('rkkl')"
            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all">
      <span>📑 Resume & Rincian Item RKA-K/L Bencana</span>
      <span class="px-2 py-0.5 rounded-full bg-slate-200/80 text-slate-700 font-mono text-[10px]">47 Satker · Rp 10,6 T</span>
    </button>
  </div>

  <!-- ========================================================================= -->
  <!-- TAB PANEL 1: REALISASI OUTPUT PENANGANAN BENCANA (6 SATKER · 19 OUTPUT)   -->
  <!-- ========================================================================= -->
  <div id="panelBencanaRealisasi" class="space-y-5 sm:space-y-6">

    <!-- 3 Resume Cards -->
    <div class="space-y-3">
      <div class="flex items-center gap-2 text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase">
        <span class="text-base">📊</span>
        <span>RESUME SATKER & OUTPUT PENANGANAN BENCANA</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-blue-600 p-4 shadow-xs hover:shadow-md transition-all flex items-center justify-between">
          <div>
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Satker Pelaksana</span>
            <div class="flex items-baseline gap-1.5 mt-1">
              <span class="text-2xl sm:text-3xl font-black text-blue-700 font-mono">{{ $summary['total_satker'] }}</span>
              <span class="text-xs font-bold text-slate-500">Satker</span>
            </div>
            <span class="text-[10px] text-slate-400 mt-1 block">BASARNAS, BWS I, BPJN, Pengairan, Cipta Karya, Dinsos</span>
          </div>
          <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-amber-500 p-4 shadow-xs hover:shadow-md transition-all flex items-center justify-between">
          <div>
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Output Aktif</span>
            <div class="flex items-baseline gap-1.5 mt-1">
              <span class="text-2xl sm:text-3xl font-black text-amber-600 font-mono">{{ $summary['total_output'] }}</span>
              <span class="text-xs font-bold text-slate-500">Output</span>
            </div>
            <span class="text-[10px] text-slate-400 mt-1 block">Mencakup {{ $summary['total_rows'] }} Baris Kegiatan & Output</span>
          </div>
          <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-emerald-500 p-4 shadow-xs hover:shadow-md transition-all flex items-center justify-between">
          <div>
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Pagu DIPA</span>
            <div class="flex items-baseline gap-1.5 mt-1">
              <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-mono">{{ $summary['total_pagu_short'] }}</span>
              <span class="text-xs font-bold text-slate-500">Rupiah</span>
            </div>
            <span class="text-[10px] text-slate-400 mt-1 block">Fokus Tanggap Darurat & Pengendalian Banjir</span>
          </div>
          <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Interactive Chart: Pagu & Realisasi per Output Penanganan Bencana -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-rose-600 p-4 sm:p-5 shadow-sm space-y-4">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-slate-100">
        <div>
          <h2 class="text-xs sm:text-sm font-black text-slate-900 tracking-tight uppercase flex items-center gap-2">
            <span>📈</span>
            <span>Pagu & Realisasi per Output Penanganan Bencana</span>
          </h2>
          <p class="text-[11px] text-slate-400 mt-0.5">
            💡 Klik batang grafik output untuk menyaring tabel 22 kegiatan penanganan bencana di bawah
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <!-- Toggle Top 10 vs Semua 19 Output -->
          <div class="inline-flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200">
            <button type="button" id="btnBencTop10" onclick="switchBencanaLimit(10)"
                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-slate-800 shadow-2xs transition-all">
              Top 10
            </button>
            <button type="button" id="btnBencAll19" onclick="switchBencanaLimit(19)"
                    class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all">
              Semua (19)
            </button>
          </div>

          <!-- Toggle Skala Nominal vs Persentase -->
          <div class="inline-flex items-center p-1 bg-slate-200/70 rounded-xl border border-slate-200">
            <button type="button" id="btnBencNominal" onclick="switchBencanaScale('nominal')"
                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all">
              Nominal (Rp)
            </button>
            <button type="button" id="btnBencPersen" onclick="switchBencanaScale('persen')"
                    class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all">
              Persentase (%)
            </button>
          </div>
        </div>
      </div>

      <!-- Insight Strip -->
      <div class="flex flex-wrap items-center justify-between gap-2 text-[11px]">
        <div class="flex flex-wrap items-center gap-2">
          <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">
            🏆 Tertinggi: [RDH] Pos SAR (100%) & [CDR] Pengairan (99,83%)
          </span>
          <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-bold">
            📌 Pagu Terbesar: [BHC] Operasi Bencana (Rp 520,4 M)
          </span>
          <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 font-bold">
            ⚡ Perlu Akselerasi: [CBH] Pengendalian Bencana (21,00%)
          </span>
        </div>
        <div id="bencChartLegend" class="flex items-center gap-3 font-semibold text-slate-500">
          <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-sky-300"></span> Pagu DIPA</span>
          <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-blue-600"></span> Realisasi</span>
        </div>
      </div>

      <!-- Chart Container -->
      <div id="bencanaChartBox" class="relative w-full h-[380px] sm:h-[420px]">
        <canvas id="chartBencanaOutput"></canvas>
      </div>
    </div>

    <!-- Interactive Table: 22 Output Penanganan Bencana -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
      <div class="p-3.5 sm:p-4 border-b border-slate-200/80 bg-slate-50/70 space-y-3">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
          <div class="w-full lg:w-96 relative">
            <input type="text"
                   id="bencanaSearchInput"
                   onkeyup="filterBencanaTable()"
                   placeholder="🔍 Cari Satker, Kode Output (BHC, CBH, RCM), atau Kegiatan..."
                   class="w-full bg-white border border-slate-200 rounded-xl pl-3.5 pr-10 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-600/20 focus:border-rose-600 transition-all font-medium shadow-2xs">
          </div>

          <div class="flex items-center gap-1.5 text-xs overflow-x-auto pb-1 lg:pb-0 no-scrollbar sm:flex-wrap">
            <button onclick="resetBencanaFilters()" id="btnBencFilterAll" class="shrink-0 benc-filter-btn px-3 py-1.5 bg-rose-600 text-white rounded-lg font-bold shadow-xs transition-all border border-rose-600">
              Semua (22)
            </button>
            <button onclick="filterBencanaStatus('tinggi', this)" class="shrink-0 benc-filter-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg font-semibold transition-all border border-slate-200">
              Serapan &ge; 80%
            </button>
            <button onclick="filterBencanaStatus('rendah', this)" class="shrink-0 benc-filter-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-rose-50 hover:text-rose-700 rounded-lg font-semibold transition-all border border-slate-200">
              Serapan &lt; 60%
            </button>
            <a href="{{ route('tematik.bencana.export') }}" class="shrink-0 sm:ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-semibold shadow-xs transition-all">
              <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
              </svg>
              <span>Unduh CSV</span>
            </a>
          </div>
        </div>

        <!-- Quick Satker Filter Pills -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar text-[11px]">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Filter Satker:</span>
          @php
            $satkerPills = [
              ['kode' => '614794', 'nama' => 'BASARNAS Banda Aceh (7)'],
              ['kode' => '694269', 'nama' => 'BWS Sumatera I (7)'],
              ['kode' => '694081', 'nama' => 'Dinas Pengairan Aceh (3)'],
              ['kode' => '498551', 'nama' => 'PJN Wilayah I Aceh (2)'],
              ['kode' => '690510', 'nama' => 'Dinsos Aceh (2)'],
              ['kode' => '694003', 'nama' => 'BPBPK Cipta Karya Aceh (1)'],
            ];
          @endphp
          @foreach ($satkerPills as $sp)
            <button onclick="filterBencanaBySatker('{{ $sp['kode'] }}', this)"
                    data-satker-pill="{{ $sp['kode'] }}"
                    class="benc-satker-pill shrink-0 px-2.5 py-1 rounded-lg bg-white text-slate-600 hover:bg-rose-50 hover:text-rose-700 border border-slate-200 font-semibold transition-all">
              <span class="font-mono font-bold text-rose-600">{{ $sp['kode'] }}</span> · {{ $sp['nama'] }}
            </button>
          @endforeach
        </div>
      </div>

      <!-- Active Filter Banner -->
      <div id="bencanaFilterBanner" class="hidden px-4 py-2 bg-rose-50 border-b border-rose-100 flex items-center justify-between text-xs text-rose-800">
        <span id="bencanaFilterText">Filter aktif...</span>
        <button onclick="resetBencanaFilters()" class="text-[11px] font-bold text-rose-700 hover:underline">Reset Filter ✕</button>
      </div>

      <!-- Data Table (Kolom Persis Gambar 3 & 4) -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse" id="bencanaDataTable">
          <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-extrabold border-b border-slate-200/90 text-[10px]">
            <tr>
              <th class="py-3 px-3.5 w-12 text-center select-none">NO</th>
              <th class="py-3 px-3.5 w-24 select-none">KODE SATKER</th>
              <th class="py-3 px-3.5 min-w-[220px] select-none">NAMA SATKER</th>
              <th class="py-3 px-3.5 min-w-[290px] select-none">PROGRAM & KEGIATAN</th>
              <th onclick="sortBencanaTable('pagu')" class="py-3 px-3.5 text-right whitespace-nowrap cursor-pointer hover:text-rose-600 transition-colors select-none">
                <span>PAGU</span> <span id="bencSort-pagu" class="text-[9px] text-slate-400">↕</span>
              </th>
              <th onclick="sortBencanaTable('realisasi')" class="py-3 px-3.5 text-right whitespace-nowrap cursor-pointer hover:text-rose-600 transition-colors select-none">
                <span>REALISASI</span> <span id="bencSort-realisasi" class="text-[9px] text-slate-400">↕</span>
              </th>
              <th onclick="sortBencanaTable('pct')" class="py-3 px-3.5 text-left w-36 whitespace-nowrap cursor-pointer hover:text-rose-600 transition-colors select-none">
                <span>% REALISASI</span> <span id="bencSort-pct" class="text-[9px] text-slate-400">↕</span>
              </th>
            </tr>
          </thead>
          <tbody id="bencanaTableBody" class="divide-y divide-slate-100 text-slate-700">
            @foreach ($items as $it)
              @php
                $sisa = max(0, $it['pagu'] - $it['realisasi']);
                $barColor = $it['pct'] >= 80 ? 'bg-emerald-500' : ($it['pct'] >= 60 ? 'bg-blue-600' : 'bg-rose-500');
                $pctColor = $it['pct'] >= 80 ? 'text-emerald-700' : ($it['pct'] >= 60 ? 'text-slate-900' : 'text-rose-600');
                $searchStr = strtolower($it['kode_satker'] . ' ' . $it['nama_satker'] . ' ' . $it['kl'] . ' ' . $it['wilayah'] . ' ' . $it['kode_out'] . ' ' . $it['kegiatan'] . ' ' . $it['output'] . ' ' . $it['program']);
              @endphp
              <tr onclick="openBencanaModal(this)"
                  class="bencana-row hover:bg-rose-50/35 transition-colors cursor-pointer group"
                  data-search="{{ $searchStr }}"
                  data-satker-kode="{{ $it['kode_satker'] }}"
                  data-out-tag="{{ $it['kode_out'] }}"
                  data-pagu="{{ $it['pagu'] }}"
                  data-realisasi="{{ $it['realisasi'] }}"
                  data-pct="{{ $it['pct'] }}"
                  data-satker="{{ $it['nama_satker'] }}"
                  data-kl="{{ $it['kl'] }}"
                  data-wilayah="{{ $it['wilayah'] }}"
                  data-kegiatan="{{ $it['kegiatan'] }}"
                  data-output="{{ $it['output'] }}"
                  data-program="{{ $it['program'] }}"
                  data-pagu-fmt="Rp {{ number_format($it['pagu'], 0, ',', '.') }}"
                  data-real-fmt="Rp {{ number_format($it['realisasi'], 0, ',', '.') }}"
                  data-sisa-fmt="Rp {{ number_format($sisa, 0, ',', '.') }}">
                <td class="py-3.5 px-3.5 text-center font-mono text-slate-400 font-semibold">{{ $it['no'] }}</td>
                <td class="py-3.5 px-3.5 font-mono font-extrabold text-slate-900">{{ $it['kode_satker'] }}</td>
                <td class="py-3.5 px-3.5">
                  <div class="font-extrabold text-slate-900 group-hover:text-rose-700 transition-colors leading-snug">
                    {{ $it['nama_satker'] }}
                  </div>
                  <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wide mt-0.5">
                    {{ $it['kl'] }} · {{ $it['wilayah'] }}
                  </div>
                </td>
                <td class="py-3.5 px-3.5">
                  <div class="text-[11px] text-slate-600 leading-snug">
                    <span class="font-bold text-slate-700">Kegiatan:</span> {{ $it['kegiatan'] }}
                  </div>
                  <div class="flex items-start gap-1.5 mt-1">
                    <span class="shrink-0 px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200 font-mono text-[10px] font-black leading-none mt-0.5">
                      {{ $it['kode_out'] }}
                    </span>
                    <span class="font-extrabold text-slate-900 leading-snug">{{ preg_replace('/^\[[^\]]+\]\s*/', '', $it['output']) }}</span>
                  </div>
                  <div class="text-[10px] text-slate-400 mt-0.5 leading-snug">
                    Program: {{ $it['program'] }}
                  </div>
                </td>
                <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                  Rp {{ number_format($it['pagu'], 0, ',', '.') }}
                </td>
                <td class="py-3.5 px-3.5 text-right font-mono font-extrabold text-emerald-600 whitespace-nowrap">
                  Rp {{ number_format($it['realisasi'], 0, ',', '.') }}
                </td>
                <td class="py-3.5 px-3.5">
                  <div class="flex items-center justify-between font-mono text-[11px] font-black {{ $pctColor }} mb-1">
                    <span>{{ number_format($it['pct'], 2, '.', ',') }}%</span>
                  </div>
                  <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="{{ $barColor }} h-2 rounded-full transition-all duration-300" style="width: {{ min($it['pct'], 100) }}%"></div>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="px-4 py-3 bg-slate-50/70 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] text-slate-500">
        <span id="bencanaCountInfo">Menampilkan <strong class="text-slate-800">22</strong> dari 22 output Penanganan Bencana</span>
        <span class="font-mono text-[10px] text-slate-400">Klik baris untuk detail output · Kanwil DJPb Provinsi Aceh</span>
      </div>
    </div>

  </div>

  <!-- ========================================================================= -->
  <!-- TAB PANEL 2: RESUME & RINCIAN ITEM RKA-K/L PENANGANAN BENCANA (10,6 T)    -->
  <!-- ========================================================================= -->
  <div id="panelBencanaRkkl" class="hidden space-y-5 sm:space-y-6">

    <!-- 3 Resume Cards RKA-K/L -->
    <div class="space-y-3">
      <div class="flex items-center gap-2 text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase">
        <span class="text-base">📑</span>
        <span>RESUME RINCIAN OUTPUT PENANGANAN BENCANA (TAGGING RKA-K/L)</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-blue-600 p-4 shadow-xs flex items-center justify-between">
          <div>
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Satker Pelaksana</span>
            <div class="flex items-baseline gap-1.5 mt-1">
              <span class="text-2xl sm:text-3xl font-black text-blue-700 font-mono">{{ $summary['rkkl_satker'] }}</span>
              <span class="text-xs font-bold text-slate-500">Satker</span>
            </div>
            <span class="text-[10px] text-slate-400 mt-1 block">Seluruh Satker Bertagging Bencana di Aceh</span>
          </div>
          <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 font-black text-sm">47</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-amber-500 p-4 shadow-xs flex items-center justify-between">
          <div>
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Output Aktif</span>
            <div class="flex items-baseline gap-1.5 mt-1">
              <span class="text-2xl sm:text-3xl font-black text-amber-600 font-mono">{{ $summary['rkkl_output'] }}</span>
              <span class="text-xs font-bold text-slate-500">Output</span>
            </div>
            <span class="text-[10px] text-slate-400 mt-1 block">Klasifikasi Rincian Output (KRO) Bencana</span>
          </div>
          <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 font-black text-sm">46</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-emerald-500 p-4 shadow-xs flex items-center justify-between">
          <div>
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Pagu DIPA</span>
            <div class="flex items-baseline gap-1.5 mt-1">
              <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-mono">{{ $summary['rkkl_pagu_short'] }}</span>
              <span class="text-xs font-bold text-slate-500">Rupiah</span>
            </div>
            <span class="text-[10px] text-slate-400 mt-1 block">Gabungan Infrastruktur, Perumahan, Pendidikan & SAR</span>
          </div>
          <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 font-black text-xs">10,6T</div>
        </div>
      </div>
    </div>

    <!-- Chart RKA-K/L: Pagu per Output Penanganan Bencana -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-indigo-600 p-4 sm:p-5 shadow-sm space-y-3">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-slate-100">
        <div>
          <h2 class="text-xs sm:text-sm font-black text-slate-900 tracking-tight uppercase">Pagu per Output Penanganan Bencana (RKA-K/L)</h2>
          <p class="text-[11px] text-slate-400">11 Output Terbesar dari 46 Output Bertagging Bencana · Klik batang untuk menyaring item RKA-K/L di bawah</p>
        </div>
        <span class="text-[10px] font-mono px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold border border-indigo-200 self-start">Total Rp 10,6 Triliun</span>
      </div>
      <div class="relative w-full h-[360px] sm:h-[400px]">
        <canvas id="chartBencanaRkkl"></canvas>
      </div>
    </div>

    <!-- Table Rincian Item RKA-K/L Penanganan Bencana -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
      <div class="p-3.5 sm:p-4 border-b border-slate-200/80 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h3 class="text-xs sm:text-sm font-black text-slate-800 uppercase tracking-tight">📄 Rincian Item RKA-K/L Penanganan Bencana</h3>
          <p class="text-[11px] text-slate-400 mt-0.5">Detail Rincian Output (RO) dan Komponen kegiatan per Satker</p>
        </div>
        <div class="w-full sm:w-80">
          <input type="text"
                 id="rkklSearchInput"
                 onkeyup="filterRkklTable()"
                 placeholder="🔍 Cari Satker, Komponen, RO, atau Output..."
                 class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 font-medium shadow-2xs">
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse" id="rkklDataTable">
          <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-extrabold border-b border-slate-200/90 text-[10px]">
            <tr>
              <th class="py-3 px-3.5 w-12 text-center">NO</th>
              <th class="py-3 px-3.5 min-w-[210px]">SATKER</th>
              <th class="py-3 px-3.5 min-w-[250px]">KEGIATAN & OUTPUT</th>
              <th class="py-3 px-3.5 min-w-[250px]">RO & KOMPONEN</th>
              <th class="py-3 px-3.5 text-right whitespace-nowrap">PAGU ITEM</th>
            </tr>
          </thead>
          <tbody id="rkklTableBody" class="divide-y divide-slate-100 text-slate-700">
            @foreach ($rkklItems as $r)
              @php
                $rkklSearch = strtolower($r['kode_satker'] . ' ' . $r['nama_satker'] . ' ' . $r['kegiatan'] . ' ' . $r['output'] . ' ' . $r['ro'] . ' ' . $r['komponen']);
              @endphp
              <tr class="rkkl-row hover:bg-indigo-50/35 transition-colors" data-rkkl-search="{{ $rkklSearch }}">
                <td class="py-3 px-3.5 text-center font-mono text-slate-400 font-semibold">{{ $r['no'] }}</td>
                <td class="py-3 px-3.5">
                  <div class="font-mono font-extrabold text-slate-900">{{ $r['kode_satker'] }}</div>
                  <div class="text-[11px] font-bold text-slate-600 mt-0.5 leading-snug">{{ $r['nama_satker'] }}</div>
                </td>
                <td class="py-3 px-3.5">
                  <div class="font-semibold text-slate-800 leading-snug">Keg: {{ $r['kegiatan'] }}</div>
                  <div class="text-[11px] font-semibold text-blue-600 mt-0.5">Out: {{ $r['output'] }}</div>
                </td>
                <td class="py-3 px-3.5">
                  <div class="font-extrabold text-emerald-700 text-[11px] leading-snug">RO: {{ $r['ro'] }}</div>
                  <div class="text-[11px] text-slate-500 mt-0.5">Komp: {{ $r['komponen'] }}</div>
                </td>
                <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                  Rp {{ number_format($r['pagu'], 0, ',', '.') }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="px-4 py-3 bg-slate-50/70 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] text-slate-500">
        <span id="rkklCountInfo">Menampilkan <strong class="text-slate-800">{{ count($rkklItems) }}</strong> sampel item RKA-K/L utama (dari total 47 Satker · Rp 10,6 T)</span>
        <span class="font-mono text-[10px] text-slate-400">Tagging Tematik Bencana RKA-K/L · Kanwil DJPb Provinsi Aceh</span>
      </div>
    </div>

  </div>

  <!-- 4. MODAL DETAIL OUTPUT PENANGANAN BENCANA (MODERN INSPECTOR) -->
  <div id="bencanaDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="relative w-full max-w-xl max-h-[92vh] flex flex-col bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto">

      <div class="p-4 sm:p-5 bg-gradient-to-r from-[#0b132b] via-[#1c2541] to-[#0f172a] border-b border-slate-700/80 text-white flex items-start justify-between gap-3 shrink-0">
        <div>
          <div class="flex flex-wrap items-center gap-2 mb-1.5">
            <span id="mbKode" class="px-2 py-0.5 rounded-md font-mono text-xs font-black bg-rose-500/20 text-rose-200 border border-rose-400/30">000000</span>
            <span id="mbOutTag" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30">Output</span>
          </div>
          <h3 id="mbSatker" class="text-sm sm:text-base font-black text-white leading-snug">Nama Satker</h3>
          <p id="mbKlWilayah" class="text-xs text-slate-300 mt-1 font-medium">K/L · Wilayah</p>
        </div>
        <button onclick="closeBencanaModal()" class="p-1 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white transition-colors shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1">
        <div class="p-3.5 rounded-xl bg-rose-50/70 border border-rose-100 space-y-1.5 text-xs">
          <div>
            <span class="text-[10px] font-bold text-rose-700 uppercase tracking-wider block">Output Penanganan Bencana</span>
            <span id="mbOutput" class="font-extrabold text-slate-900"></span>
          </div>
          <div class="pt-1 border-t border-rose-100">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Kegiatan</span>
            <span id="mbKegiatan" class="text-slate-700 font-semibold"></span>
          </div>
          <div class="pt-1 border-t border-rose-100">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Program APBN</span>
            <span id="mbProgram" class="text-slate-600 font-medium"></span>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
            <div class="text-[10px] font-bold text-slate-500 uppercase">PAGU DIPA</div>
            <div id="mbPagu" class="text-xs sm:text-sm font-black text-slate-900 font-mono mt-0.5">Rp 0</div>
          </div>
          <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/80">
            <div class="text-[10px] font-bold text-emerald-700 uppercase">REALISASI</div>
            <div id="mbReal" class="text-xs sm:text-sm font-black text-emerald-600 font-mono mt-0.5">Rp 0</div>
          </div>
          <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/80">
            <div class="text-[10px] font-bold text-amber-700 uppercase">SISA PAGU</div>
            <div id="mbSisa" class="text-xs sm:text-sm font-black text-amber-600 font-mono mt-0.5">Rp 0</div>
          </div>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1.5">
          <div class="flex items-center justify-between text-xs">
            <span class="font-semibold text-slate-600">Capaian Realisasi Output:</span>
            <span id="mbPct" class="font-mono font-black text-slate-900 text-sm">0%</span>
          </div>
          <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
            <div id="mbBar" class="bg-rose-600 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
          </div>
        </div>
      </div>

      <div class="p-3.5 sm:p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
        <span class="text-[10px] text-slate-400 font-mono">Penanganan Bencana · Kanwil DJPb Provinsi Aceh</span>
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <button onclick="copyBencanaInfo()" id="btnCopyBencana" class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-xs font-bold text-slate-700 transition-colors">
            Salin Info
          </button>
          <button onclick="closeBencanaModal()" class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-xs font-bold text-white transition-colors">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  const bencanaOutputsData = @json($outputs);
  const bencanaRkklData = @json($rkklOutputs);

  let bencanaChart = null;
  let rkklChart = null;
  let bencLimit = 10;
  let bencScale = 'nominal';
  let activeBencSatker = 'all';
  let activeBencTag = 'all';
  let activeBencStatus = 'all';

  function formatBencRp(val) {
    return 'Rp ' + Math.round(val).toLocaleString('id-ID');
  }

  // 2-Tab Switcher
  function switchBencanaTab(tab) {
    const pReal = document.getElementById('panelBencanaRealisasi');
    const pRkkl = document.getElementById('panelBencanaRkkl');
    const bReal = document.getElementById('tabBtnRealisasi');
    const bRkkl = document.getElementById('tabBtnRkkl');

    if (tab === 'rkkl') {
      pReal.classList.add('hidden');
      pRkkl.classList.remove('hidden');
      bRkkl.className = 'flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold bg-indigo-600 text-white shadow-sm transition-all';
      bReal.className = 'flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all';
      if (!rkklChart) {
        renderRkklChart();
      } else {
        rkklChart.resize();
      }
    } else {
      pRkkl.classList.add('hidden');
      pReal.classList.remove('hidden');
      bReal.className = 'flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold bg-rose-600 text-white shadow-sm transition-all';
      bRkkl.className = 'flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all';
      if (bencanaChart) bencanaChart.resize();
    }
  }

  function renderBencanaChart() {
    const canvas = document.getElementById('chartBencanaOutput');
    const box = document.getElementById('bencanaChartBox');
    if (!canvas || typeof Chart === 'undefined') return;
    if (bencanaChart) bencanaChart.destroy();

    const slice = bencanaOutputsData.slice(0, bencLimit);
    if (box) {
      box.style.height = bencLimit > 10 ? '640px' : (window.innerWidth < 640 ? '380px' : '420px');
    }

    const labels = slice.map(o => {
      const maxLen = window.innerWidth < 640 ? 20 : 34;
      const lbl = `[${o.tag}] ${o.label}`;
      return lbl.length > maxLen ? lbl.slice(0, maxLen) + '…' : lbl;
    });

    const isNominal = bencScale === 'nominal';
    const pluginsList = typeof ChartDataLabels !== 'undefined' ? [ChartDataLabels] : [];

    const datasets = isNominal
      ? [
          {
            label: 'Pagu DIPA',
            data: slice.map(o => +(o.pagu / 1e9).toFixed(2)), // Miliar
            backgroundColor: 'rgba(125, 211, 252, 0.85)',
            borderColor: '#38bdf8',
            borderWidth: 1,
            borderRadius: 5,
            barPercentage: 0.85,
            categoryPercentage: 0.72,
            datalabels: { display: false }
          },
          {
            label: 'Realisasi',
            data: slice.map(o => +(o.realisasi / 1e9).toFixed(2)), // Miliar
            backgroundColor: slice.map(o => o.persen >= 80 ? '#10b981' : (o.persen >= 60 ? '#2563eb' : '#f43f5e')),
            borderRadius: 5,
            barPercentage: 0.85,
            categoryPercentage: 0.72,
            datalabels: {
              display: true,
              anchor: 'end',
              align: 'right',
              offset: 6,
              color: '#0f172a',
              font: { family: '"Plus Jakarta Sans", sans-serif', size: 10, weight: '800' },
              formatter: (val, ctx) => {
                const o = slice[ctx.dataIndex];
                return o ? `${o.persen.toFixed(1).replace('.', ',')}%` : '';
              }
            }
          }
        ]
      : [
          {
            label: '% Realisasi',
            data: slice.map(o => o.persen),
            backgroundColor: slice.map(o => o.persen >= 80 ? '#10b981' : (o.persen >= 60 ? '#2563eb' : '#f43f5e')),
            borderRadius: 6,
            barPercentage: 0.65,
            datalabels: {
              display: true,
              anchor: 'end',
              align: 'right',
              offset: 6,
              color: '#0f172a',
              font: { family: '"Plus Jakarta Sans", sans-serif', size: 10, weight: '800' },
              formatter: (val) => val.toFixed(2).replace('.', ',') + '%'
            }
          }
        ];

    bencanaChart = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: { labels, datasets },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        onClick: (evt, els) => {
          if (els && els.length > 0) {
            const item = slice[els[0].index];
            if (item) filterBencanaByTag(item.tag, item.nama);
          }
        },
        onHover: (evt, els) => {
          evt.native.target.style.cursor = els.length ? 'pointer' : 'default';
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0f172a',
            titleColor: '#f8fafc',
            bodyColor: '#cbd5e1',
            padding: 12,
            cornerRadius: 10,
            titleFont: { family: '"Plus Jakarta Sans", sans-serif', size: 12, weight: 'bold' },
            bodyFont: { family: 'monospace', size: 11 },
            callbacks: {
              title: (items) => slice[items[0].dataIndex]?.nama || '',
              label: (ctx) => {
                const o = slice[ctx.dataIndex];
                if (!o) return '';
                if (!isNominal) {
                  return [
                    ` Realisasi : ${formatBencRp(o.realisasi)} (${o.persen.toFixed(2).replace('.', ',')}%)`,
                    ` Pagu DIPA : ${formatBencRp(o.pagu)}`
                  ];
                }
                if (ctx.datasetIndex === 0) return ` Pagu DIPA : ${formatBencRp(o.pagu)}`;
                return ` Realisasi : ${formatBencRp(o.realisasi)} (${o.persen.toFixed(2).replace('.', ',')}%)`;
              },
              afterBody: () => '\n Klik batang untuk filter tabel output di bawah'
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            suggestedMax: isNominal ? 600 : 114,
            grid: { color: '#f1f5f9' },
            ticks: {
              color: '#64748b',
              font: { family: 'monospace', size: 10 },
              callback: (val) => isNominal ? ('Rp ' + val + ' M') : (val <= 100 ? val + '%' : '')
            }
          },
          y: {
            grid: { display: false },
            ticks: {
              color: '#1e293b',
              font: { family: '"Plus Jakarta Sans", sans-serif', size: 10.5, weight: '700' }
            }
          }
        }
      },
      plugins: pluginsList
    });
  }

  function switchBencanaLimit(lim) {
    bencLimit = lim;
    const b10 = document.getElementById('btnBencTop10');
    const b19 = document.getElementById('btnBencAll19');
    if (lim === 10) {
      b10.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-slate-800 shadow-2xs transition-all';
      b19.className = 'px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all';
    } else {
      b19.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-slate-800 shadow-2xs transition-all';
      b10.className = 'px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all';
    }
    renderBencanaChart();
  }

  function switchBencanaScale(scale) {
    bencScale = scale;
    const bNom = document.getElementById('btnBencNominal');
    const bPct = document.getElementById('btnBencPersen');
    const leg = document.getElementById('bencChartLegend');
    if (scale === 'nominal') {
      bNom.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all';
      bPct.className = 'px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all';
      if (leg) leg.classList.remove('hidden');
    } else {
      bPct.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all';
      bNom.className = 'px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all';
      if (leg) leg.classList.add('hidden');
    }
    renderBencanaChart();
  }

  // Render Tab 2 RKA-K/L Chart
  function renderRkklChart() {
    const canvas = document.getElementById('chartBencanaRkkl');
    if (!canvas || typeof Chart === 'undefined') return;
    if (rkklChart) rkklChart.destroy();

    const labels = bencanaRkklData.map(o => {
      const maxLen = window.innerWidth < 640 ? 22 : 38;
      return o.nama.length > maxLen ? o.nama.slice(0, maxLen) + '…' : o.nama;
    });
    const pluginsList = typeof ChartDataLabels !== 'undefined' ? [ChartDataLabels] : [];

    rkklChart = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Pagu RKA-K/L',
          data: bencanaRkklData.map(o => +(o.pagu / 1e12).toFixed(3)),
          backgroundColor: '#3b82f6',
          hoverBackgroundColor: '#1d4ed8',
          borderRadius: 6,
          barPercentage: 0.7,
          datalabels: {
            display: true,
            anchor: 'end',
            align: 'right',
            offset: 6,
            color: '#1e293b',
            font: { family: '"Plus Jakarta Sans", sans-serif', size: 10, weight: '800' },
            formatter: (val) => val >= 1 ? (`Rp ${val.toFixed(2).replace('.', ',')} T`) : (`Rp ${Math.round(val * 1000)} M`)
          }
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        onClick: (evt, els) => {
          if (els && els.length > 0) {
            const item = bencanaRkklData[els[0].index];
            if (item) {
              const inp = document.getElementById('rkklSearchInput');
              if (inp) {
                inp.value = item.nama.split(' ').slice(0, 3).join(' ');
                filterRkklTable();
                document.getElementById('rkklDataTable')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
              }
            }
          }
        },
        onHover: (evt, els) => {
          evt.native.target.style.cursor = els.length ? 'pointer' : 'default';
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0f172a',
            padding: 12,
            cornerRadius: 10,
            callbacks: {
              title: (items) => bencanaRkklData[items[0].dataIndex]?.nama || '',
              label: (ctx) => {
                const o = bencanaRkklData[ctx.dataIndex];
                return ` Pagu: ${formatBencRp(o.pagu)} (${o.satker_count} Satker)`;
              }
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            suggestedMax: 3.1,
            grid: { color: '#f1f5f9' },
            ticks: {
              color: '#64748b',
              font: { family: 'monospace', size: 10 },
              callback: (val) => 'Rp ' + val + ' T'
            }
          },
          y: {
            grid: { display: false },
            ticks: {
              color: '#1e293b',
              font: { family: '"Plus Jakarta Sans", sans-serif', size: 10.5, weight: '700' }
            }
          }
        }
      },
      plugins: pluginsList
    });
  }

  // Table 1 Filter & Sort
  function filterBencanaTable() {
    const keyword = (document.getElementById('bencanaSearchInput')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('.bencana-row');
    let visible = 0;

    rows.forEach(r => {
      const text = r.getAttribute('data-search') || '';
      const satker = r.getAttribute('data-satker-kode') || '';
      const tag = r.getAttribute('data-out-tag') || '';
      const pct = parseFloat(r.getAttribute('data-pct') || 0);

      const mKey = !keyword || text.includes(keyword);
      const mSat = activeBencSatker === 'all' || satker === activeBencSatker;
      const mTag = activeBencTag === 'all' || tag === activeBencTag;
      let mStat = true;
      if (activeBencStatus === 'tinggi') mStat = pct >= 80;
      if (activeBencStatus === 'rendah') mStat = pct < 60;

      if (mKey && mSat && mTag && mStat) {
        r.style.display = '';
        visible++;
      } else {
        r.style.display = 'none';
      }
    });

    const info = document.getElementById('bencanaCountInfo');
    if (info) info.innerHTML = `Menampilkan <strong class="text-slate-800">${visible}</strong> dari 22 output Penanganan Bencana`;
  }

  function filterBencanaByTag(tag, label) {
    activeBencTag = tag;
    const banner = document.getElementById('bencanaFilterBanner');
    const txt = document.getElementById('bencanaFilterText');
    if (banner && txt) {
      banner.classList.remove('hidden');
      txt.textContent = `Filter Output Aktif: ${label}`;
    }
    filterBencanaTable();
    document.getElementById('bencanaDataTable')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function filterBencanaBySatker(kode, btnEl) {
    activeBencSatker = kode;
    document.querySelectorAll('.benc-satker-pill').forEach(b => {
      if (b.getAttribute('data-satker-pill') === kode) {
        b.className = 'benc-satker-pill shrink-0 px-2.5 py-1 rounded-lg bg-rose-600 text-white border border-rose-600 font-bold transition-all shadow-2xs';
      } else {
        b.className = 'benc-satker-pill shrink-0 px-2.5 py-1 rounded-lg bg-white text-slate-600 hover:bg-rose-50 hover:text-rose-700 border border-slate-200 font-semibold transition-all';
      }
    });
    const banner = document.getElementById('bencanaFilterBanner');
    const txt = document.getElementById('bencanaFilterText');
    if (banner && txt) {
      banner.classList.remove('hidden');
      txt.textContent = `Filter Satker Aktif: ${btnEl ? btnEl.textContent.trim() : kode}`;
    }
    filterBencanaTable();
  }

  function filterBencanaStatus(status, btnEl) {
    activeBencStatus = status;
    document.querySelectorAll('.benc-filter-btn').forEach(b => {
      b.className = 'shrink-0 benc-filter-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-slate-100 rounded-lg font-semibold transition-all border border-slate-200';
    });
    if (btnEl) btnEl.className = 'shrink-0 benc-filter-btn px-3 py-1.5 bg-rose-600 text-white rounded-lg font-bold shadow-xs transition-all border border-rose-600';
    filterBencanaTable();
  }

  function resetBencanaFilters() {
    activeBencSatker = 'all';
    activeBencTag = 'all';
    activeBencStatus = 'all';
    const inp = document.getElementById('bencanaSearchInput');
    if (inp) inp.value = '';

    document.querySelectorAll('.benc-filter-btn').forEach(b => {
      b.className = 'shrink-0 benc-filter-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-slate-100 rounded-lg font-semibold transition-all border border-slate-200';
    });
    const btnAll = document.getElementById('btnBencFilterAll');
    if (btnAll) btnAll.className = 'shrink-0 benc-filter-btn px-3 py-1.5 bg-rose-600 text-white rounded-lg font-bold shadow-xs transition-all border border-rose-600';

    document.querySelectorAll('.benc-satker-pill').forEach(b => {
      b.className = 'benc-satker-pill shrink-0 px-2.5 py-1 rounded-lg bg-white text-slate-600 hover:bg-rose-50 hover:text-rose-700 border border-slate-200 font-semibold transition-all';
    });

    document.getElementById('bencanaFilterBanner')?.classList.add('hidden');
    filterBencanaTable();
  }

  let bencSortState = { col: '', dir: 'desc' };
  function sortBencanaTable(col) {
    const tbody = document.getElementById('bencanaTableBody');
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('.bencana-row'));
    const dir = (bencSortState.col === col && bencSortState.dir === 'desc') ? 'asc' : 'desc';
    bencSortState = { col, dir };

    ['pagu', 'realisasi', 'pct'].forEach(c => {
      const el = document.getElementById(`bencSort-${c}`);
      if (el) el.textContent = '↕';
    });
    const activeEl = document.getElementById(`bencSort-${col}`);
    if (activeEl) activeEl.textContent = dir === 'asc' ? '▲' : '▼';

    rows.sort((a, b) => {
      const va = parseFloat(a.getAttribute(`data-${col}`) || 0);
      const vb = parseFloat(b.getAttribute(`data-${col}`) || 0);
      return dir === 'asc' ? va - vb : vb - va;
    });
    rows.forEach(r => tbody.appendChild(r));
  }

  // Table 2 RKA-K/L Search
  function filterRkklTable() {
    const kw = (document.getElementById('rkklSearchInput')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('.rkkl-row');
    let visible = 0;
    rows.forEach(r => {
      const txt = r.getAttribute('data-rkkl-search') || '';
      if (!kw || txt.includes(kw)) {
        r.style.display = '';
        visible++;
      } else {
        r.style.display = 'none';
      }
    });
    const info = document.getElementById('rkklCountInfo');
    if (info) info.innerHTML = `Menampilkan <strong class="text-slate-800">${visible}</strong> item RKA-K/L Penanganan Bencana`;
  }

  // Modal Inspector
  let currentBencItem = null;
  function openBencanaModal(row) {
    const d = {
      kode: row.getAttribute('data-satker-kode'),
      tag: row.getAttribute('data-out-tag'),
      satker: row.getAttribute('data-satker'),
      kl: row.getAttribute('data-kl'),
      wilayah: row.getAttribute('data-wilayah'),
      kegiatan: row.getAttribute('data-kegiatan'),
      output: row.getAttribute('data-output'),
      program: row.getAttribute('data-program'),
      pagu: row.getAttribute('data-pagu-fmt'),
      real: row.getAttribute('data-real-fmt'),
      sisa: row.getAttribute('data-sisa-fmt'),
      pct: parseFloat(row.getAttribute('data-pct') || 0)
    };
    currentBencItem = d;

    document.getElementById('mbKode').textContent = d.kode;
    document.getElementById('mbOutTag').textContent = `Output [${d.tag}]`;
    document.getElementById('mbSatker').textContent = d.satker;
    document.getElementById('mbKlWilayah').textContent = `${d.kl} · ${d.wilayah}`;
    document.getElementById('mbOutput').textContent = d.output;
    document.getElementById('mbKegiatan').textContent = d.kegiatan;
    document.getElementById('mbProgram').textContent = d.program;
    document.getElementById('mbPagu').textContent = d.pagu;
    document.getElementById('mbReal').textContent = d.real;
    document.getElementById('mbSisa').textContent = d.sisa;
    document.getElementById('mbPct').textContent = d.pct.toFixed(2) + '%';
    document.getElementById('mbBar').style.width = Math.min(d.pct, 100) + '%';

    document.getElementById('bencanaDetailModal')?.classList.remove('hidden');
  }

  function closeBencanaModal() {
    document.getElementById('bencanaDetailModal')?.classList.add('hidden');
  }

  function copyBencanaInfo() {
    if (!currentBencItem) return;
    const d = currentBencItem;
    const txt = `[PENANGANAN BENCANA - KANWIL DJPB PROVINSI ACEH]\nSatker: ${d.kode} - ${d.satker}\nOutput: ${d.output}\nKegiatan: ${d.kegiatan}\nPagu: ${d.pagu}\nRealisasi: ${d.real} (${d.pct.toFixed(2)}%)\nSisa: ${d.sisa}`;
    navigator.clipboard.writeText(txt).then(() => {
      const btn = document.getElementById('btnCopyBencana');
      if (btn) {
        btn.textContent = '✓ Tersalin!';
        setTimeout(() => btn.textContent = 'Salin Info', 2000);
      }
    });
  }

  document.getElementById('bencanaDetailModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeBencanaModal();
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeBencanaModal();
  });

  document.addEventListener('DOMContentLoaded', renderBencanaChart);
</script>
@endpush
