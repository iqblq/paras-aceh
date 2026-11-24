@extends('layouts.app')

@section('title', 'Preservasi Jalan - PARAS Kanwil DJPb Provinsi Aceh')

@section('content')
<div class="space-y-5 sm:space-y-6">

  <!-- 1. HEADER BRANDING BANNER -->
  <div class="bg-white rounded-2xl border border-slate-200/90 p-3.5 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white p-1 border border-slate-200/90 flex items-center justify-center shadow-xs shrink-0 overflow-hidden">
        <img src="{{ asset('images/paras.png') }}" alt="Logo PARAS" class="w-full h-full object-contain">
      </div>
      <div>
        <div class="flex items-center gap-1.5 leading-none">
          <span class="text-xs sm:text-sm font-black text-[#1e40af] tracking-tight">Kanwil DJPb Provinsi Aceh</span>
        </div>
        <div class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider mt-1">Indonesian Treasury · Pengawasan Konektivitas Jalan & Jembatan</div>
      </div>
    </div>

    <div class="text-left sm:text-right border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-100">
      <div class="flex items-center sm:justify-end gap-2">
        <h1 class="text-sm sm:text-lg font-black text-slate-900 tracking-tight">Preservasi Jalan</h1>
        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-300">{{ $summary['total_rows'] }} Output</span>
      </div>
      <p class="text-[10px] sm:text-[11px] text-slate-500 font-mono mt-0.5">T.A. 2026 · {{ $summary['total_satker'] }} Satker Pelaksana · {{ $summary['total_output'] }} Output Konektivitas Aktif</p>
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
        <span>Alokasi Jalan & Jembatan Nasional</span>
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
        <span class="font-bold text-amber-600">Perlu Akselerasi</span>
      </div>
    </div>

  </div>

  <!-- 3. RESUME SATKER & OUTPUT PRESERVASI JALAN -->
  <div class="space-y-3">
    <div class="flex items-center gap-2 text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase">
      <span class="text-base">🛣️</span>
      <span>RESUME SATKER & OUTPUT PRESERVASI JALAN</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
      <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-blue-600 p-4 shadow-xs hover:shadow-md transition-all flex items-center justify-between">
        <div>
          <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Satker Pelaksana</span>
          <div class="flex items-baseline gap-1.5 mt-1">
            <span class="text-2xl sm:text-3xl font-black text-blue-700 font-mono">{{ $summary['total_satker'] }}</span>
            <span class="text-xs font-bold text-slate-500">Satker</span>
          </div>
          <span class="text-[10px] text-slate-400 mt-1 block">BPJN, PJN I–III, P2JN & Dinas PUPR Aceh</span>
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
          <span class="text-[10px] text-slate-400 mt-1 block">Mencakup {{ $summary['total_rows'] }} Baris Paket Preservasi</span>
        </div>
        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
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
          <span class="text-[10px] text-slate-400 mt-1 block">Porsi 14,39% dari Total APBN Aceh</span>
        </div>
        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
      </div>
    </div>
  </div>

  <!-- 4. INTERACTIVE CHART & 9 OUTPUT CARDS: PAGU & REALISASI PER OUTPUT PRESERVASI JALAN -->
  <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-teal-600 p-4 sm:p-5 shadow-sm space-y-4">

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-slate-100">
      <div>
        <h2 class="text-xs sm:text-sm font-black text-slate-900 tracking-tight uppercase flex items-center gap-2">
          <span>📊</span>
          <span>Pagu & Realisasi per Output Preservasi Jalan</span>
        </h2>
        <p class="text-[11px] text-slate-400 mt-0.5">
          💡 Klik batang grafik atau kartu output untuk menyaring tabel 26 rincian preservasi jalan & jembatan di bawah
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <!-- Toggle Skala -->
        <div id="jalanScaleToggle" class="inline-flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200">
          <button type="button" id="btnJalanNominal" onclick="switchJalanScale('nominal')"
                  class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all">
            Nominal (Rp)
          </button>
          <button type="button" id="btnJalanPersen" onclick="switchJalanScale('persen')"
                  class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all">
            Persentase (%)
          </button>
        </div>

        <!-- Toggle Mode Grafik vs Kartu -->
        <div class="inline-flex items-center p-1 bg-slate-200/70 rounded-xl border border-slate-200">
          <button type="button" id="btnJalanChart" onclick="switchJalanView('chart')"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all">
            <span>📊 Grafik</span>
          </button>
          <button type="button" id="btnJalanCards" onclick="switchJalanView('cards')"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all">
            <span>⊞ Kartu (9)</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Executive Insight Strip -->
    <div class="flex flex-wrap items-center justify-between gap-2 text-[11px]">
      <div class="flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">
          🏆 Serapan 100%: [CBF] Jembatan & Dinas PUPR Aceh
        </span>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-bold">
          📌 Pagu Terbesar: [CDC] OM Jalan (Rp 3,05 T)
        </span>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 font-bold">
          ⚡ Perlu Akselerasi: [CDF] OM Jembatan (47,00%)
        </span>
      </div>
      <div id="jalanChartLegend" class="flex items-center gap-3 font-semibold text-slate-500">
        <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-sky-300"></span> Pagu DIPA</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-blue-600"></span> Realisasi</span>
      </div>
    </div>

    <!-- VIEW 1: Modern Horizontal Bar Chart -->
    <div id="jalanChartContainer" class="relative w-full h-[380px] sm:h-[420px]">
      <canvas id="chartPreservasiJalan"></canvas>
    </div>

    <!-- VIEW 2: 9 Output Cards Grid -->
    <div id="jalanCardsContainer" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
      @foreach ($outputs as $o)
        @php
          $badge = $o['persen'] >= 80
            ? ['lbl' => 'Optimal', 'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'bar' => 'bg-emerald-500', 'txt' => 'text-emerald-600']
            : ($o['persen'] >= 60
              ? ['lbl' => 'On Track', 'cls' => 'bg-blue-50 text-blue-700 border-blue-200', 'bar' => 'bg-blue-600', 'txt' => 'text-blue-600']
              : ['lbl' => 'Akselerasi', 'cls' => 'bg-rose-50 text-rose-700 border-rose-200', 'bar' => 'bg-rose-500', 'txt' => 'text-rose-600']);
        @endphp
        <div onclick="filterJalanByOutput('{{ $o['kode'] }}')"
             class="jalan-out-card bg-slate-50/60 hover:bg-white rounded-xl border border-slate-200 p-4 shadow-2xs hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer flex flex-col justify-between"
             data-jalan-out="{{ $o['kode'] }}">
          <div>
            <div class="flex items-start justify-between gap-2 mb-2.5">
              <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-md bg-teal-600 text-white font-mono text-[11px] font-black shrink-0">{{ $o['kode'] }}</span>
                <span class="text-xs font-extrabold text-slate-800 leading-snug">{{ $o['label'] }}</span>
              </div>
              <span class="shrink-0 text-[9px] font-bold px-1.5 py-0.5 rounded-full border {{ $badge['cls'] }}">{{ $badge['lbl'] }}</span>
            </div>

            <div class="space-y-1 text-xs font-mono mt-2">
              <div class="flex items-baseline justify-between gap-2">
                <span class="text-[10px] font-sans font-semibold text-slate-400">Pagu:</span>
                <span class="font-bold text-slate-800 text-[11px] whitespace-nowrap">Rp {{ number_format($o['pagu'], 0, ',', '.') }}</span>
              </div>
              <div class="flex items-baseline justify-between gap-2">
                <span class="text-[10px] font-sans font-semibold text-slate-400">Realisasi:</span>
                <span class="font-black {{ $badge['txt'] }} text-[11px] whitespace-nowrap">Rp {{ number_format($o['realisasi'], 0, ',', '.') }}</span>
              </div>
            </div>
          </div>

          <div class="mt-3 pt-2.5 border-t border-slate-200/70">
            <div class="flex items-center justify-between text-xs mb-1.5">
              <span class="text-[10px] text-slate-500 font-semibold">{{ $o['row_count'] }} Paket Output</span>
              <span class="font-mono font-black {{ $badge['txt'] }} text-xs">{{ number_format($o['persen'], 2, ',', '.') }}%</span>
            </div>
            <div class="w-full bg-slate-200/80 rounded-full h-1.5 overflow-hidden">
              <div class="{{ $badge['bar'] }} h-1.5 rounded-full" style="width: {{ min($o['persen'], 100) }}%"></div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

  </div>

  <!-- 5. SECTION TABEL 26 RINCIAN OUTPUT PRESERVASI JALAN -->
  <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">

    <!-- Toolbar Search + Filter Pills + Export CSV -->
    <div class="p-3.5 sm:p-4 border-b border-slate-200/80 bg-slate-50/70 space-y-3">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <div class="w-full lg:w-96 relative">
          <input type="text"
                 id="jalanSearchInput"
                 onkeyup="filterJalanTable()"
                 placeholder="🔍 Cari Satker, Kode Output (CDC, CDF, CBR), atau Kegiatan..."
                 class="w-full bg-white border border-slate-200 rounded-xl pl-3.5 pr-10 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-600/20 focus:border-teal-600 transition-all font-medium shadow-2xs">
        </div>

        <div class="flex items-center gap-1.5 text-xs overflow-x-auto pb-1 lg:pb-0 no-scrollbar sm:flex-wrap">
          <button onclick="resetJalanFilters()" id="btnJalanAll" class="shrink-0 jalan-filter-btn px-3 py-1.5 bg-teal-600 text-white rounded-lg font-bold shadow-xs transition-all border border-teal-600">
            Semua (26)
          </button>
          <button onclick="filterJalanStatus('tinggi', this)" class="shrink-0 jalan-filter-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg font-semibold transition-all border border-slate-200">
            Serapan &ge; 80%
          </button>
          <button onclick="filterJalanStatus('rendah', this)" class="shrink-0 jalan-filter-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-rose-50 hover:text-rose-700 rounded-lg font-semibold transition-all border border-slate-200">
            Serapan &lt; 60%
          </button>
          <a href="{{ route('tematik.jalan.export') }}" class="shrink-0 sm:ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-semibold shadow-xs transition-all">
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span>Unduh CSV</span>
          </a>
        </div>
      </div>

      <!-- 6 Satker Filter Pills -->
      <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar text-[11px]">
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Filter Satker:</span>
        @php
          $jalanSatkers = [
            ['kode' => '693818', 'nama' => 'PJN Wilayah III Aceh (7)'],
            ['kode' => '693817', 'nama' => 'PJN Wilayah II Aceh (6)'],
            ['kode' => '693816', 'nama' => 'PJN Wilayah I Aceh (5)'],
            ['kode' => '693753', 'nama' => 'BPJN Aceh (4)'],
            ['kode' => '693754', 'nama' => 'Dinas PUPR Aceh (3)'],
            ['kode' => '693838', 'nama' => 'P2JN Aceh (1)'],
          ];
        @endphp
        @foreach ($jalanSatkers as $js)
          <button onclick="filterJalanBySatker('{{ $js['kode'] }}', this)"
                  data-jalan-satker="{{ $js['kode'] }}"
                  class="jalan-satker-pill shrink-0 px-2.5 py-1 rounded-lg bg-white text-slate-600 hover:bg-teal-50 hover:text-teal-700 border border-slate-200 font-semibold transition-all">
            <span class="font-mono font-bold text-teal-600">{{ $js['kode'] }}</span> · {{ $js['nama'] }}
          </button>
        @endforeach
      </div>
    </div>

    <!-- Active Filter Info Banner -->
    <div id="jalanFilterBanner" class="hidden px-4 py-2 bg-teal-50 border-b border-teal-100 flex items-center justify-between text-xs text-teal-800">
      <span id="jalanFilterText">Filter aktif...</span>
      <button onclick="resetJalanFilters()" class="text-[11px] font-bold text-teal-700 hover:underline">Reset Filter ✕</button>
    </div>

    <!-- Data Table (Kolom Persis Gambar 2 & 3) -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse" id="jalanDataTable">
        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-extrabold border-b border-slate-200/90 text-[10px]">
          <tr>
            <th class="py-3 px-3.5 w-12 text-center select-none">NO</th>
            <th class="py-3 px-3.5 w-24 select-none">KODE SATKER</th>
            <th class="py-3 px-3.5 min-w-[220px] select-none">NAMA SATKER</th>
            <th class="py-3 px-3.5 min-w-[280px] select-none">PROGRAM & KEGIATAN</th>
            <th onclick="sortJalanTable('pagu')" class="py-3 px-3.5 text-right whitespace-nowrap cursor-pointer hover:text-teal-600 transition-colors select-none">
              <span>PAGU</span> <span id="jalanSort-pagu" class="text-[9px] text-slate-400">↕</span>
            </th>
            <th onclick="sortJalanTable('realisasi')" class="py-3 px-3.5 text-right whitespace-nowrap cursor-pointer hover:text-teal-600 transition-colors select-none">
              <span>REALISASI</span> <span id="jalanSort-realisasi" class="text-[9px] text-slate-400">↕</span>
            </th>
            <th onclick="sortJalanTable('pct')" class="py-3 px-3.5 text-left w-36 whitespace-nowrap cursor-pointer hover:text-teal-600 transition-colors select-none">
              <span>% REALISASI</span> <span id="jalanSort-pct" class="text-[9px] text-slate-400">↕</span>
            </th>
          </tr>
        </thead>
        <tbody id="jalanTableBody" class="divide-y divide-slate-100 text-slate-700">
          @foreach ($items as $it)
            @php
              $sisa = max(0, $it['pagu'] - $it['realisasi']);
              $barColor = $it['pct'] >= 80 ? 'bg-emerald-500' : ($it['pct'] >= 60 ? 'bg-blue-600' : 'bg-amber-500');
              $pctColor = $it['pct'] >= 80 ? 'text-emerald-700' : ($it['pct'] >= 60 ? 'text-slate-900' : 'text-amber-700');
              $searchStr = strtolower($it['kode_satker'] . ' ' . $it['nama_satker'] . ' ' . $it['kl'] . ' ' . $it['wilayah'] . ' ' . $it['kode_out'] . ' ' . $it['kegiatan'] . ' ' . $it['output'] . ' ' . $it['program']);
            @endphp
            <tr onclick="openJalanModal(this)"
                class="jalan-row hover:bg-teal-50/35 transition-colors cursor-pointer group"
                data-search="{{ $searchStr }}"
                data-satker-kode="{{ $it['kode_satker'] }}"
                data-out-kode="{{ $it['kode_out'] }}"
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
                <div class="font-extrabold text-slate-900 group-hover:text-teal-700 transition-colors leading-snug">
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
                  <span class="shrink-0 px-1.5 py-0.5 rounded bg-teal-50 text-teal-700 border border-teal-200 font-mono text-[10px] font-black leading-none mt-0.5">
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

    <!-- Footer Counter -->
    <div class="px-4 py-3 bg-slate-50/70 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] text-slate-500">
      <span id="jalanCountInfo">Menampilkan <strong class="text-slate-800">26</strong> dari 26 rincian output Preservasi Jalan</span>
      <span class="font-mono text-[10px] text-slate-400">Klik baris untuk detail paket · Kanwil DJPb Provinsi Aceh</span>
    </div>

  </div>

  <!-- 6. MODAL DETAIL OUTPUT PRESERVASI JALAN -->
  <div id="jalanDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="relative w-full max-w-xl max-h-[92vh] flex flex-col bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto">

      <div class="p-4 sm:p-5 bg-gradient-to-r from-[#0b132b] via-[#1c2541] to-[#0f172a] border-b border-slate-700/80 text-white flex items-start justify-between gap-3 shrink-0">
        <div>
          <div class="flex flex-wrap items-center gap-2 mb-1.5">
            <span id="mjKode" class="px-2 py-0.5 rounded-md font-mono text-xs font-black bg-teal-500/20 text-teal-200 border border-teal-400/30">000000</span>
            <span id="mjOutBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30">Output</span>
          </div>
          <h3 id="mjSatker" class="text-sm sm:text-base font-black text-white leading-snug">Nama Satker</h3>
          <p id="mjKlWilayah" class="text-xs text-slate-300 mt-1 font-medium">K/L · Wilayah</p>
        </div>
        <button onclick="closeJalanModal()" class="p-1 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white transition-colors shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1">
        <div class="p-3.5 rounded-xl bg-teal-50/70 border border-teal-100 space-y-1.5 text-xs">
          <div>
            <span class="text-[10px] font-bold text-teal-700 uppercase tracking-wider block">Output Preservasi Jalan & Jembatan</span>
            <span id="mjOutput" class="font-extrabold text-slate-900"></span>
          </div>
          <div class="pt-1 border-t border-teal-100">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Kegiatan</span>
            <span id="mjKegiatan" class="text-slate-700 font-semibold"></span>
          </div>
          <div class="pt-1 border-t border-teal-100">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Program APBN</span>
            <span id="mjProgram" class="text-slate-600 font-medium"></span>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
            <div class="text-[10px] font-bold text-slate-500 uppercase">PAGU DIPA</div>
            <div id="mjPagu" class="text-xs sm:text-sm font-black text-slate-900 font-mono mt-0.5">Rp 0</div>
          </div>
          <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/80">
            <div class="text-[10px] font-bold text-emerald-700 uppercase">REALISASI</div>
            <div id="mjReal" class="text-xs sm:text-sm font-black text-emerald-600 font-mono mt-0.5">Rp 0</div>
          </div>
          <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/80">
            <div class="text-[10px] font-bold text-amber-700 uppercase">SISA PAGU</div>
            <div id="mjSisa" class="text-xs sm:text-sm font-black text-amber-600 font-mono mt-0.5">Rp 0</div>
          </div>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1.5">
          <div class="flex items-center justify-between text-xs">
            <span class="font-semibold text-slate-600">Capaian Realisasi Output:</span>
            <span id="mjPct" class="font-mono font-black text-slate-900 text-sm">0%</span>
          </div>
          <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
            <div id="mjBar" class="bg-teal-600 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
          </div>
        </div>
      </div>

      <div class="p-3.5 sm:p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
        <span class="text-[10px] text-slate-400 font-mono">Preservasi Jalan · Kanwil DJPb Provinsi Aceh</span>
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <button onclick="copyJalanInfo()" id="btnCopyJalan" class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-xs font-bold text-slate-700 transition-colors">
            Salin Info
          </button>
          <button onclick="closeJalanModal()" class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-xs font-bold text-white transition-colors">
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
  const jalanOutputsData = @json($outputs);
  let jalanChart = null;
  let activeJalanScale = 'nominal';
  let activeJalanOut = 'all';
  let activeJalanSatker = 'all';
  let activeJalanStatus = 'all';

  function formatJalanRp(val) {
    return 'Rp ' + Math.round(val).toLocaleString('id-ID');
  }

  function renderJalanChart() {
    const canvas = document.getElementById('chartPreservasiJalan');
    if (!canvas || typeof Chart === 'undefined') return;
    if (jalanChart) jalanChart.destroy();

    const labels = jalanOutputsData.map(o => {
      const maxLen = window.innerWidth < 640 ? 20 : 34;
      const lbl = `[${o.kode}] ${o.label}`;
      return lbl.length > maxLen ? lbl.slice(0, maxLen) + '…' : lbl;
    });

    const isNominal = activeJalanScale === 'nominal';
    const pluginsList = typeof ChartDataLabels !== 'undefined' ? [ChartDataLabels] : [];

    const datasets = isNominal
      ? [
          {
            label: 'Pagu DIPA',
            data: jalanOutputsData.map(o => +(o.pagu / 1e12).toFixed(4)),
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
            data: jalanOutputsData.map(o => +(o.realisasi / 1e12).toFixed(4)),
            backgroundColor: jalanOutputsData.map(o => o.persen >= 80 ? '#10b981' : (o.persen >= 60 ? '#2563eb' : '#f43f5e')),
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
                const o = jalanOutputsData[ctx.dataIndex];
                return o ? `${o.persen.toFixed(1).replace('.', ',')}%` : '';
              }
            }
          }
        ]
      : [
          {
            label: '% Realisasi',
            data: jalanOutputsData.map(o => o.persen),
            backgroundColor: jalanOutputsData.map(o => o.persen >= 80 ? '#10b981' : (o.persen >= 60 ? '#2563eb' : '#f43f5e')),
            borderRadius: 6,
            barPercentage: 0.65,
            datalabels: {
              display: true,
              anchor: 'end',
              align: 'right',
              offset: 6,
              color: '#0f172a',
              font: { family: '"Plus Jakarta Sans", sans-serif', size: 11, weight: '800' },
              formatter: (val) => val.toFixed(2).replace('.', ',') + '%'
            }
          }
        ];

    jalanChart = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: { labels, datasets },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        onClick: (evt, els) => {
          if (els && els.length > 0) {
            const item = jalanOutputsData[els[0].index];
            if (item) filterJalanByOutput(item.kode);
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
              title: (items) => jalanOutputsData[items[0].dataIndex]?.nama || '',
              label: (ctx) => {
                const o = jalanOutputsData[ctx.dataIndex];
                if (!o) return '';
                if (!isNominal) {
                  return [
                    ` Realisasi : ${formatJalanRp(o.realisasi)} (${o.persen.toFixed(2).replace('.', ',')}%)`,
                    ` Pagu DIPA : ${formatJalanRp(o.pagu)}`
                  ];
                }
                if (ctx.datasetIndex === 0) return ` Pagu DIPA : ${formatJalanRp(o.pagu)}`;
                return ` Realisasi : ${formatJalanRp(o.realisasi)} (${o.persen.toFixed(2).replace('.', ',')}%)`;
              },
              afterBody: () => '\n Klik batang untuk filter tabel 26 output'
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            suggestedMax: isNominal ? 3.55 : 114,
            grid: { color: '#f1f5f9' },
            ticks: {
              color: '#64748b',
              font: { family: 'monospace', size: 10 },
              callback: (val) => isNominal ? ('Rp ' + val + ' T') : (val <= 100 ? val + '%' : '')
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

  function switchJalanScale(scale) {
    activeJalanScale = scale;
    const bNom = document.getElementById('btnJalanNominal');
    const bPct = document.getElementById('btnJalanPersen');
    const leg = document.getElementById('jalanChartLegend');
    if (scale === 'nominal') {
      bNom.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all';
      bPct.className = 'px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all';
      if (leg) leg.classList.remove('hidden');
    } else {
      bPct.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all';
      bNom.className = 'px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all';
      if (leg) leg.classList.add('hidden');
    }
    renderJalanChart();
  }

  function switchJalanView(mode) {
    const chartBox = document.getElementById('jalanChartContainer');
    const cardsBox = document.getElementById('jalanCardsContainer');
    const scaleToggle = document.getElementById('jalanScaleToggle');
    const leg = document.getElementById('jalanChartLegend');
    const btnChart = document.getElementById('btnJalanChart');
    const btnCards = document.getElementById('btnJalanCards');

    if (mode === 'cards') {
      chartBox.classList.add('hidden');
      cardsBox.classList.remove('hidden');
      scaleToggle.classList.add('hidden');
      if (leg) leg.classList.add('hidden');
      btnCards.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all';
      btnChart.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all';
    } else {
      cardsBox.classList.add('hidden');
      chartBox.classList.remove('hidden');
      scaleToggle.classList.remove('hidden');
      if (leg && activeJalanScale === 'nominal') leg.classList.remove('hidden');
      btnChart.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all';
      btnCards.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all';
      if (jalanChart) jalanChart.resize();
    }
  }

  // Filters
  function filterJalanTable() {
    const kw = (document.getElementById('jalanSearchInput')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('.jalan-row');
    let visible = 0;

    rows.forEach(r => {
      const txt = r.getAttribute('data-search') || '';
      const out = r.getAttribute('data-out-kode') || '';
      const sat = r.getAttribute('data-satker-kode') || '';
      const pct = parseFloat(r.getAttribute('data-pct') || 0);

      const mKw = !kw || txt.includes(kw);
      const mOut = activeJalanOut === 'all' || out === activeJalanOut;
      const mSat = activeJalanSatker === 'all' || sat === activeJalanSatker;
      let mStat = true;
      if (activeJalanStatus === 'tinggi') mStat = pct >= 80;
      if (activeJalanStatus === 'rendah') mStat = pct < 60;

      if (mKw && mOut && mSat && mStat) {
        r.style.display = '';
        visible++;
      } else {
        r.style.display = 'none';
      }
    });

    const info = document.getElementById('jalanCountInfo');
    if (info) info.innerHTML = `Menampilkan <strong class="text-slate-800">${visible}</strong> dari 26 rincian output Preservasi Jalan`;
  }

  function filterJalanByOutput(kode) {
    activeJalanOut = kode;
    document.querySelectorAll('.jalan-out-card').forEach(c => {
      if (c.getAttribute('data-jalan-out') === kode) {
        c.classList.add('ring-2', 'ring-teal-600', 'bg-teal-50/40');
      } else {
        c.classList.remove('ring-2', 'ring-teal-600', 'bg-teal-50/40');
      }
    });
    const found = jalanOutputsData.find(o => o.kode === kode);
    const banner = document.getElementById('jalanFilterBanner');
    const txt = document.getElementById('jalanFilterText');
    if (banner && txt) {
      banner.classList.remove('hidden');
      txt.textContent = `Filter Output Aktif: ${found ? found.nama : kode}`;
    }
    filterJalanTable();
    document.getElementById('jalanDataTable')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function filterJalanBySatker(kode, btnEl) {
    activeJalanSatker = kode;
    document.querySelectorAll('.jalan-satker-pill').forEach(b => {
      if (b.getAttribute('data-jalan-satker') === kode) {
        b.className = 'jalan-satker-pill shrink-0 px-2.5 py-1 rounded-lg bg-teal-600 text-white border border-teal-600 font-bold transition-all shadow-2xs';
      } else {
        b.className = 'jalan-satker-pill shrink-0 px-2.5 py-1 rounded-lg bg-white text-slate-600 hover:bg-teal-50 hover:text-teal-700 border border-slate-200 font-semibold transition-all';
      }
    });
    const banner = document.getElementById('jalanFilterBanner');
    const txt = document.getElementById('jalanFilterText');
    if (banner && txt) {
      banner.classList.remove('hidden');
      txt.textContent = `Filter Satker Aktif: ${btnEl ? btnEl.textContent.trim() : kode}`;
    }
    filterJalanTable();
  }

  function filterJalanStatus(status, btnEl) {
    activeJalanStatus = status;
    document.querySelectorAll('.jalan-filter-btn').forEach(b => {
      b.className = 'shrink-0 jalan-filter-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-slate-100 rounded-lg font-semibold transition-all border border-slate-200';
    });
    if (btnEl) btnEl.className = 'shrink-0 jalan-filter-btn px-3 py-1.5 bg-teal-600 text-white rounded-lg font-bold shadow-xs transition-all border border-teal-600';
    filterJalanTable();
  }

  function resetJalanFilters() {
    activeJalanOut = 'all';
    activeJalanSatker = 'all';
    activeJalanStatus = 'all';
    const inp = document.getElementById('jalanSearchInput');
    if (inp) inp.value = '';

    document.querySelectorAll('.jalan-filter-btn').forEach(b => {
      b.className = 'shrink-0 jalan-filter-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-slate-100 rounded-lg font-semibold transition-all border border-slate-200';
    });
    const btnAll = document.getElementById('btnJalanAll');
    if (btnAll) btnAll.className = 'shrink-0 jalan-filter-btn px-3 py-1.5 bg-teal-600 text-white rounded-lg font-bold shadow-xs transition-all border border-teal-600';

    document.querySelectorAll('.jalan-satker-pill').forEach(b => {
      b.className = 'jalan-satker-pill shrink-0 px-2.5 py-1 rounded-lg bg-white text-slate-600 hover:bg-teal-50 hover:text-teal-700 border border-slate-200 font-semibold transition-all';
    });
    document.querySelectorAll('.jalan-out-card').forEach(c => {
      c.classList.remove('ring-2', 'ring-teal-600', 'bg-teal-50/40');
    });

    document.getElementById('jalanFilterBanner')?.classList.add('hidden');
    filterJalanTable();
  }

  // Sort
  let jalanSortState = { col: '', dir: 'desc' };
  function sortJalanTable(col) {
    const tbody = document.getElementById('jalanTableBody');
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('.jalan-row'));
    const dir = (jalanSortState.col === col && jalanSortState.dir === 'desc') ? 'asc' : 'desc';
    jalanSortState = { col, dir };

    ['pagu', 'realisasi', 'pct'].forEach(c => {
      const el = document.getElementById(`jalanSort-${c}`);
      if (el) el.textContent = '↕';
    });
    const activeEl = document.getElementById(`jalanSort-${col}`);
    if (activeEl) activeEl.textContent = dir === 'asc' ? '▲' : '▼';

    rows.sort((a, b) => {
      const va = parseFloat(a.getAttribute(`data-${col}`) || 0);
      const vb = parseFloat(b.getAttribute(`data-${col}`) || 0);
      return dir === 'asc' ? va - vb : vb - va;
    });
    rows.forEach(r => tbody.appendChild(r));
  }

  // Modal Inspector
  let currentJalanItem = null;
  function openJalanModal(row) {
    const d = {
      kode: row.getAttribute('data-satker-kode'),
      outKode: row.getAttribute('data-out-kode'),
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
    currentJalanItem = d;

    document.getElementById('mjKode').textContent = d.kode;
    document.getElementById('mjOutBadge').textContent = `Output [${d.outKode}]`;
    document.getElementById('mjSatker').textContent = d.satker;
    document.getElementById('mjKlWilayah').textContent = `${d.kl} · ${d.wilayah}`;
    document.getElementById('mjOutput').textContent = d.output;
    document.getElementById('mjKegiatan').textContent = d.kegiatan;
    document.getElementById('mjProgram').textContent = d.program;
    document.getElementById('mjPagu').textContent = d.pagu;
    document.getElementById('mjReal').textContent = d.real;
    document.getElementById('mjSisa').textContent = d.sisa;
    document.getElementById('mjPct').textContent = d.pct.toFixed(2) + '%';
    document.getElementById('mjBar').style.width = Math.min(d.pct, 100) + '%';

    document.getElementById('jalanDetailModal')?.classList.remove('hidden');
  }

  function closeJalanModal() {
    document.getElementById('jalanDetailModal')?.classList.add('hidden');
  }

  function copyJalanInfo() {
    if (!currentJalanItem) return;
    const d = currentJalanItem;
    const txt = `[PRESERVASI JALAN - KANWIL DJPB PROVINSI ACEH]\nSatker: ${d.kode} - ${d.satker}\nOutput: ${d.output}\nKegiatan: ${d.kegiatan}\nPagu: ${d.pagu}\nRealisasi: ${d.real} (${d.pct.toFixed(2)}%)\nSisa: ${d.sisa}`;
    navigator.clipboard.writeText(txt).then(() => {
      const btn = document.getElementById('btnCopyJalan');
      if (btn) {
        btn.textContent = '✓ Tersalin!';
        setTimeout(() => btn.textContent = 'Salin Info', 2000);
      }
    });
  }

  document.getElementById('jalanDetailModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeJalanModal();
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeJalanModal();
  });

  document.addEventListener('DOMContentLoaded', renderJalanChart);
</script>
@endpush
