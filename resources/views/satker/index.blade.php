@extends('layouts.app')

@section('title', 'Realisasi Anggaran 311 Satker - PARAS Kanwil DJPb Provinsi Aceh')

@section('content')
<div class="space-y-6">

  <!-- 1. HEADER BRANDING BANNER (PERSIS GAMBAR 1) -->
  <div class="bg-white rounded-2xl border border-slate-200/90 p-3.5 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
    <div class="flex items-center gap-3">
      <!-- Logo InTress Kanwil DJPb Provinsi Aceh -->
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white p-1 border border-slate-200/90 flex items-center justify-center shadow-xs shrink-0 overflow-hidden">
          <img src="{{ asset('images/paras.png') }}" alt="Logo PARAS" class="w-full h-full object-contain">
        </div>
        <div>
          <div class="flex items-center gap-1.5 leading-none">
            <span class="text-xs sm:text-sm font-black text-[#1e40af] tracking-tight">Kanwil DJPb Provinsi Aceh</span>
          </div>
          <div class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider mt-1">Indonesian Treasury · Portal PARAS Regional</div>
        </div>
      </div>
    </div>

    <!-- Title Kanan -->
    <div class="text-left sm:text-right border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-100">
      <h1 class="text-sm sm:text-lg font-black text-slate-900 tracking-tight">Realisasi Anggaran</h1>
      <p class="text-[10px] sm:text-[11px] text-slate-500 font-mono">T.A. 2026 · Kanwil DJPb Provinsi Aceh ({{ count($satkerList) }} Satker)</p>
    </div>
  </div>

  <!-- 2. TOP 3 KPI SUMMARY CARDS (PERSIS GAMBAR 1 DENGAN AKSEN BORDER KIRI MODERN) -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
    
    <!-- Card 1: TOTAL PAGU -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-blue-600 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
      <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1">
        TOTAL PAGU
      </div>
      <div class="text-lg sm:text-2xl font-black text-blue-700 font-mono tracking-tight my-1 break-all sm:break-normal">
        {{ $kpi['total_pagu_formatted'] }}
      </div>
      <div class="text-[11px] text-slate-400 font-medium flex items-center justify-between pt-2 border-t border-slate-100">
        <span>Alokasi Gabungan K/L & BA BUN</span>
        <span class="font-bold text-blue-600 font-mono">100%</span>
      </div>
    </div>

    <!-- Card 2: TOTAL REALISASI -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-emerald-500 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
      <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1">
        TOTAL REALISASI
      </div>
      <div class="text-lg sm:text-2xl font-black text-emerald-600 font-mono tracking-tight my-1 break-all sm:break-normal">
        {{ $kpi['total_realisasi_formatted'] }}
      </div>
      <div class="text-[11px] text-slate-400 font-medium flex items-center justify-between pt-2 border-t border-slate-100">
        <span>Capaian Seluruh Pembayaran SP2D</span>
        <span class="font-bold text-emerald-600 font-mono">SP2D Online</span>
      </div>
    </div>

    <!-- Card 3: PERSENTASE DENGAN MINI CIRCULAR PROGRESS RING -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-amber-500 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
      <div class="flex items-center justify-between gap-3">
        <div>
          <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1">
            PERSENTASE
          </div>
          <div class="text-xl sm:text-3xl font-black text-amber-500 font-mono tracking-tight leading-none my-1">
            {{ number_format($kpi['persen_realisasi'], 2, ',', '.') }}%
          </div>
        </div>
        <!-- Circular SVG Ring Progress -->
        <div class="relative w-14 h-14 sm:w-16 sm:h-16 shrink-0 flex items-center justify-center">
          <svg class="w-full h-full -rotate-90" viewBox="0 0 64 64">
            <circle cx="32" cy="32" r="26" fill="none" stroke="#fef3c7" stroke-width="6"></circle>
            <circle cx="32" cy="32" r="26" fill="none" stroke="#f59e0b" stroke-width="6"
                    stroke-linecap="round"
                    stroke-dasharray="163.36"
                    stroke-dashoffset="{{ round(163.36 * (1 - ($kpi['persen_realisasi'] / 100)), 2) }}"></circle>
          </svg>
          <span class="absolute text-[10px] font-mono font-black text-amber-700">{{ number_format($kpi['persen_realisasi'], 1) }}%</span>
        </div>
      </div>
      <div class="text-[11px] text-slate-400 font-medium flex items-center justify-between pt-2 mt-2 border-t border-slate-100">
        <span>Target Nasional Triwulan IV: 90%</span>
        <span class="font-bold text-amber-600">On Track</span>
      </div>
    </div>

  </div>

  <!-- 3. SECTION: "🛒 RINCIAN PAGU & REALISASI PER JENIS BELANJA" (10 KARTU + MODE GRAFIK INTERAKTIF) -->
  <div class="space-y-3">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2.5">
      <div>
        <div class="flex items-center gap-2 text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase">
          <span class="text-base">🛒</span>
          <span>RINCIAN PAGU & REALISASI PER JENIS BELANJA</span>
        </div>
        <p class="text-[11px] text-slate-400 mt-0.5">
          💡 Klik kartu atau batang grafik jenis belanja untuk menyaring tabel satker di bawah
        </p>
      </div>

      <!-- Segmented Switcher: Tampilan Kartu vs Mode Grafik -->
      <div class="inline-flex items-center p-1 bg-slate-200/70 rounded-xl border border-slate-200 self-start md:self-auto shrink-0">
        <button type="button" id="btnViewCards" onclick="switchJenisBelanjaView('cards')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-blue-700 shadow-xs transition-all">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
          </svg>
          <span>Kartu (10)</span>
        </button>
        <button type="button" id="btnViewChart" onclick="switchJenisBelanjaView('chart')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 transition-all">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <span>Grafik Komparasi</span>
        </button>
      </div>
    </div>

    <!-- MODE 1: Grid 10 Kartu Jenis Belanja (5 Kolom x 2 Baris di Desktop) -->
    <div id="jenisBelanjaCardView" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3.5">
      @foreach ($jenisBelanja as $b)
        @php
          $statusBadge = $b['persen'] >= 90
            ? ['label' => 'Optimal', 'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200']
            : ($b['persen'] >= 60
              ? ['label' => 'On Track', 'cls' => 'bg-blue-50 text-blue-700 border-blue-200']
              : ['label' => 'Akselerasi', 'cls' => 'bg-rose-50 text-rose-700 border-rose-200']);
        @endphp
        <div onclick="filterTableByAkun('{{ $b['kode'] }}', this)" 
             class="jenis-belanja-card bg-white rounded-2xl border border-slate-200/90 border-t-[4px] {{ $b['border_accent'] }} p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer group flex flex-col justify-between"
             data-akun="{{ $b['kode'] }}">
          
          <div>
            <!-- Header Kartu + Mini Status Badge -->
            <div class="flex items-start justify-between gap-1.5 mb-2.5">
              <span class="text-[11px] font-black {{ $b['text_color'] }} uppercase tracking-wide leading-tight group-hover:translate-x-0.5 transition-transform">
                {{ $b['nama'] }}
              </span>
              <span class="shrink-0 text-[9px] font-bold px-1.5 py-0.5 rounded-full border {{ $statusBadge['cls'] }} flex items-center gap-1">
                @if ($b['persen'] < 60)
                  <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                @endif
                {{ $statusBadge['label'] }}
              </span>
            </div>

            <!-- Angka Pagu & Realisasi -->
            <div class="space-y-1.5 text-xs">
              <div class="flex items-baseline justify-between gap-2 font-mono">
                <span class="text-[10px] text-slate-400 font-sans font-semibold shrink-0">Pagu:</span>
                <span class="font-bold text-slate-800 text-[11px] truncate">{{ $b['pagu_formatted'] }}</span>
              </div>
              <div class="flex items-baseline justify-between gap-2 font-mono">
                <span class="text-[10px] text-slate-400 font-sans font-semibold shrink-0">Real:</span>
                <span class="font-black {{ $b['text_color'] }} text-[11px] truncate">{{ $b['realisasi_formatted'] }}</span>
              </div>
            </div>
          </div>

          <!-- Progress Bar & Persentase Capaian -->
          <div class="mt-3 pt-2.5 border-t border-slate-100">
            <div class="flex items-center justify-between text-xs mb-1.5 font-mono">
              <span class="text-[10px] text-slate-500 font-sans font-medium">Realisasi</span>
              <span class="font-black {{ $b['text_color'] }} text-xs">{{ number_format($b['persen'], 2, ',', '.') }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
              <div class="{{ $b['bar_color'] }} h-1.5 rounded-full transition-all duration-500" style="width: {{ min($b['persen'], 100) }}%"></div>
            </div>
          </div>

        </div>
      @endforeach
    </div>

    <!-- MODE 2: Grafik Komparasi Interaktif Pagu vs Realisasi 10 Jenis Belanja -->
    <div id="jenisBelanjaChartView" class="hidden bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-sm space-y-4">
      <!-- Insight Strip Atas Grafik -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-3 border-b border-slate-100">
        <div class="flex flex-wrap items-center gap-2 text-[11px]">
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">
            🏆 Tertinggi: 66 - Dana Desa (99,15%)
          </span>
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 font-bold">
            ⚡ Perlu Akselerasi: 53 - Belanja Modal (47,14%)
          </span>
        </div>
        <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-500">
          <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-slate-200 border border-slate-300"></span> Pagu (Triliun)</span>
          <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-blue-600"></span> Realisasi (Triliun)</span>
        </div>
      </div>

      <!-- Canvas Container -->
      <div class="relative w-full h-72 sm:h-80 lg:h-96">
        <canvas id="jenisBelanjaCompareChart"></canvas>
      </div>
    </div>
  </div>

  <!-- 4. SECTION TABEL DATA REALISASI SATKER (PERSIS GAMBAR 2 DENGAN DESAIN MODERN UPGRADE) -->
  <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
    
    <!-- Controls Toolbar (Search Input & Quick Filter Pills) -->
    <div class="p-3.5 sm:p-4 border-b border-slate-200/80 bg-slate-50/70 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
      
      <!-- Live Search Box (Persis Gambar 2) -->
      <div class="w-full lg:w-96 relative">
        <input type="text" 
               id="tableSearchInput" 
               onkeyup="filterSatkerTable()"
               placeholder="🔍 Cari Satker, Kode DIPA, atau Lembaga..." 
               class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-12 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all font-medium shadow-xs">
        <span class="absolute right-2.5 top-2.5 text-[10px] font-mono text-slate-400 px-1.5 py-0.5 rounded border border-slate-200 bg-slate-50">/</span>
      </div>

      <!-- Quick Filter Pills & Reset -->
      <div class="flex items-center gap-1.5 text-xs w-full lg:w-auto overflow-x-auto pb-1 lg:pb-0 no-scrollbar sm:flex-wrap">
        <button onclick="resetAllFilters()" id="btnFilterAll" class="shrink-0 filter-pill-btn px-3 py-1.5 bg-blue-600 text-white rounded-lg font-bold shadow-xs transition-all border border-blue-600">
          Semua (311 Satker)
        </button>
        <button onclick="filterByCriteria('kl')" class="shrink-0 filter-pill-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-slate-100 rounded-lg font-semibold transition-all border border-slate-200">
          Satker K/L (308)
        </button>
        <button onclick="filterByCriteria('bun')" class="shrink-0 filter-pill-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-slate-100 rounded-lg font-semibold transition-all border border-slate-200">
          Satker BUN (3)
        </button>
        <button onclick="filterByCriteria('blokir')" class="shrink-0 filter-pill-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-rose-50 hover:text-rose-600 rounded-lg font-semibold transition-all border border-slate-200">
          Ada Pagu Blokir
        </button>
        <button onclick="filterByCriteria('tinggi')" class="shrink-0 filter-pill-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-lg font-semibold transition-all border border-slate-200">
          Serapan &ge; 90%
        </button>
        
        <a href="{{ route('satker.export') }}" class="shrink-0 sm:ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-semibold shadow-xs transition-all">
          <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
          </svg>
          <span>Unduh CSV</span>
        </a>
      </div>

    </div>

    <!-- Active Filter Info Banner -->
    <div id="filterInfoBanner" class="hidden px-4 py-2 bg-blue-50 border-b border-blue-100 flex items-center justify-between text-xs text-blue-800">
      <span id="filterInfoText">Menampilkan data satker yang difilter...</span>
      <button onclick="resetAllFilters()" class="text-[11px] font-bold text-blue-600 hover:underline">Reset Filter ✕</button>
    </div>

    <!-- Data Table (Persis Kolom Gambar 2: NO, KODE SATKER, NAMA SATKER, PAGU, REALISASI, % REALISASI) -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse" id="satkerDataTable">
        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-extrabold border-b border-slate-200/90 text-[10px]">
          <tr>
            <th class="py-3 px-3.5 w-12 text-center select-none">NO</th>
            <th class="py-3 px-3.5 w-28 select-none">KODE SATKER</th>
            <th class="py-3 px-3.5 select-none">NAMA SATKER</th>
            <th onclick="sortTable('pagu')" class="py-3 px-3.5 text-right w-44 cursor-pointer hover:text-blue-600 transition-colors select-none">
              <span>PAGU</span>
              <span id="sortIndicator-pagu" class="text-[9px] ml-0.5 text-slate-400">↕</span>
            </th>
            <th onclick="sortTable('realisasi')" class="py-3 px-3.5 text-right w-44 cursor-pointer hover:text-blue-600 transition-colors select-none">
              <span>REALISASI</span>
              <span id="sortIndicator-realisasi" class="text-[9px] ml-0.5 text-slate-400">↕</span>
            </th>
            <th onclick="sortTable('persen')" class="py-3 px-3.5 text-center w-36 cursor-pointer hover:text-blue-600 transition-colors select-none">
              <span>% REALISASI</span>
              <span id="sortIndicator-persen" class="text-[9px] ml-0.5 text-slate-400">↕</span>
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700 font-sans" id="satkerTableBody">
          @php $no = 1; @endphp
          @foreach ($satkerList as $s)
            @php
              $pagu = $s['total_pagu'] ?? 0;
              $realisasi = $s['total_realisasi'] ?? 0;
              $persen = $pagu > 0 ? round(($realisasi / $pagu) * 100, 2) : 0;
              $blokir = $s['pagu_blokir'] ?? 0;
              $isBun = in_array($s['kode_satker'] ?? '', ['500133', '700254', '600061']);
            @endphp
            <tr onclick="openSatkerDetail(this)" 
                class="hover:bg-blue-50/50 transition-colors satker-row cursor-pointer group" 
                data-satker="{{ strtolower(($s['kode_satker'] ?? '') . ' ' . ($s['nama_satker'] ?? '') . ' ' . ($s['nama_kl'] ?? '') . ' ' . ($s['wilayah'] ?? '')) }}"
                data-kode="{{ $s['kode_satker'] ?? '-' }}"
                data-nama="{{ $s['nama_satker'] ?? '-' }}"
                data-kl="{{ $s['nama_kl'] ?? '-' }}"
                data-wilayah="{{ $s['wilayah'] ?? 'Provinsi Aceh' }}"
                data-pagu="{{ $pagu }}"
                data-realisasi="{{ $realisasi }}"
                data-persen="{{ $persen }}"
                data-blokir="{{ $blokir }}"
                data-pagu-fmt="Rp {{ number_format($pagu, 0, ',', '.') }}"
                data-real-fmt="Rp {{ number_format($realisasi, 0, ',', '.') }}"
                data-blokir-fmt="Rp {{ number_format($blokir, 0, ',', '.') }}"
                data-sisa-fmt="Rp {{ number_format(max(0, $pagu - $realisasi), 0, ',', '.') }}"
                data-is-bun="{{ $isBun ? 'true' : 'false' }}">
              
              <!-- Kolom NO -->
              <td class="py-3.5 px-3.5 text-center font-mono text-slate-400 font-semibold group-hover:text-blue-600">
                {{ $s['no'] ?? $no++ }}
              </td>

              <!-- Kolom KODE SATKER -->
              <td class="py-3.5 px-3.5 font-mono">
                <span class="px-2 py-0.5 rounded-md font-bold text-xs bg-slate-100 group-hover:bg-blue-100 group-hover:text-blue-800 border border-slate-200 text-slate-900 shadow-2xs transition-colors">
                  {{ $s['kode_satker'] ?? '-' }}
                </span>
              </td>

              <!-- Kolom NAMA SATKER (Line 1: Nama Satker Bold, Line 2: KL + Wilayah, Line 3: Blokir Merah jika ada) -->
              <td class="py-3.5 px-3.5">
                <div class="space-y-0.5">
                  <div class="font-extrabold text-slate-900 group-hover:text-blue-700 text-[12px] leading-snug transition-colors">
                    {{ $s['nama_satker'] ?? '-' }}
                  </div>
                  <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1.5">
                    <span>{{ $s['nama_kl'] ?? '-' }}</span>
                    @if(!empty($s['wilayah']))
                      <span>•</span>
                      <span class="font-semibold text-slate-600">{{ $s['wilayah'] }}</span>
                    @endif
                  </div>
                  @if($blokir > 0)
                    <div class="text-[10px] font-mono font-bold text-rose-600 flex items-center gap-1 pt-0.5">
                      <span class="w-1.5 h-1.5 rounded-full bg-rose-500 inline-block"></span>
                      <span>Blokir:</span>
                      <span>Rp {{ number_format($blokir, 0, ',', '.') }}</span>
                    </div>
                  @endif
                </div>
              </td>

              <!-- Kolom PAGU -->
              <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 text-xs">
                Rp {{ number_format($pagu, 0, ',', '.') }}
              </td>

              <!-- Kolom REALISASI (Persis Warna Hijau Tebal di Gambar 2) -->
              <td class="py-3.5 px-3.5 text-right font-mono font-bold text-emerald-600 text-xs">
                Rp {{ number_format($realisasi, 0, ',', '.') }}
              </td>

              <!-- Kolom % REALISASI (Persis Gambar 2: Nilai % + Horizontal Blue Progress Bar) -->
              <td class="py-3.5 px-3.5 text-center">
                <div class="flex flex-col items-center gap-1 w-full max-w-[100px] mx-auto">
                  <div class="flex items-center gap-1">
                    <span class="font-mono font-bold text-xs text-slate-800">
                      {{ number_format($persen, 2, ',', '.') }}%
                    </span>
                    @if($persen >= 90)
                      <span class="text-[8px] font-bold px-1 py-0.2 rounded bg-emerald-100 text-emerald-800">Baik</span>
                    @elseif($persen < 50 && $pagu > 1000)
                      <span class="text-[8px] font-bold px-1 py-0.2 rounded bg-rose-100 text-rose-800">Rendah</span>
                    @endif
                  </div>
                  <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min($persen, 100) }}%"></div>
                  </div>
                </div>
              </td>

            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Table Footer (Row Counter & Entity Note) -->
    <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500 font-mono">
      <span>Menampilkan: <strong id="visibleRowCount" class="text-slate-900 font-bold">{{ count($satkerList) }}</strong> Satuan Kerja</span>
      <span>Kanwil DJPb Provinsi Aceh · Indonesian Treasury</span>
    </div>

  </div>

  <!-- 5. SLIDE-OVER / MODAL DETAIL SATKER (MODERN INSPECTOR) -->
  <div id="satkerDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="relative w-full max-w-xl max-h-[92vh] flex flex-col bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all animate-in fade-in zoom-in-95 duration-150 my-auto">
      
      <!-- Modal Header -->
      <div class="p-4 sm:p-5 bg-gradient-to-r from-slate-900 to-blue-950 text-white flex items-start justify-between shrink-0">
        <div>
          <div class="flex items-center gap-2 mb-1.5">
            <span id="modalSatkerKode" class="px-2 py-0.5 rounded-md font-mono text-xs font-black bg-blue-500/20 text-blue-300 border border-blue-400/30">
              000000
            </span>
            <span id="modalSatkerBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
              Optimal
            </span>
          </div>
          <h2 id="modalSatkerNama" class="text-sm sm:text-base font-black text-white leading-snug">
            Nama Satuan Kerja
          </h2>
          <p id="modalSatkerKl" class="text-xs text-slate-300 mt-1 font-medium">
            Kementerian / Lembaga · Wilayah
          </p>
        </div>
        <button onclick="closeSatkerDetail()" class="p-1 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Modal Body: 4 KPI Cards -->
      <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">PAGU DIPA</div>
            <div id="modalSatkerPagu" class="text-sm sm:text-base font-black text-slate-900 font-mono mt-0.5 break-all sm:break-normal">Rp 0</div>
          </div>
          <div class="p-3 rounded-xl bg-emerald-50/60 border border-emerald-200/80">
            <div class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider">REALISASI SP2D</div>
            <div id="modalSatkerReal" class="text-sm sm:text-base font-black text-emerald-600 font-mono mt-0.5 break-all sm:break-normal">Rp 0</div>
          </div>
          <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-200/80">
            <div class="text-[10px] font-bold text-amber-700 uppercase tracking-wider">SISA DIPA TERSEDIA</div>
            <div id="modalSatkerSisa" class="text-sm sm:text-base font-black text-amber-600 font-mono mt-0.5 break-all sm:break-normal">Rp 0</div>
          </div>
          <div class="p-3 rounded-xl bg-rose-50/60 border border-rose-200/80">
            <div class="text-[10px] font-bold text-rose-700 uppercase tracking-wider">PAGU TERBLOKIR</div>
            <div id="modalSatkerBlokir" class="text-sm sm:text-base font-black text-rose-600 font-mono mt-0.5 break-all sm:break-normal">Rp 0</div>
          </div>
        </div>

        <!-- Progress Bar Capaian -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1.5">
          <div class="flex items-center justify-between text-xs font-mono">
            <span class="text-slate-500 font-sans font-semibold">Tingkat Penyerapan Anggaran:</span>
            <span id="modalSatkerPersen" class="font-black text-slate-900 text-sm">0%</span>
          </div>
          <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
            <div id="modalSatkerBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
          </div>
        </div>

        <!-- Akun Belanja Breakdown Simulation -->
        <div class="space-y-2">
          <div class="text-xs font-bold text-slate-800 uppercase tracking-wider">Estimasi Alokasi Akun APBN</div>
          <div class="space-y-1.5 text-xs font-mono">
            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
              <span class="font-sans text-slate-600">51 - Belanja Pegawai</span>
              <span class="font-bold text-blue-700">Gaji & Tunjangan Terpadu</span>
            </div>
            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
              <span class="font-sans text-slate-600">52 - Belanja Barang</span>
              <span class="font-bold text-amber-600">Operasional & Perjalanan Dinas</span>
            </div>
            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
              <span class="font-sans text-slate-600">53 - Belanja Modal</span>
              <span class="font-bold text-teal-600">Fasilitas, Gedung & Peralatan</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="p-3.5 sm:p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
        <span class="text-[10px] sm:text-[11px] text-slate-400 font-mono text-center sm:text-left">Sinkronisasi SP2D Online · Kanwil DJPb Provinsi Aceh</span>
        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
          <button onclick="copySatkerInfo()" id="btnCopySatker" class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-xs font-bold text-slate-700 transition-colors shadow-2xs text-center">
            Salin Info Satker
          </button>
          <button onclick="closeSatkerDetail()" class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-xs font-bold text-white transition-colors shadow-2xs text-center">
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
  let activeFilterType = 'all';

  // Live Search Filter
  function filterSatkerTable() {
    const input = document.getElementById('tableSearchInput');
    const keyword = input ? input.value.toLowerCase().trim() : '';
    const rows = document.querySelectorAll('.satker-row');
    let visible = 0;

    rows.forEach(r => {
      const text = r.getAttribute('data-satker') || '';
      let matchSearch = (keyword === '' || text.includes(keyword));
      let matchCriteria = checkRowCriteria(r, activeFilterType);

      if (matchSearch && matchCriteria) {
        r.style.display = '';
        visible++;
      } else {
        r.style.display = 'none';
      }
    });

    const countEl = document.getElementById('visibleRowCount');
    if (countEl) countEl.textContent = visible;
  }

  function checkRowCriteria(row, criteria) {
    if (criteria === 'all') return true;
    if (criteria === 'kl') return row.getAttribute('data-is-bun') === 'false';
    if (criteria === 'bun') return row.getAttribute('data-is-bun') === 'true';
    if (criteria === 'blokir') return parseFloat(row.getAttribute('data-blokir') || 0) > 0;
    if (criteria === 'tinggi') return parseFloat(row.getAttribute('data-persen') || 0) >= 90;
    return true;
  }

  function filterByCriteria(criteria) {
    activeFilterType = criteria;

    // Update Pill Buttons Styling
    document.querySelectorAll('.filter-pill-btn').forEach(btn => {
      btn.className = 'filter-pill-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-slate-100 rounded-lg font-semibold transition-all border border-slate-200';
    });
    event.currentTarget.className = 'filter-pill-btn px-3 py-1.5 bg-blue-600 text-white rounded-lg font-bold shadow-xs transition-all border border-blue-600';

    // Update Banner
    const banner = document.getElementById('filterInfoBanner');
    const text = document.getElementById('filterInfoText');
    if (banner && text) {
      if (criteria !== 'all') {
        banner.classList.remove('hidden');
        text.textContent = 'Memfilter data: ' + criteria.toUpperCase();
      } else {
        banner.classList.add('hidden');
      }
    }

    filterSatkerTable();
  }

  function filterTableByAkun(kodeAkun, cardEl) {
    // Highlight Card
    document.querySelectorAll('.jenis-belanja-card').forEach(c => {
      c.classList.remove('ring-2', 'ring-blue-600', 'bg-blue-50/40');
    });
    const targetCard = cardEl || document.querySelector(`.jenis-belanja-card[data-akun="${kodeAkun}"]`);
    if (targetCard) {
      targetCard.classList.add('ring-2', 'ring-blue-600', 'bg-blue-50/40');
    }

    const banner = document.getElementById('filterInfoBanner');
    const text = document.getElementById('filterInfoText');
    if (banner && text) {
      banner.classList.remove('hidden');
      text.textContent = `Menampilkan rincian Satker untuk Akun ${kodeAkun}`;
    }

    // Scroll to table smoothly
    const table = document.getElementById('satkerDataTable');
    if (table) {
      table.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function resetAllFilters() {
    activeFilterType = 'all';
    const input = document.getElementById('tableSearchInput');
    if (input) input.value = '';

    document.querySelectorAll('.jenis-belanja-card').forEach(c => {
      c.classList.remove('ring-2', 'ring-blue-600', 'bg-blue-50/40');
    });

    document.querySelectorAll('.filter-pill-btn').forEach(btn => {
      btn.className = 'shrink-0 filter-pill-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-slate-100 rounded-lg font-semibold transition-all border border-slate-200';
    });
    const btnAll = document.getElementById('btnFilterAll');
    if (btnAll) btnAll.className = 'shrink-0 filter-pill-btn px-3 py-1.5 bg-blue-600 text-white rounded-lg font-bold shadow-xs transition-all border border-blue-600';

    const banner = document.getElementById('filterInfoBanner');
    if (banner) banner.classList.add('hidden');

    filterSatkerTable();
  }

  // Keyboard shortcut '/' to search
  document.addEventListener('keydown', function(e) {
    if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
      e.preventDefault();
      const input = document.getElementById('tableSearchInput');
      if (input) input.focus();
    }
    if (e.key === 'Escape') {
      closeSatkerDetail();
    }
  });

  // Modal Satker Detail Inspector
  let currentSelectedSatker = null;

  function openSatkerDetail(row) {
    const kode = row.getAttribute('data-kode') || '';
    const nama = row.getAttribute('data-nama') || '';
    const kl = row.getAttribute('data-kl') || '';
    const wilayah = row.getAttribute('data-wilayah') || '';
    const paguFmt = row.getAttribute('data-pagu-fmt') || 'Rp 0';
    const realFmt = row.getAttribute('data-real-fmt') || 'Rp 0';
    const blokirFmt = row.getAttribute('data-blokir-fmt') || 'Rp 0';
    const sisaFmt = row.getAttribute('data-sisa-fmt') || 'Rp 0';
    const persen = parseFloat(row.getAttribute('data-persen') || 0);

    currentSelectedSatker = { kode, nama, kl, wilayah, paguFmt, realFmt, blokirFmt, sisaFmt, persen };

    document.getElementById('modalSatkerKode').textContent = kode;
    document.getElementById('modalSatkerNama').textContent = nama;
    document.getElementById('modalSatkerKl').textContent = `${kl} · ${wilayah}`;
    document.getElementById('modalSatkerPagu').textContent = paguFmt;
    document.getElementById('modalSatkerReal').textContent = realFmt;
    document.getElementById('modalSatkerSisa').textContent = sisaFmt;
    document.getElementById('modalSatkerBlokir').textContent = blokirFmt;
    document.getElementById('modalSatkerPersen').textContent = `${persen.toFixed(2)}%`;
    document.getElementById('modalSatkerBar').style.width = `${Math.min(persen, 100)}%`;

    const badge = document.getElementById('modalSatkerBadge');
    if (persen >= 90) {
      badge.textContent = 'Sangat Baik (≥90%)';
      badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
    } else if (persen >= 75) {
      badge.textContent = 'Optimal (75-90%)';
      badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30';
    } else if (persen >= 50) {
      badge.textContent = 'Perhatian (50-75%)';
      badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30';
    } else {
      badge.textContent = 'Perlu Percepatan (<50%)';
      badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30';
    }

    const modal = document.getElementById('satkerDetailModal');
    if (modal) modal.classList.remove('hidden');
  }

  function closeSatkerDetail() {
    const modal = document.getElementById('satkerDetailModal');
    if (modal) modal.classList.add('hidden');
  }

  function copySatkerInfo() {
    if (!currentSelectedSatker) return;
    const txt = `[PARAS KANWIL DJPB PROVINSI ACEH]\nSatker: ${currentSelectedSatker.kode} - ${currentSelectedSatker.nama}\nK/L: ${currentSelectedSatker.kl}\nPagu DIPA: ${currentSelectedSatker.paguFmt}\nRealisasi: ${currentSelectedSatker.realFmt} (${currentSelectedSatker.persen.toFixed(2)}%)\nSisa DIPA: ${currentSelectedSatker.sisaFmt}\nPagu Blokir: ${currentSelectedSatker.blokirFmt}`;
    navigator.clipboard.writeText(txt).then(() => {
      const btn = document.getElementById('btnCopySatker');
      if (btn) {
        btn.textContent = '✓ Tersalin!';
        setTimeout(() => btn.textContent = 'Salin Info Satker', 2000);
      }
    });
  }

  // Dynamic Column Sorting
  let currentSort = { column: '', direction: 'asc' };

  function sortTable(column) {
    const tbody = document.getElementById('satkerTableBody');
    if (!tbody) return;

    const rows = Array.from(tbody.querySelectorAll('.satker-row'));
    const direction = (currentSort.column === column && currentSort.direction === 'asc') ? 'desc' : 'asc';
    currentSort = { column, direction };

    // Reset indicators
    ['pagu', 'realisasi', 'persen'].forEach(c => {
      const el = document.getElementById(`sortIndicator-${c}`);
      if (el) el.textContent = '↕';
    });
    const activeEl = document.getElementById(`sortIndicator-${column}`);
    if (activeEl) activeEl.textContent = direction === 'asc' ? '▲' : '▼';

    rows.sort((a, b) => {
      let valA = parseFloat(a.getAttribute(`data-${column}`) || 0);
      let valB = parseFloat(b.getAttribute(`data-${column}`) || 0);
      return direction === 'asc' ? valA - valB : valB - valA;
    });

    rows.forEach(r => tbody.appendChild(r));
  }

  // Close modal on outside click
  document.getElementById('satkerDetailModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeSatkerDetail();
  });

  // =========================================================================
  // TOGGLE VIEW: 10 KARTU JENIS BELANJA vs GRAFIK KOMPARASI INTERAKTIF
  // =========================================================================
  const jenisBelanjaData = @json($jenisBelanja);
  let jenisBelanjaChartInstance = null;

  function switchJenisBelanjaView(mode) {
    const cardView = document.getElementById('jenisBelanjaCardView');
    const chartView = document.getElementById('jenisBelanjaChartView');
    const btnCards = document.getElementById('btnViewCards');
    const btnChart = document.getElementById('btnViewChart');

    if (mode === 'chart') {
      if (cardView) cardView.classList.add('hidden');
      if (chartView) chartView.classList.remove('hidden');

      if (btnChart) {
        btnChart.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-blue-700 shadow-xs transition-all';
      }
      if (btnCards) {
        btnCards.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 transition-all';
      }

      if (!jenisBelanjaChartInstance) {
        renderJenisBelanjaChart();
      } else {
        jenisBelanjaChartInstance.resize();
      }
    } else {
      if (chartView) chartView.classList.add('hidden');
      if (cardView) cardView.classList.remove('hidden');

      if (btnCards) {
        btnCards.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-blue-700 shadow-xs transition-all';
      }
      if (btnChart) {
        btnChart.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 transition-all';
      }
    }
  }

  function renderJenisBelanjaChart() {
    const canvas = document.getElementById('jenisBelanjaCompareChart');
    if (!canvas || typeof Chart === 'undefined') return;

    const shortLabels = {
      '51': '51 Pegawai',
      '52': '52 Barang',
      '53': '53 Modal',
      '57': '57 Bansos',
      '61': '61 DBH',
      '62': '62 DAU',
      '63': '63 DAK Fisik',
      '64': '64 Otsus/DID',
      '65': '65 DAK Non-F',
      '66': '66 Dana Desa'
    };

    const labels = jenisBelanjaData.map(item => shortLabels[item.kode] || item.nama);
    const paguT = jenisBelanjaData.map(item => +(item.pagu / 1e12).toFixed(3));
    const realT = jenisBelanjaData.map(item => +(item.realisasi / 1e12).toFixed(3));
    const barColors = jenisBelanjaData.map(item => {
      if (item.persen >= 90) return '#10b981'; // emerald-500
      if (item.persen >= 60) return '#2563eb'; // blue-600
      if (item.persen >= 50) return '#f59e0b'; // amber-500
      return '#f43f5e'; // rose-500
    });

    const pluginsList = typeof ChartDataLabels !== 'undefined' ? [ChartDataLabels] : [];

    jenisBelanjaChartInstance = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Pagu Anggaran',
            data: paguT,
            backgroundColor: 'rgba(203, 213, 225, 0.75)',
            borderColor: '#94a3b8',
            borderWidth: 1,
            borderRadius: 6,
            borderSkipped: false,
            barPercentage: 0.85,
            categoryPercentage: 0.72,
            datalabels: { display: false }
          },
          {
            label: 'Realisasi SP2D',
            data: realT,
            backgroundColor: barColors,
            borderRadius: 6,
            borderSkipped: false,
            barPercentage: 0.85,
            categoryPercentage: 0.72,
            datalabels: {
              display: true,
              anchor: 'end',
              align: 'top',
              offset: 2,
              color: '#0f172a',
              font: { family: '"Plus Jakarta Sans", sans-serif', size: 10, weight: '800' },
              formatter: (value, ctx) => {
                const item = jenisBelanjaData[ctx.dataIndex];
                return item ? item.persen.toFixed(1).replace('.', ',') + '%' : '';
              }
            }
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        onClick: (evt, elements) => {
          if (elements && elements.length > 0) {
            const idx = elements[0].index;
            const item = jenisBelanjaData[idx];
            if (item) {
              filterTableByAkun(item.kode, null);
            }
          }
        },
        onHover: (event, chartElement) => {
          event.native.target.style.cursor = chartElement.length ? 'pointer' : 'default';
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
              title: (items) => {
                const item = jenisBelanjaData[items[0].dataIndex];
                return item ? item.nama : '';
              },
              label: (ctx) => {
                const item = jenisBelanjaData[ctx.dataIndex];
                if (!item) return '';
                if (ctx.datasetIndex === 0) {
                  return ` Pagu      : ${item.pagu_formatted}`;
                }
                return ` Realisasi : ${item.realisasi_formatted} (${item.persen.toFixed(2).replace('.', ',')}%)`;
              },
              afterBody: (items) => {
                const item = jenisBelanjaData[items[0].dataIndex];
                if (!item) return '';
                return `\n Klik batang untuk menyaring tabel Satker`;
              }
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              color: '#334155',
              font: { family: '"Plus Jakarta Sans", sans-serif', size: 10, weight: '700' }
            }
          },
          y: {
            beginAtZero: true,
            suggestedMax: 13.5,
            grid: { color: '#f1f5f9' },
            ticks: {
              color: '#64748b',
              font: { family: 'monospace', size: 10 },
              callback: (val) => 'Rp ' + val + ' T'
            }
          }
        }
      },
      plugins: pluginsList
    });
  }
</script>
@endpush
