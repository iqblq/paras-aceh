@extends('layouts.app')

@section('title', 'Dashboard Utama - SIPANTAS Kanwil DJPb Provinsi Aceh')

@section('content')
<div class="space-y-6">

  <!-- HEADER BRANDING BANNER (KANWIL DJPB PROVINSI ACEH) -->
  <div class="bg-white rounded-2xl border border-slate-200/90 p-3.5 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
    <div class="flex items-center gap-3">
      <!-- Logo InTress Kanwil DJPb Provinsi Aceh -->
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white p-1 border border-slate-200/90 flex items-center justify-center shadow-xs shrink-0 overflow-hidden">
          <img src="{{ asset('images/paras.png') }}" alt="Logo SIPANTAS" class="w-full h-full object-contain">
        </div>
        <div>
          <div class="flex items-center gap-1.5 leading-none">
            <span class="text-xs sm:text-sm font-black text-[#1e40af] tracking-tight">Kanwil DJPb Provinsi Aceh</span>
          </div>
          <div class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider mt-1">Indonesian Treasury · Portal SIPANTAS Regional</div>
        </div>
      </div>
    </div>

    <!-- Title Kanan -->
    <div class="text-left sm:text-right border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-100">
      <h1 class="text-sm sm:text-lg font-black text-slate-900 tracking-tight">Executive Dashboard Perbendaharaan</h1>
      <p class="text-[10px] sm:text-[11px] text-slate-500 font-mono">Konsolidasi {{ count($regionalMatrix['kppns'] ?? []) ?: 7 }} KPPN Se-Aceh (Banda Aceh + 6 KPPN Daerah · {{ $regionalMatrix['ALL']['all']['satker_count'] ?? 789 }} Satker K/L &amp; BA BUN)</p>
    </div>
  </div>

  @php
    $heroInit = $regionalMatrix['ALL']['all'] ?? [
      'pagu_formatted' => $kpi['total_pagu_formatted'],
      'realisasi_formatted' => $kpi['total_realisasi_formatted'],
      'sisa_formatted' => $kpi['sisa_pagu_formatted'],
      'blokir_formatted' => $kpi['total_blokir_formatted'],
      'persen_realisasi' => $kpi['persen_realisasi'],
      'persen_realisasi_formatted' => number_format($kpi['persen_realisasi'], 2, ',', '.') . '%',
      'persen_sisa' => $kpi['persen_sisa'],
      'persen_sisa_formatted' => number_format($kpi['persen_sisa'], 2, ',', '.') . '%',
      'persen_blokir' => $kpi['persen_blokir'],
      'satker_count' => $kpi['total_satker_count'] ?? 789,
    ];
    $heroKlInit = $regionalMatrix['ALL']['kl'] ?? $heroInit;
    $heroBunInit = $regionalMatrix['ALL']['bun'] ?? $heroInit;
  @endphp

  <!-- SECTION 1: HERO EXECUTIVE APBN BAR (MIDNIGHT TREASURY NAVY) -->
  <div class="w-full bg-gradient-to-br from-[#0b132b] via-[#1c2541] to-[#0f172a] border border-slate-700/80 text-white rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-xl relative overflow-hidden transition-all duration-300">

    <!-- Top Controls: Scope (Semua APBN / K/L / BA BUN) + Filter 7 KPPN -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2.5 pb-3.5 mb-4 border-b border-slate-700/70">
      <div class="flex flex-wrap items-center gap-1.5 bg-slate-900/70 border border-slate-700/80 rounded-xl p-1">
        <button type="button" id="btnHeroScopeAll" onclick="setHeroScope('all')"
                class="px-2.5 sm:px-3 py-1.5 rounded-lg text-[11px] sm:text-xs font-bold bg-blue-600 text-white shadow-xs transition-all">
          Semua APBN (K/L + TKD) · {{ $heroInit['satker_count'] }} Satker
        </button>
        <button type="button" id="btnHeroScopeKl" onclick="setHeroScope('kl')"
                class="px-2.5 sm:px-3 py-1.5 rounded-lg text-[11px] sm:text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition-all">
          Belanja K/L ({{ $heroKlInit['satker_count'] }})
        </button>
        <button type="button" id="btnHeroScopeBun" onclick="setHeroScope('bun')"
                class="px-2.5 sm:px-3 py-1.5 rounded-lg text-[11px] sm:text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition-all">
          BA BUN / TKD ({{ $heroBunInit['satker_count'] }})
        </button>
      </div>

      <div class="flex items-center gap-2">
        <span class="text-[11px] text-slate-400 font-semibold hidden sm:inline">Wilayah KPPN:</span>
        <select id="heroKppnSelect" onchange="setHeroRegion(this.value)"
                class="bg-slate-900/90 border border-slate-600/80 text-white text-xs font-bold rounded-xl px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
          <option value="ALL">Semua 7 KPPN Se-Aceh (Konsolidasi)</option>
          @foreach(($regionalMatrix['kppns'] ?? []) as $kKey => $kItem)
            <option value="{{ $kKey }}">{{ $kItem['nama'] }} ({{ $kItem['all']['satker_count'] }} Satker)</option>
          @endforeach
        </select>
      </div>
    </div>
    
    <!-- Top Row: Total Pagu on Left, Realisasi & Sisa Pagu Cards on Right -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-4">
      
      <!-- Left: Total Pagu -->
      <div>
        <div class="flex items-center gap-1.5 text-slate-300 text-xs sm:text-sm font-medium mb-1">
          <span id="heroScopeTitle" class="tracking-wide uppercase text-[11px] font-bold text-slate-300">KONSOLIDASI APBN ACEH (K/L + BA BUN · 7 KPPN) · Total Pagu DIPA</span>
          <span class="inline-flex items-center justify-center w-4 h-4 rounded-full border border-slate-500 text-[10px] font-serif cursor-help text-slate-400" title="Total Pagu DIPA gabungan Satker K/L dan Satker Penyalur TKD (BA BUN) seluruh 7 KPPN se-Aceh">ⓘ</span>
        </div>
        <div id="heroTotalPagu" class="text-xl sm:text-3xl lg:text-4xl font-black text-white font-mono tracking-tight break-all sm:break-normal">
          {{ $heroInit['pagu_formatted'] }}
        </div>
        <div id="heroSubSummary" class="text-[11px] text-slate-400 font-mono mt-1">
          Belanja K/L: {{ $heroKlInit['pagu_formatted'] }} + BA BUN/TKD: {{ $heroBunInit['pagu_formatted'] }}
        </div>
      </div>

      <!-- Right: 2 Distinct Metric Badges -->
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3 w-full lg:w-auto">
        
        <!-- Card Realisasi Belanja (Treasury Blue Card) -->
        <div class="bg-slate-900/90 text-white rounded-xl sm:rounded-2xl px-4 py-2.5 sm:px-5 sm:py-3 border border-blue-500/30 shadow-md flex-1 sm:flex-initial min-w-0 sm:min-w-[215px] xl:min-w-[230px] transition-all">
          <div class="text-left sm:text-right text-[10px] sm:text-xs text-blue-300 font-semibold mb-0.5">
            Realisasi Belanja
          </div>
          <div id="heroRealVal" class="text-left sm:text-right text-base sm:text-lg lg:text-xl font-black text-white font-mono tracking-tight whitespace-nowrap">
            {{ $heroInit['realisasi_formatted'] }}
          </div>
        </div>

        <!-- Card Sisa Pagu (Treasury Gold/Amber Card) -->
        <div class="bg-slate-900/90 text-white rounded-xl sm:rounded-2xl px-4 py-2.5 sm:px-5 sm:py-3 border border-amber-500/40 shadow-md flex-1 sm:flex-initial min-w-0 sm:min-w-[215px] xl:min-w-[230px] transition-all">
          <div class="text-left sm:text-right text-[10px] sm:text-xs text-amber-400 font-semibold mb-0.5">
            Sisa Pagu
          </div>
          <div id="heroSisaVal" class="text-left sm:text-right text-base sm:text-lg lg:text-xl font-black text-amber-300 font-mono tracking-tight whitespace-nowrap">
            {{ $heroInit['sisa_formatted'] }}
          </div>
        </div>

      </div>

    </div>

    <!-- Middle Row: Percentage Headers -->
    <div class="flex items-center justify-between text-xs sm:text-sm font-medium mb-1.5 px-0.5">
      <div class="flex items-center gap-1.5">
        <span class="text-slate-300">Total Realisasi</span>
        <span id="heroRealPctText" class="text-sm sm:text-lg font-black text-blue-400 font-mono ml-1">{{ $heroInit['persen_realisasi_formatted'] }}</span>
      </div>
      <div class="flex items-center gap-1.5">
        <span class="text-slate-300">Sisa Pagu</span>
        <span id="heroSisaPctText" class="text-sm sm:text-lg font-black text-amber-400 font-mono ml-1">{{ $heroInit['persen_sisa_formatted'] }}</span>
      </div>
    </div>

    <!-- Two-Tone Split Progress Bar -->
    <div class="w-full bg-slate-900/80 border border-slate-700/60 rounded-xl h-6 sm:h-7 p-0.5 flex overflow-hidden shadow-inner mb-3 sm:mb-4">
      <!-- Realisasi Segment (Treasury Deep Blue) -->
      <div id="heroBarReal" 
           class="bg-blue-600 rounded-l-lg h-full flex items-center justify-end pr-2.5 sm:pr-3 text-white text-xs sm:text-sm font-black transition-all duration-700" 
           style="width: {{ $heroInit['persen_realisasi'] }}%;">
        <span id="heroBarRealLabel">{{ $heroInit['persen_realisasi_formatted'] }}</span>
      </div>
      <!-- Sisa Segment (Treasury Warm Gold) -->
      <div id="heroBarSisa" 
           class="bg-amber-500/80 rounded-r-lg h-full transition-all duration-700" 
           style="width: {{ $heroInit['persen_sisa'] }}%;">
      </div>
    </div>

    <!-- Bottom Row: Realisasi Cutoff info on Left, Timestamp on Right -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2.5 border-t border-slate-700/70 text-[11px] sm:text-xs text-slate-300">
      
      <!-- Left: Pagu Blokir Info -->
      <div class="flex items-center gap-2">
        <div class="w-5 h-5 rounded-md bg-rose-900/70 border border-rose-500/30 flex items-center justify-center shrink-0">
          <svg class="w-3 h-3 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
          </svg>
        </div>
        <div class="flex items-baseline gap-1.5 flex-wrap">
          <span class="text-slate-300">Pagu Blokir</span>
          <span class="inline-flex items-center justify-center w-3.5 h-3.5 rounded-full border border-slate-500 text-[9px] font-serif cursor-help text-slate-400" title="Total Pagu DIPA berstatus blokir">ⓘ</span>
          <span id="heroCutoffVal" class="font-black text-rose-300 font-mono text-xs sm:text-sm">
            {{ $heroInit['blokir_formatted'] }}
          </span>
          <span id="heroBlokirPct" class="text-[10px] font-mono text-rose-400/90">({{ number_format($heroInit['persen_blokir'], 2, ',', '.') }}%)</span>
        </div>
      </div>

      <!-- Right: Timestamp -->
      <div id="heroTimestamp" class="text-[10px] sm:text-[11px] text-slate-400 font-mono text-left sm:text-right">
        Data per {{ $kpi['cutoff_time'] }}
      </div>

    </div>

  </div>

  <!-- SECTION 2: RADAR KINERJA SEMUA KPPN SE-PROVINSI ACEH -->
  <div class="space-y-3">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-0.5">
      <div class="flex items-center gap-2 text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase">
        <span class="text-base">📍</span>
        <span>RADAR KINERJA {{ count($kppnRanking['details'] ?? []) ?: 7 }} KPPN LINGKUP KANWIL DJPB PROVINSI ACEH</span>
      </div>
      <div class="flex flex-wrap items-center gap-2 sm:gap-3">
        <div class="inline-flex rounded-lg border border-slate-200 bg-slate-50 p-0.5 text-xs">
          <button id="btnKppnScopeAll" onclick="switchKppnScope('all')" class="px-2.5 py-1 rounded-md font-bold bg-blue-600 text-white shadow-2xs transition-all">
            Gabungan (K/L + TKD)
          </button>
          <button id="btnKppnScopeKl" onclick="switchKppnScope('kl')" class="px-2.5 py-1 rounded-md font-semibold text-slate-600 hover:text-slate-900 transition-all">
            K/L Saja
          </button>
          <button id="btnKppnScopeBun" onclick="switchKppnScope('bun')" class="px-2.5 py-1 rounded-md font-semibold text-slate-600 hover:text-slate-900 transition-all">
            TKD / BA BUN
          </button>
        </div>
        <div class="inline-flex rounded-lg border border-slate-200 bg-slate-50 p-0.5 text-xs">
          <button id="btnKppnNominal" onclick="switchKppnView('nominal')" class="px-2.5 py-1 rounded-md font-bold bg-white text-blue-700 shadow-2xs transition-all">
            Batang Nominal (T)
          </button>
          <button id="btnKppnPct" onclick="switchKppnView('percent')" class="px-2.5 py-1 rounded-md font-semibold text-slate-500 hover:text-slate-800 transition-all">
            Persentase &amp; Nominal
          </button>
        </div>
        <span id="labelKppnSub" class="text-[11px] text-slate-400 font-mono hidden md:inline">Realisasi / Pagu APBN (K/L + TKD)</span>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/90 p-3.5 sm:p-5 shadow-xs space-y-5">
      
      <!-- Full Width Horizontal Ranking Chart -->
      <div class="w-full">
        <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2">
          <span>💡 Klik salah satu batang KPPN (mis. <strong class="text-slate-600">KPPN Kutacane</strong>) untuk membuka rincian data per KPPN</span>
          <span class="font-mono hidden sm:inline">Urutan: % Serapan Tertinggi</span>
        </div>
        <div class="relative h-72 sm:h-80 w-full">
          <canvas id="chartRadarKppn"></canvas>
        </div>
      </div>

      <!-- Bottom Row: Highlight Capaian Regional (3 Cards Horizontal) -->
      <div class="border-t border-slate-100 pt-4">
        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Highlight Capaian Regional</div>
        
        @php
          $allKppnCards = collect($regionalMatrix['kppns'] ?? [])->map(function ($item) {
              $b = $item['all'];
              return array_merge($b, ['nama' => $item['nama']]);
          });
          $topKppn = $allKppnCards->sortByDesc('persen_realisasi')->first() ?? ['nama' => 'KPPN Kutacane', 'persen_realisasi_formatted' => '92,43%', 'realisasi' => 0, 'pagu' => 0];
          $porsiKppn = $allKppnCards->sortByDesc('pagu')->first() ?? ['nama' => 'KPPN Banda Aceh', 'pagu' => 0, 'realisasi' => 0, 'persen_realisasi_formatted' => '72,84%'];
          $avgAll = $regionalMatrix['ALL']['all'] ?? $heroInit;
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200/80">
            <div class="flex items-center justify-between mb-1">
              <span id="hlTopName" class="text-xs font-extrabold text-emerald-900">{{ $topKppn['nama'] }}</span>
              <span id="hlTopPct" class="text-xs font-black text-emerald-700 font-mono">{{ $topKppn['persen_realisasi_formatted'] }}</span>
            </div>
            <p id="hlTopDesc" class="text-[11px] text-emerald-700">Peringkat 1 serapan tertinggi (Real: Rp {{ number_format(($topKppn['realisasi'] ?? 0) / 1e12, 2, ',', '.') }} T / Pagu: Rp {{ number_format(($topKppn['pagu'] ?? 0) / 1e12, 2, ',', '.') }} T).</p>
          </div>

          <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200/80">
            <div class="flex items-center justify-between mb-1">
              <span id="hlAvgTitle" class="text-xs font-extrabold text-blue-900">Rata-rata Regional Aceh (K/L + TKD)</span>
              <span id="hlAvgPct" class="text-xs font-black text-blue-700 font-mono">{{ $avgAll['persen_realisasi_formatted'] }}</span>
            </div>
            <p id="hlAvgDesc" class="text-[11px] text-blue-700">Total Realisasi Se-Aceh: {{ $avgAll['realisasi_formatted'] }} dari {{ $avgAll['pagu_formatted'] }}.</p>
          </div>

          <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
            <div class="flex items-center justify-between mb-1">
              <span id="hlMaxName" class="text-xs font-extrabold text-slate-800">{{ $porsiKppn['nama'] }}</span>
              <span id="hlMaxPct" class="text-xs font-black text-slate-900 font-mono">{{ $porsiKppn['persen_realisasi_formatted'] }}</span>
            </div>
            <p id="hlMaxDesc" class="text-[11px] text-slate-500">Pagu DIPA terbesar: Rp {{ number_format(($porsiKppn['pagu'] ?? 0) / 1e12, 2, ',', '.') }} T (Real: Rp {{ number_format(($porsiKppn['realisasi'] ?? 0) / 1e12, 2, ',', '.') }} T).</p>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- SECTION 4: "📊 PAGU & REALISASI 10 K/L TERBESAR" (DENGAN TOGGLE NOMINAL VS PERSEN) -->
  <div class="space-y-3">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-0.5">
      <div class="flex items-center gap-2 text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase">
        <span>📊</span>
        <span>PAGU & REALISASI 10 K/L TERBESAR</span>
      </div>
      <div class="flex flex-wrap items-center gap-2 sm:gap-4">
        <!-- Interactive Metric View Toggle -->
        <div class="inline-flex rounded-lg border border-slate-200 bg-slate-50 p-0.5 text-xs">
          <button id="btnTopKlNominal" onclick="switchTopKlView('nominal')" class="px-2.5 py-1 rounded-md font-bold bg-white text-blue-700 shadow-2xs transition-all">
            Nominal (Triliun)
          </button>
          <button id="btnTopKlPercent" onclick="switchTopKlView('percent')" class="px-2.5 py-1 rounded-md font-semibold text-slate-500 hover:text-slate-800 transition-all">
            Persentase (%)
          </button>
        </div>

        <!-- Legend Indicator -->
        <div class="flex items-center gap-3 text-xs font-bold">
          <span class="inline-flex items-center gap-1.5 text-slate-600">
            <span class="w-3 h-3 rounded-sm bg-[#1e40af]"></span> Pagu DIPA
          </span>
          <span class="inline-flex items-center gap-1.5 text-slate-600">
            <span class="w-3 h-3 rounded-sm bg-[#f59e0b]"></span> Realisasi
          </span>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/90 p-3.5 sm:p-5 shadow-sm">
      <div class="relative h-80 sm:h-96 lg:h-[420px] w-full">
        <canvas id="chartTopKL"></canvas>
      </div>
      <div class="mt-3 pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between text-[11px] text-slate-400 font-mono gap-1">
        <span id="labelTopKlSub">Data diurutkan berdasarkan alokasi pagu DIPA terbesar</span>
        <span id="labelTopKlScale">Skala dalam Triliun Rupiah</span>
      </div>
    </div>
  </div>

  <!-- SECTION 6: "📊 GRAFIK PAGU & REALISASI SATKER BA BUN (TKD ACEH)" (DENGAN FILTER KPPN & KABUPATEN/KOTA) -->
  <div class="space-y-3">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/90 shadow-sm">
      <div class="flex items-center gap-2 text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase">
        <span class="text-base">📊</span>
        <span>GRAFIK PAGU & REALISASI SATKER BA BUN (TKD ACEH)</span>
      </div>

      <!-- Controls: Legend & Filter Menu -->
      <div class="flex flex-wrap items-center gap-3">
        <!-- Legend Indicator -->
        <div class="flex items-center gap-3 text-xs font-bold px-2 py-1 rounded-lg bg-slate-50 border border-slate-200/70">
          <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-sm bg-[#8b5cf6]"></span>
            <span class="text-slate-700">Pagu</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-sm bg-[#f59e0b]"></span>
            <span class="text-slate-700">Realisasi</span>
          </div>
        </div>

        <!-- Filter Dropdown KPPN / Kabupaten Kota di Aceh -->
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <div class="relative flex items-center w-full sm:w-auto">
            <span class="absolute left-2.5 text-purple-600 pointer-events-none">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
            </span>
            <select id="filterSatkerBUN" 
                    onchange="filterChartSatkerBUN(this.value)" 
                    class="w-full sm:w-auto appearance-none pl-8 pr-8 py-1.5 bg-slate-50 hover:bg-slate-100 border border-purple-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 cursor-pointer transition-colors shadow-xs">
              @foreach($bunData as $bKey => $bItem)
                <option value="{{ $bKey }}">{{ $bItem['label'] ?? $bKey }}</option>
              @endforeach
            </select>
            <span class="absolute right-2.5 text-slate-400 pointer-events-none text-[10px]">▼</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Quick Buttons (Dinamis dari Database SQLite per KPPN) -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 no-scrollbar sm:flex-wrap text-xs">
      <span class="text-[11px] font-bold text-slate-400 mr-1 shrink-0">Filter:</span>
      @foreach($bunData as $bKey => $bItem)
        <button onclick="filterChartSatkerBUN('{{ $bKey }}')" 
                data-code="{{ $bKey }}"
                class="shrink-0 bun-filter-pill px-3 py-1 rounded-lg font-semibold text-xs transition-all {{ $bKey === 'ALL' ? 'bg-purple-600 text-white shadow-xs border border-purple-600' : 'bg-white text-slate-700 hover:bg-purple-50 border border-slate-200' }}">
          {{ $bItem['label'] ?? $bKey }}
        </button>
      @endforeach
    </div>

    <!-- Chart Card BA BUN (Persis Tampilan Gambar 1) -->
    <div class="bg-white rounded-2xl border-2 border-purple-400/80 shadow-sm p-3.5 sm:p-5">
      <div class="relative h-72 sm:h-80 lg:h-96 w-full">
        <canvas id="chartSatkerBUN"></canvas>
      </div>
      <div class="mt-3 pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-400 font-mono gap-1">
        <span id="labelSatkerBUNFoot">{{ $bunData['ALL']['footer'] ?? 'Satker Penyalur Transfer Ke Daerah (TKD) · Kanwil DJPb Provinsi Aceh' }}</span>
        <span>Skala dalam Miliar Rupiah</span>
      </div>
    </div>
  </div>

  <!-- SECTION 7: "🏛️ PAGU REALISASI SATKER BA BUN" (DINAMIS DARI DATABASE) -->
  <div class="space-y-3">
    <div class="flex items-center gap-2 text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase px-0.5">
      <span>🏛️</span>
      <span>PAGU REALISASI SATKER BA BUN</span>
    </div>

    <!-- 3 KARTU PAGU REALISASI SATKER BA BUN (DINAMIS MENGIKUTI FILTER) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
      
      <!-- Card BUN 1: Total Pagu DIPA -->
      <div class="bg-white rounded-2xl border-t-4 border-t-purple-500 border border-slate-200 p-3.5 sm:p-5 shadow-sm flex flex-col justify-between transition-all">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-bold text-slate-700">Total Pagu DIPA</span>
          <span class="text-base">📋</span>
        </div>
        <div id="bunCardPagu" class="text-lg sm:text-xl lg:text-2xl font-black text-slate-900 font-mono tracking-tight break-all sm:break-normal">
          {{ $bunData['ALL']['cardPagu'] ?? 'Rp 0' }}
        </div>
        <div id="bunCardSubPagu" class="text-[11px] text-slate-400 mt-2">{{ $bunData['ALL']['subPagu'] ?? 'Alokasi Satker Penyalur BUN' }}</div>
      </div>

      <!-- Card BUN 2: Total Realisasi -->
      <div class="bg-white rounded-2xl border-t-4 border-t-emerald-500 border border-slate-200 p-3.5 sm:p-5 shadow-sm flex flex-col justify-between transition-all">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-bold text-slate-700">Total Realisasi</span>
          <span class="text-base">💵</span>
        </div>
        <div id="bunCardRealisasi" class="text-lg sm:text-xl lg:text-2xl font-black text-emerald-600 font-mono tracking-tight break-all sm:break-normal">
          {{ $bunData['ALL']['cardRealisasi'] ?? 'Rp 0' }}
        </div>
        <div id="bunCardSubRealisasi" class="text-[11px] text-emerald-600 font-bold mt-2">{{ $bunData['ALL']['subRealisasi'] ?? 'Realisasi Penyaluran BUN' }}</div>
      </div>

      <!-- Card BUN 3: Total Pagu Blokir -->
      <div class="bg-white rounded-2xl border-t-4 border-t-rose-500 border border-slate-200 p-3.5 sm:p-5 shadow-sm flex flex-col justify-between transition-all">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-bold text-slate-700">Total Pagu Blokir</span>
          <span class="w-4 h-4 rounded-full bg-rose-500 inline-block"></span>
        </div>
        <div id="bunCardBlokir" class="text-lg sm:text-xl lg:text-2xl font-black text-rose-600 font-mono tracking-tight break-all sm:break-normal">
          {{ $bunData['ALL']['cardBlokir'] ?? 'Rp 0' }}
        </div>
        <div id="bunCardSubBlokir" class="text-[11px] text-emerald-600 font-semibold mt-2">{{ $bunData['ALL']['subBlokir'] ?? 'Nihil / Tidak Ada Blokir' }}</div>
      </div>

    </div>
  </div>

  <!-- SECTION 8: "📊 PAGU & REALISASI PER JENIS BELANJA" -->
  <div class="space-y-3">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-0.5">
      <div class="flex items-center gap-2 text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase">
        <span class="text-base">📊</span>
        <span>PAGU & REALISASI PER JENIS BELANJA</span>
      </div>
      <div class="flex flex-wrap items-center gap-2 sm:gap-3">
        <!-- Toggle Nominal vs Persentase -->
        <div class="inline-flex rounded-lg border border-slate-200 bg-slate-50 p-0.5 text-xs">
          <button id="btnJbNominal" onclick="switchJbView('nominal')" class="px-2.5 py-1 rounded-md font-bold bg-white text-blue-700 shadow-2xs transition-all">
            Nominal (Triliun)
          </button>
          <button id="btnJbPercent" onclick="switchJbView('percent')" class="px-2.5 py-1 rounded-md font-semibold text-slate-500 hover:text-slate-800 transition-all">
            Persentase (%)
          </button>
        </div>

        <!-- Legend Indicator -->
        <div id="jbLegendNominal" class="flex items-center gap-3 text-xs font-bold px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200/70">
          <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-sm bg-[#1e40af]"></span>
            <span class="text-slate-700">Pagu</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-sm bg-[#f59e0b]"></span>
            <span class="text-slate-700">Realisasi</span>
          </div>
        </div>

        <a href="{{ route('satker.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors group">
          <span>Lihat Semua Rincian Satker</span>
          <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
        </a>
      </div>
    </div>

    <!-- Chart Card Pagu & Realisasi per Jenis Belanja -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-3.5 sm:p-5">
      <div class="relative h-72 sm:h-80 w-full">
        <canvas id="chartJenisBelanjaBar"></canvas>
      </div>

      <!-- Strip Ringkasan 4 Jenis Belanja (Membantu Keterbacaan Skala Kecil spt Bansos 57 & % Serapan) -->
      <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
        @foreach(($jenisBelanjaChart['details'] ?? []) as $jbIdx => $jbDet)
          @php
            $pctVal = $jbDet['persen_num'] ?? (float) str_replace(',', '.', str_replace('%', '', $jbDet['persen'] ?? '0'));
            $badgeTone = $pctVal >= 80
              ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
              : ($pctVal >= 60 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200');
            $barTone = $pctVal >= 80 ? 'bg-emerald-500' : ($pctVal >= 60 ? 'bg-amber-500' : 'bg-rose-500');
          @endphp
          <div class="rounded-xl border border-slate-200/80 bg-slate-50/60 p-3 flex flex-col justify-between gap-2">
            <div class="flex items-center justify-between gap-2">
              <span class="text-xs font-extrabold text-slate-800 truncate">{{ $jbDet['akun'] ?? '' }} {{ $jbDet['nama'] ?? '' }}</span>
              <span class="shrink-0 text-[11px] font-extrabold px-2 py-0.5 rounded-full border {{ $badgeTone }}">{{ $jbDet['persen'] ?? '0%' }}</span>
            </div>
            <div class="w-full h-1.5 bg-slate-200/80 rounded-full overflow-hidden">
              <div class="h-full rounded-full {{ $barTone }}" style="width: {{ min(100, max(2, $pctVal)) }}%"></div>
            </div>
            <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium">
              <span>Pagu: <strong class="text-slate-700">{{ $jenisBelanjaChart['pagu_badges'][$jbIdx] ?? '-' }}</strong></span>
              <span>Real: <strong class="text-slate-800">{{ $jenisBelanjaChart['realisasi_badges'][$jbIdx] ?? '-' }}</strong></span>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-3 pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-400 font-mono gap-1">
        <span>Monitoring per kode akun belanja APBN (Pegawai 51, Barang 52, Modal 53, Bansos 57)</span>
        <span id="labelJbScale">Skala dalam Triliun Rupiah</span>
      </div>
    </div>
  </div>

  <!-- Modal Detail KPPN (Saat Batang Radar KPPN Diklik) -->
  <div id="modalDetailKppn" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4" onclick="if(event.target === this) closeKppnDetailModal()">
    <div class="bg-white w-full max-w-4xl max-h-[90vh] rounded-2xl border border-slate-200 shadow-2xl flex flex-col overflow-hidden">
      <div class="bg-gradient-to-r from-[#0b132b] to-[#1e3a8a] text-white px-5 py-4 flex items-start justify-between gap-3 shrink-0">
        <div>
          <div class="flex items-center gap-2 text-[10px] font-mono uppercase tracking-wider text-blue-200">
            <span id="mkppnKodeBadge" class="px-2 py-0.5 rounded bg-blue-500/30 border border-blue-400/40 font-bold text-white">KPPN 001</span>
            <span>Rincian Kinerja Pelaksanaan APBN &amp; TKD</span>
          </div>
          <h3 id="mkppnTitle" class="text-base sm:text-xl font-black mt-1 tracking-tight">KPPN ...</h3>
          <p id="mkppnSubtitle" class="text-[11px] text-blue-100/90 font-mono mt-0.5">-</p>
        </div>
        <button type="button" onclick="closeKppnDetailModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center font-bold transition-colors shrink-0" aria-label="Tutup">
          ✕
        </button>
      </div>

      <div class="p-4 sm:p-5 overflow-y-auto space-y-5">
        <!-- 3 Kartu Ringkasan KPPN -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div class="p-3.5 rounded-xl bg-slate-900 text-white border border-slate-700">
            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-blue-300">
              <span>Gabungan (K/L + TKD)</span>
              <span id="mkppnAllSatker" class="font-mono text-slate-300">-</span>
            </div>
            <div id="mkppnAllReal" class="text-sm sm:text-base font-black font-mono mt-1">-</div>
            <div id="mkppnAllPagu" class="text-[11px] font-mono text-slate-300 mt-0.5">Pagu: -</div>
            <div class="flex items-center justify-between text-[11px] font-mono mt-2 pt-2 border-t border-slate-700">
              <span class="text-slate-400">Serapan</span>
              <span id="mkppnAllPct" class="font-black text-emerald-400">-</span>
            </div>
          </div>

          <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200/80">
            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-blue-700">
              <span>Belanja Satker K/L</span>
              <span id="mkppnKlSatker" class="font-mono text-blue-600">-</span>
            </div>
            <div id="mkppnKlReal" class="text-sm sm:text-base font-black font-mono text-slate-900 mt-1">-</div>
            <div id="mkppnKlPagu" class="text-[11px] font-mono text-slate-500 mt-0.5">Pagu: -</div>
            <div class="flex items-center justify-between text-[11px] font-mono mt-2 pt-2 border-t border-blue-200/70">
              <span class="text-slate-500">Serapan</span>
              <span id="mkppnKlPct" class="font-black text-blue-700">-</span>
            </div>
          </div>

          <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200/80">
            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-amber-800">
              <span>TKD / BA BUN</span>
              <span id="mkppnBunSatker" class="font-mono text-amber-700">-</span>
            </div>
            <div id="mkppnBunReal" class="text-sm sm:text-base font-black font-mono text-slate-900 mt-1">-</div>
            <div id="mkppnBunPagu" class="text-[11px] font-mono text-slate-500 mt-0.5">Pagu: -</div>
            <div class="flex items-center justify-between text-[11px] font-mono mt-2 pt-2 border-t border-amber-200/70">
              <span class="text-slate-500">Serapan</span>
              <span id="mkppnBunPct" class="font-black text-amber-700">-</span>
            </div>
          </div>
        </div>

        <!-- Jenis Belanja per KPPN -->
        <div>
          <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Rincian Pagu &amp; Realisasi per Jenis Belanja</h4>
          <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-xs border-collapse">
              <thead class="bg-slate-50 text-[10px] uppercase font-extrabold text-slate-500 border-b border-slate-200">
                <tr>
                  <th class="py-2 px-3">Jenis Belanja</th>
                  <th class="py-2 px-3 text-right">Pagu</th>
                  <th class="py-2 px-3 text-right">Realisasi</th>
                  <th class="py-2 px-3 text-right w-20">%</th>
                </tr>
              </thead>
              <tbody id="mkppnJbBody" class="divide-y divide-slate-100 text-slate-700"></tbody>
            </table>
          </div>
        </div>

        <!-- Top 8 Satker di Wilayah KPPN -->
        <div>
          <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Satker / Penyalur Pagu Terbesar di Wilayah Ini</h4>
          <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-xs border-collapse">
              <thead class="bg-slate-50 text-[10px] uppercase font-extrabold text-slate-500 border-b border-slate-200">
                <tr>
                  <th class="py-2 px-3">Satker</th>
                  <th class="py-2 px-3">Tipe</th>
                  <th class="py-2 px-3 text-right">Pagu</th>
                  <th class="py-2 px-3 text-right">Realisasi</th>
                  <th class="py-2 px-3 text-right w-20">%</th>
                </tr>
              </thead>
              <tbody id="mkppnSatkerBody" class="divide-y divide-slate-100 text-slate-700"></tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-2 shrink-0">
        <span class="text-[11px] font-mono text-slate-400">Data SINTESA Kanwil DJPb Provinsi Aceh</span>
        <div class="flex items-center gap-2">
          <button type="button" id="mkppnApplyBtn" onclick="applySelectedKppnFilter()" class="px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors shadow-2xs">
            Filter Dashboard ke KPPN Ini
          </button>
          <button type="button" onclick="closeKppnDetailModal()" class="px-3.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
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
  // Inisialisasi Chart.js Global Typography & Colors
  Chart.defaults.font.family = '"Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, sans-serif';
  Chart.defaults.color = '#475569';

  // 0. DATA KONSOLIDASI 7 KPPN & HERO EXECUTIVE SWITCHER
  const regionalMatrix = @json($regionalMatrix);
  let activeHeroScope = 'all';
  let activeHeroRegion = 'ALL';

  function updateHeroBar() {
    const regionObj = activeHeroRegion === 'ALL'
      ? regionalMatrix.ALL
      : ((regionalMatrix.kppns || {})[activeHeroRegion] || regionalMatrix.ALL);
    const data = regionObj[activeHeroScope] || regionObj.all;
    const klInfo = regionObj.kl || data;
    const bunInfo = regionObj.bun || data;

    const scopeLabels = {
      all: `KONSOLIDASI APBN (${regionObj.nama.toUpperCase()} · K/L + BA BUN · ${data.satker_count} SATKER) · Total Pagu DIPA`,
      kl: `BELANJA SATKER K/L (${regionObj.nama.toUpperCase()} · ${data.satker_count} SATKER) · Total Pagu DIPA`,
      bun: `TRANSFER KE DAERAH / BA BUN (${regionObj.nama.toUpperCase()} · ${data.satker_count} SATKER) · Total Pagu DIPA`
    };

    document.getElementById('heroScopeTitle').textContent = scopeLabels[activeHeroScope];
    document.getElementById('heroTotalPagu').textContent = data.pagu_formatted;
    document.getElementById('heroRealVal').textContent = data.realisasi_formatted;
    document.getElementById('heroSisaVal').textContent = data.sisa_formatted;
    document.getElementById('heroRealPctText').textContent = data.persen_realisasi_formatted;
    document.getElementById('heroSisaPctText').textContent = data.persen_sisa_formatted;
    document.getElementById('heroBarReal').style.width = Math.min(data.persen_realisasi, 100) + '%';
    document.getElementById('heroBarRealLabel').textContent = data.persen_realisasi_formatted;
    document.getElementById('heroBarSisa').style.width = Math.max(data.persen_sisa, 0) + '%';
    document.getElementById('heroCutoffVal').textContent = data.blokir_formatted;
    document.getElementById('heroBlokirPct').textContent = `(${Number(data.persen_blokir || 0).toFixed(2).replace('.', ',')}%)`;

    const subEl = document.getElementById('heroSubSummary');
    if (subEl) {
      subEl.textContent = activeHeroScope === 'all'
        ? `Belanja K/L (${klInfo.satker_count} Satker): ${klInfo.pagu_formatted} + BA BUN/TKD (${bunInfo.satker_count} Satker): ${bunInfo.pagu_formatted}`
        : `${regionObj.nama} · Realisasi ${data.persen_realisasi_formatted}`;
    }

    const activeCls = 'px-2.5 sm:px-3 py-1.5 rounded-lg text-[11px] sm:text-xs font-bold bg-blue-600 text-white shadow-xs transition-all';
    const idleCls = 'px-2.5 sm:px-3 py-1.5 rounded-lg text-[11px] sm:text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition-all';
    document.getElementById('btnHeroScopeAll').className = activeHeroScope === 'all' ? activeCls : idleCls;
    document.getElementById('btnHeroScopeKl').className = activeHeroScope === 'kl' ? activeCls : idleCls;
    document.getElementById('btnHeroScopeBun').className = activeHeroScope === 'bun' ? activeCls : idleCls;
  }

  function setHeroScope(scope) {
    activeHeroScope = scope;
    updateHeroBar();
  }

  function setHeroRegion(regionKey) {
    activeHeroRegion = regionKey;
    updateHeroBar();
    if (typeof filterBunWilayah === 'function') {
      filterBunWilayah(regionKey);
    }
  }

  // 1. CHART RADAR KINERJA 7 KPPN SE-PROVINSI ACEH (GABUNGAN K/L+TKD, K/L, BUN)
  let chartRadarKppnInstance = null;
  let currentKppnMode = 'nominal';
  let currentKppnScope = 'all';
  let activeRadarDetails = [];

  function formatShortNom(valRp) {
    if (valRp >= 1e12) return 'Rp ' + (valRp / 1e12).toFixed(2).replace('.', ',') + ' T';
    if (valRp >= 1e9) return 'Rp ' + (valRp / 1e9).toFixed(1).replace('.', ',') + ' M';
    return 'Rp ' + Math.round(valRp / 1e6).toLocaleString('id-ID') + ' Jt';
  }

  function buildKppnSeries(scope) {
    const list = Object.entries(regionalMatrix.kppns || {}).map(([key, k]) => {
      const b = k[scope] || k.all;
      return {
        key: key,
        nama: k.nama,
        pagu: b.pagu,
        pagu_formatted: b.pagu_formatted,
        realisasi: b.realisasi,
        realisasi_formatted: b.realisasi_formatted,
        persen: b.persen_realisasi,
        persentase_formatted: b.persen_realisasi_formatted,
      };
    });
    list.sort((a, b) => b.persen - a.persen);
    return list;
  }

  function updateHighlightCards(scope) {
    const details = buildKppnSeries(scope);
    if (!details.length) return;
    const topItem = details[0];
    const maxPaguItem = [...details].sort((a, b) => b.pagu - a.pagu)[0];
    const avgBucket = (regionalMatrix.ALL && regionalMatrix.ALL[scope]) ? regionalMatrix.ALL[scope] : regionalMatrix.ALL.all;
    const scopeTitleMap = { all: 'K/L + TKD', kl: 'Satker K/L', bun: 'BA BUN / TKD' };

    document.getElementById('hlTopName').textContent = topItem.nama;
    document.getElementById('hlTopPct').textContent = topItem.persentase_formatted;
    document.getElementById('hlTopDesc').textContent = `Peringkat 1 serapan tertinggi (Real: ${formatShortNom(topItem.realisasi)} / Pagu: ${formatShortNom(topItem.pagu)}).`;

    document.getElementById('hlAvgTitle').textContent = `Rata-rata Regional Aceh (${scopeTitleMap[scope]})`;
    document.getElementById('hlAvgPct').textContent = avgBucket.persen_realisasi_formatted;
    document.getElementById('hlAvgDesc').textContent = `Total Realisasi ${scopeTitleMap[scope]}: ${avgBucket.realisasi_formatted} dari ${avgBucket.pagu_formatted}.`;

    document.getElementById('hlMaxName').textContent = maxPaguItem.nama;
    document.getElementById('hlMaxPct').textContent = maxPaguItem.persentase_formatted;
    document.getElementById('hlMaxDesc').textContent = `Pagu DIPA terbesar: ${formatShortNom(maxPaguItem.pagu)} (Real: ${formatShortNom(maxPaguItem.realisasi)}).`;
  }

  function initChartRadarKppn() {
    const ctx = document.getElementById('chartRadarKppn');
    if (!ctx) return;

    activeRadarDetails = buildKppnSeries(currentKppnScope);

    chartRadarKppnInstance = new Chart(ctx.getContext('2d'), {
      type: 'bar',
      data: {
        labels: activeRadarDetails.map(d => d.nama),
        datasets: [{
          label: '% Realisasi',
          data: activeRadarDetails.map(d => d.persen),
          backgroundColor: activeRadarDetails.map(d => d.persen >= 85 ? '#10b981' : (d.persen >= 75 ? '#3b82f6' : '#f59e0b')),
          borderRadius: 6,
          barPercentage: 0.62,
          categoryPercentage: 0.78
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        onClick: (evt, _elements, chart) => {
          const hits = chart.getElementsAtEventForMode(evt, 'y', { intersect: false }, true);
          if (hits.length > 0) {
            const d = activeRadarDetails[hits[0].index];
            if (d && d.key) {
              openKppnDetailModal(d.key);
            }
          }
        },
        onHover: (evt, _elements, chart) => {
          const hits = chart.getElementsAtEventForMode(evt, 'y', { intersect: false }, true);
          if (evt?.native?.target) {
            evt.native.target.style.cursor = hits.length ? 'pointer' : 'default';
          }
        },
        layout: {
          padding: {
            right: window.innerWidth >= 640 ? 210 : 125
          }
        },
        plugins: {
          legend: { display: false },
          datalabels: {
            display: true,
            anchor: 'end',
            align: 'right',
            offset: 6,
            color: '#0f172a',
            font: { weight: '800', size: window.innerWidth >= 640 ? 11.5 : 10 },
            formatter: (v, context) => {
              const d = activeRadarDetails[context.dataIndex];
              if (currentKppnMode === 'nominal') {
                if (!d) return v.toFixed(2) + ' T';
                const valRp = context.datasetIndex === 0 ? d.pagu : d.realisasi;
                return context.datasetIndex === 1
                  ? `${formatShortNom(valRp)} (${d.persentase_formatted})`
                  : formatShortNom(valRp);
              }
              if (!d) return v.toFixed(2).replace('.', ',') + '%';
              const realShort = formatShortNom(d.realisasi);
              const paguShort = formatShortNom(d.pagu);
              return window.innerWidth >= 640
                ? `${v.toFixed(2).replace('.', ',')}%  ·  ${realShort} / ${paguShort}`
                : `${v.toFixed(1).replace('.', ',')}% (${realShort})`;
            }
          },
          tooltip: {
            callbacks: {
              label: function(ctx) {
                const d = activeRadarDetails[ctx.dataIndex];
                if (!d) return ` Serapan: ${ctx.parsed.x.toFixed(2)}%`;
                return [
                  ` Serapan: ${d.persentase_formatted}`,
                  ` Realisasi: ${d.realisasi_formatted}`,
                  ` Pagu DIPA: ${d.pagu_formatted}`
                ];
              }
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            max: 100,
            ticks: {
              stepSize: 20,
              callback: (v) => v + '%',
              font: { size: 11.5, weight: '600' }
            },
            grid: { color: 'rgba(0,0,0,0.06)' }
          },
          y: {
            grid: { display: false },
            ticks: { font: { size: 12.5, weight: '700' }, color: '#0f172a' }
          }
        }
      }
    });

    if (currentKppnMode === 'nominal') {
      switchKppnView('nominal');
    }
  }

  function switchKppnScope(scope) {
    currentKppnScope = scope;
    const activeCls = 'px-2.5 py-1 rounded-md font-bold bg-blue-600 text-white shadow-2xs transition-all';
    const idleCls = 'px-2.5 py-1 rounded-md font-semibold text-slate-600 hover:text-slate-900 transition-all';
    document.getElementById('btnKppnScopeAll').className = scope === 'all' ? activeCls : idleCls;
    document.getElementById('btnKppnScopeKl').className = scope === 'kl' ? activeCls : idleCls;
    document.getElementById('btnKppnScopeBun').className = scope === 'bun' ? activeCls : idleCls;

    const subText = {
      all: 'Realisasi / Pagu APBN (K/L + TKD)',
      kl: 'Realisasi / Pagu DIPA K/L',
      bun: 'Realisasi / Pagu TKD (BA BUN)'
    };
    const subEl = document.getElementById('labelKppnSub');
    if (subEl) subEl.textContent = subText[scope];

    updateHighlightCards(scope);
    switchKppnView(currentKppnMode);
  }

  function switchKppnView(mode) {
    if (!chartRadarKppnInstance) return;
    currentKppnMode = mode;

    const btnPct = document.getElementById('btnKppnPct');
    const btnNom = document.getElementById('btnKppnNominal');
    activeRadarDetails = buildKppnSeries(currentKppnScope);
    chartRadarKppnInstance.data.labels = activeRadarDetails.map(d => d.nama);

    if (mode === 'nominal') {
      const paguT = activeRadarDetails.map(d => +(d.pagu / 1e12).toFixed(3));
      const realT = activeRadarDetails.map(d => +(d.realisasi / 1e12).toFixed(3));
      const maxT = Math.ceil(Math.max(...paguT, 1) * 1.1);

      chartRadarKppnInstance.data.datasets = [
        {
          label: 'Pagu DIPA',
          data: paguT,
          backgroundColor: '#1e40af',
          borderRadius: 4,
          barPercentage: 0.72,
          categoryPercentage: 0.75
        },
        {
          label: 'Realisasi',
          data: realT,
          backgroundColor: '#f59e0b',
          borderRadius: 4,
          barPercentage: 0.72,
          categoryPercentage: 0.75
        }
      ];
      chartRadarKppnInstance.options.plugins.legend.display = true;
      chartRadarKppnInstance.options.layout.padding.right = window.innerWidth >= 640 ? 150 : 90;
      chartRadarKppnInstance.options.scales.x.max = maxT;
      chartRadarKppnInstance.options.scales.x.ticks.stepSize = Math.max(Math.ceil(maxT / 5), 1);
      chartRadarKppnInstance.options.scales.x.ticks.callback = (v) => v + ' T';

      if (btnNom) btnNom.className = 'px-2.5 py-1 rounded-md font-bold bg-white text-blue-700 shadow-2xs transition-all';
      if (btnPct) btnPct.className = 'px-2.5 py-1 rounded-md font-semibold text-slate-500 hover:text-slate-800 transition-all';
    } else {
      const persenArr = activeRadarDetails.map(d => d.persen);
      chartRadarKppnInstance.data.datasets = [{
        label: '% Realisasi',
        data: persenArr,
        backgroundColor: persenArr.map(p => p >= 85 ? '#10b981' : (p >= 75 ? '#3b82f6' : '#f59e0b')),
        borderRadius: 6,
        barPercentage: 0.62,
        categoryPercentage: 0.78
      }];
      chartRadarKppnInstance.options.plugins.legend.display = false;
      chartRadarKppnInstance.options.layout.padding.right = window.innerWidth >= 640 ? 210 : 125;
      chartRadarKppnInstance.options.scales.x.max = 100;
      chartRadarKppnInstance.options.scales.x.ticks.stepSize = 20;
      chartRadarKppnInstance.options.scales.x.ticks.callback = (v) => v + '%';

      if (btnPct) btnPct.className = 'px-2.5 py-1 rounded-md font-bold bg-white text-blue-700 shadow-2xs transition-all';
      if (btnNom) btnNom.className = 'px-2.5 py-1 rounded-md font-semibold text-slate-500 hover:text-slate-800 transition-all';
    }

    chartRadarKppnInstance.update();
  }

  // 1B. MODAL DETAIL KPPN SAAT BAR DIKLIK
  let activeModalKppnKey = null;

  function escHtml(str) {
    return String(str ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function openKppnDetailModal(kppnKey) {
    const k = (regionalMatrix.kppns || {})[kppnKey];
    if (!k) return;
    activeModalKppnKey = kppnKey;

    document.getElementById('mkppnKodeBadge').textContent = `KPPN ${k.kode}`;
    document.getElementById('mkppnTitle').textContent = k.nama;
    document.getElementById('mkppnSubtitle').textContent =
      `Total ${k.all.satker_count} Satker (${k.kl.satker_count} Satker K/L + ${k.bun.satker_count} BA BUN) · Blokir: ${k.all.blokir_formatted}`;

    document.getElementById('mkppnAllSatker').textContent = `${k.all.satker_count} Satker`;
    document.getElementById('mkppnAllReal').textContent = k.all.realisasi_formatted;
    document.getElementById('mkppnAllPagu').textContent = `Pagu: ${k.all.pagu_formatted}`;
    document.getElementById('mkppnAllPct').textContent = k.all.persen_realisasi_formatted;

    document.getElementById('mkppnKlSatker').textContent = `${k.kl.satker_count} Satker`;
    document.getElementById('mkppnKlReal').textContent = k.kl.realisasi_formatted;
    document.getElementById('mkppnKlPagu').textContent = `Pagu: ${k.kl.pagu_formatted}`;
    document.getElementById('mkppnKlPct').textContent = k.kl.persen_realisasi_formatted;

    document.getElementById('mkppnBunSatker').textContent = `${k.bun.satker_count} Satker`;
    document.getElementById('mkppnBunReal').textContent = k.bun.realisasi_formatted;
    document.getElementById('mkppnBunPagu').textContent = `Pagu: ${k.bun.pagu_formatted}`;
    document.getElementById('mkppnBunPct').textContent = k.bun.persen_realisasi_formatted;

    const jbBody = document.getElementById('mkppnJbBody');
    const jbRows = k.jenis_belanja || [];
    jbBody.innerHTML = jbRows.length
      ? jbRows.map(r => `
          <tr class="hover:bg-slate-50">
            <td class="py-2 px-3 font-semibold text-slate-800">${escHtml(r.label)}</td>
            <td class="py-2 px-3 text-right font-mono text-slate-700">${escHtml(r.pagu_formatted)}</td>
            <td class="py-2 px-3 text-right font-mono font-bold text-emerald-700">${escHtml(r.realisasi_formatted)}</td>
            <td class="py-2 px-3 text-right font-mono font-extrabold text-blue-700">${escHtml(r.persen_formatted)}</td>
          </tr>
        `).join('')
      : '<tr><td colspan="4" class="py-3 px-3 text-center text-slate-400">Tidak ada data jenis belanja</td></tr>';

    const sBody = document.getElementById('mkppnSatkerBody');
    const sRows = k.top_satkers || [];
    sBody.innerHTML = sRows.length
      ? sRows.map(s => `
          <tr class="hover:bg-slate-50">
            <td class="py-2 px-3">
              <div class="font-bold text-slate-900 leading-snug">${escHtml(s.nama)}</div>
              <div class="text-[10px] text-slate-400 font-mono">${escHtml(s.kode)} · ${escHtml(s.kl)}</div>
            </td>
            <td class="py-2 px-3">
              <span class="px-1.5 py-0.5 rounded text-[10px] font-bold ${s.jenis === 'K/L' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-800'}">${escHtml(s.jenis)}</span>
            </td>
            <td class="py-2 px-3 text-right font-mono text-slate-700 whitespace-nowrap">${escHtml(s.pagu_formatted)}</td>
            <td class="py-2 px-3 text-right font-mono font-bold text-emerald-700 whitespace-nowrap">${escHtml(s.realisasi_formatted)}</td>
            <td class="py-2 px-3 text-right font-mono font-extrabold text-slate-800">${escHtml(s.persen_formatted)}</td>
          </tr>
        `).join('')
      : '<tr><td colspan="5" class="py-3 px-3 text-center text-slate-400">Tidak ada data satker</td></tr>';

    document.getElementById('modalDetailKppn').classList.remove('hidden');
  }

  function closeKppnDetailModal() {
    document.getElementById('modalDetailKppn').classList.add('hidden');
  }

  function applySelectedKppnFilter() {
    if (!activeModalKppnKey) return;
    const sel = document.getElementById('heroKppnSelect');
    if (sel) sel.value = activeModalKppnKey;
    setHeroRegion(activeModalKppnKey);
    closeKppnDetailModal();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeKppnDetailModal();
  });

  // 2. CHART 10 K/L TERBESAR DENGAN TOGGLE NOMINAL VS PERSEN + LOGO PER K/L
  let chartTopKLInstance = null;
  let currentTopKlMode = 'nominal';
  const klDataMaster = @json($topKl);
  const klLogoBaseUrl = "{{ asset('images/kl') }}";
  const klLogoCache = {};

  function preloadKlLogos(codes) {
    (codes || []).forEach((code) => {
      if (!code || klLogoCache[code]) return;
      const img = new Image();
      img._failed = false;
      img.onload = () => {
        if (chartTopKLInstance) chartTopKLInstance.draw();
      };
      img.onerror = () => {
        img._failed = true;
        if (chartTopKLInstance) chartTopKLInstance.draw();
      };
      img.src = `${klLogoBaseUrl}/${code}.png`;
      klLogoCache[code] = img;
    });
  }

  const klLogoPlugin = {
    id: 'klLogoPlugin',
    afterDraw(chart) {
      const codes = klDataMaster.codes || [];
      const labels = klDataMaster.labels || [];
      const yScale = chart.scales?.y;
      const area = chart.chartArea;
      if (!yScale || !area || !codes.length) return;

      const ctx = chart.ctx;
      const iconSize = window.innerWidth < 640 ? 18 : 22;
      const xPos = Math.max(4, area.left - iconSize - 7);

      ctx.save();
      codes.forEach((code, i) => {
        const yCenter = yScale.getPixelForValue(i);
        if (yCenter < area.top - 10 || yCenter > area.bottom + 10) return;
        const yPos = yCenter - iconSize / 2;
        const img = klLogoCache[code];

        if (img && img.complete && !img._failed && img.naturalWidth > 0) {
          ctx.drawImage(img, xPos, yPos, iconSize, iconSize);
        } else {
          // Fallback badge jika kode K/L baru belum punya file PNG
          const rawLabel = (labels[i] || code || 'KL').replace(/Kementerian\s+/i, '').trim();
          const initials = rawLabel.split(/\s+/).slice(0, 2).map(w => w[0] || '').join('').toUpperCase() || 'KL';
          const cx = xPos + iconSize / 2;
          const cy = yCenter;
          ctx.beginPath();
          ctx.arc(cx, cy, iconSize / 2 - 1, 0, Math.PI * 2);
          ctx.fillStyle = '#e2e8f0';
          ctx.fill();
          ctx.fillStyle = '#334155';
          ctx.font = `bold ${Math.round(iconSize * 0.42)}px "Plus Jakarta Sans", sans-serif`;
          ctx.textAlign = 'center';
          ctx.textBaseline = 'middle';
          ctx.fillText(initials, cx, cy + 0.5);
        }
      });
      ctx.restore();
    }
  };

  function initChartTopKL() {
    const ctx = document.getElementById('chartTopKL');
    if (!ctx) return;

    preloadKlLogos(klDataMaster.codes || []);
    const isMobile = window.innerWidth < 640;

    chartTopKLInstance = new Chart(ctx.getContext('2d'), {
      type: 'bar',
      data: {
        labels: klDataMaster.labels,
        datasets: [
          {
            label: 'Pagu DIPA',
            data: klDataMaster.pagu,
            backgroundColor: '#1e40af',
            borderRadius: 4,
            barPercentage: 0.65,
            categoryPercentage: 0.75
          },
          {
            label: 'Realisasi',
            data: klDataMaster.realisasi,
            backgroundColor: '#f59e0b',
            borderRadius: 4,
            barPercentage: 0.65,
            categoryPercentage: 0.75
          }
        ]
      },
      plugins: [klLogoPlugin],
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          datalabels: { display: false },
          tooltip: {
            callbacks: {
              label: function(ctx) {
                if (currentTopKlMode === 'percent') {
                  return ` Capaian Realisasi: ${ctx.parsed.x.toFixed(2)}%`;
                }
                const v = ctx.parsed.x;
                return ` ${ctx.dataset.label}: Rp ${v >= 1 ? v.toFixed(2) + ' Triliun' : (v * 1000).toFixed(0) + ' Miliar'}`;
              }
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            max: Math.ceil(Math.max(...klDataMaster.pagu, 1) * 1.15),
            ticks: {
              stepSize: Math.max(Math.ceil(Math.max(...klDataMaster.pagu, 1) / 5), 1),
              callback: (v) => v + ' T',
              font: { size: 11.5, weight: '600' }
            },
            grid: { color: 'rgba(0,0,0,0.06)' }
          },
          y: {
            grid: { display: false },
            ticks: {
              padding: isMobile ? 28 : 34,
              font: { size: isMobile ? 11 : 12.5, weight: 'bold' },
              color: '#0f172a'
            }
          }
        }
      }
    });
  }

  function switchTopKlView(mode) {
    if (!chartTopKLInstance) return;
    currentTopKlMode = mode;

    const btnNominal = document.getElementById('btnTopKlNominal');
    const btnPercent = document.getElementById('btnTopKlPercent');
    const labelScale = document.getElementById('labelTopKlScale');

    if (mode === 'percent') {
      // Hitung persentase realisasi per K/L
      const pctData = klDataMaster.labels.map((_, i) => {
        const p = klDataMaster.pagu[i];
        const r = klDataMaster.realisasi[i];
        return p > 0 ? parseFloat(((r / p) * 100).toFixed(2)) : 0;
      });

      chartTopKLInstance.data.datasets = [{
        label: 'Persentase Serapan',
        data: pctData,
        backgroundColor: '#10b981',
        borderRadius: 4,
        barPercentage: 0.6,
        categoryPercentage: 0.7
      }];

      chartTopKLInstance.options.scales.x.max = 100;
      chartTopKLInstance.options.scales.x.ticks.stepSize = 20;
      chartTopKLInstance.options.scales.x.ticks.callback = (v) => v + '%';

      if (btnPercent) {
        btnPercent.className = 'px-2.5 py-1 rounded-md font-bold bg-white text-emerald-700 shadow-2xs transition-all';
      }
      if (btnNominal) {
        btnNominal.className = 'px-2.5 py-1 rounded-md font-semibold text-slate-500 hover:text-slate-800 transition-all';
      }
      if (labelScale) labelScale.textContent = 'Skala dalam Persentase Capaian (%)';
    } else {
      // Kembali ke Nominal (Triliun)
      chartTopKLInstance.data.datasets = [
        {
          label: 'Pagu DIPA',
          data: klDataMaster.pagu,
          backgroundColor: '#1e40af',
          borderRadius: 4,
          barPercentage: 0.65,
          categoryPercentage: 0.75
        },
        {
          label: 'Realisasi',
          data: klDataMaster.realisasi,
          backgroundColor: '#f59e0b',
          borderRadius: 4,
          barPercentage: 0.65,
          categoryPercentage: 0.75
        }
      ];

      const maxNom = Math.ceil(Math.max(...klDataMaster.pagu, 1) * 1.15);
      chartTopKLInstance.options.scales.x.max = maxNom;
      chartTopKLInstance.options.scales.x.ticks.stepSize = Math.max(Math.ceil(maxNom / 5), 1);
      chartTopKLInstance.options.scales.x.ticks.callback = (v) => v + ' T';

      if (btnNominal) {
        btnNominal.className = 'px-2.5 py-1 rounded-md font-bold bg-white text-blue-700 shadow-2xs transition-all';
      }
      if (btnPercent) {
        btnPercent.className = 'px-2.5 py-1 rounded-md font-semibold text-slate-500 hover:text-slate-800 transition-all';
      }
      if (labelScale) labelScale.textContent = 'Skala dalam Triliun Rupiah';
    }

    chartTopKLInstance.update();
  }

  // 4. CHART GRAFIK SATKER BA BUN DENGAN FILTER REGIONAL DINAMIS
  let chartSatkerBUNInstance = null;
  let currentBunFilter = 'ALL';
  const bunMasterData = @json($bunData);

  function initChartSatkerBUN() {
    const ctx = document.getElementById('chartSatkerBUN');
    if (!ctx) return;

    const initData = bunMasterData['ALL'];

    chartSatkerBUNInstance = new Chart(ctx.getContext('2d'), {
      type: 'bar',
      data: {
        labels: initData.labels,
        datasets: [
          {
            label: 'Pagu',
            data: initData.pagu,
            backgroundColor: '#8b5cf6',
            borderRadius: 4,
            barPercentage: 0.65,
            categoryPercentage: 0.65
          },
          {
            label: 'Realisasi',
            data: initData.realisasi,
            backgroundColor: '#f59e0b',
            borderRadius: 4,
            barPercentage: 0.65,
            categoryPercentage: 0.65
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          datalabels: { display: false },
          tooltip: {
            callbacks: {
              title: function(items) {
                const idx = items[0].dataIndex;
                const item = bunMasterData[currentBunFilter] || bunMasterData['ALL'];
                return item.fullLabels[idx] || '';
              },
              label: function(ctx) {
                const val = ctx.parsed.y;
                return ` ${ctx.dataset.label}: Rp ${val.toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 1})} M`;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: Math.max(Math.ceil((Math.max(...initData.pagu, ...initData.realisasi, 100) * 1.15) / 1000) * 1000, 1000),
            ticks: {
              stepSize: Math.max(Math.round((Math.ceil((Math.max(...initData.pagu, ...initData.realisasi, 100) * 1.15) / 1000) * 1000) / 5 / 500) * 500, 500),
              callback: (val) => val === 0 ? '0' : 'Rp ' + val.toLocaleString('id-ID') + ' M',
              font: { size: 11.5, weight: '600' },
              color: '#334155'
            },
            grid: { color: 'rgba(226, 232, 240, 0.7)' }
          },
          x: {
            grid: { color: 'rgba(226, 232, 240, 0.5)' },
            ticks: { font: { size: 12, weight: 'bold' }, color: '#0f172a' }
          }
        }
      }
    });
  }

  // Fungsi Filter Dinamis Wilayah KPPN di Aceh
  function filterChartSatkerBUN(code) {
    currentBunFilter = code;
    const item = bunMasterData[code] || bunMasterData['ALL'];

    if (chartSatkerBUNInstance) {
      chartSatkerBUNInstance.data.labels = item.labels;
      chartSatkerBUNInstance.data.datasets[0].data = item.pagu;
      chartSatkerBUNInstance.data.datasets[1].data = item.realisasi;

      const maxVal = Math.max(...item.pagu, ...item.realisasi, 100);
      const ceiling = Math.max(Math.ceil((maxVal * 1.15) / 500) * 500, 500);
      chartSatkerBUNInstance.options.scales.y.max = ceiling;
      chartSatkerBUNInstance.options.scales.y.ticks.stepSize = Math.max(Math.round(ceiling / 5 / 100) * 100, 100);

      chartSatkerBUNInstance.update();
    }

    // Update 3 KPI Cards BA BUN
    const elPagu = document.getElementById('bunCardPagu');
    const elSubPagu = document.getElementById('bunCardSubPagu');
    const elReal = document.getElementById('bunCardRealisasi');
    const elSubReal = document.getElementById('bunCardSubRealisasi');
    const elBlokir = document.getElementById('bunCardBlokir');
    const elSubBlokir = document.getElementById('bunCardSubBlokir');
    const elFoot = document.getElementById('labelSatkerBUNFoot');

    if (elPagu) elPagu.textContent = item.cardPagu;
    if (elSubPagu) elSubPagu.textContent = item.subPagu;
    if (elReal) elReal.textContent = item.cardRealisasi;
    if (elSubReal) elSubReal.textContent = item.subRealisasi;
    if (elBlokir) elBlokir.textContent = item.cardBlokir;
    if (elSubBlokir) elSubBlokir.textContent = item.subBlokir;
    if (elFoot) elFoot.textContent = item.footer;

    // Update Active State pada Pill Buttons
    document.querySelectorAll('.bun-filter-pill').forEach(btn => {
      if (btn.getAttribute('data-code') === code) {
        btn.classList.add('bg-purple-600', 'text-white', 'shadow-xs', 'border-purple-600');
        btn.classList.remove('bg-white', 'text-slate-700', 'hover:bg-purple-50', 'border-slate-200');
      } else {
        btn.classList.remove('bg-purple-600', 'text-white', 'shadow-xs', 'border-purple-600');
        btn.classList.add('bg-white', 'text-slate-700', 'hover:bg-purple-50', 'border-slate-200');
      }
    });

    const select = document.getElementById('filterSatkerBUN');
    if (select && select.value !== code) {
      select.value = code;
    }
  }

  // 5. CHART PAGU & REALISASI PER JENIS BELANJA (DENGAN TOGGLE NOMINAL VS PERSENTASE + RINGKASAN SERAPAN)
  let chartJenisBelanjaBarInstance = null;
  let currentJbMode = 'nominal';
  const jbDataMaster = @json($jenisBelanjaChart);

  function getJbPctData() {
    return (jbDataMaster.details || []).map((d, i) => {
      if (typeof d.persen_num === 'number') return d.persen_num;
      const p = jbDataMaster.pagu[i] || 0;
      const r = jbDataMaster.realisasi[i] || 0;
      return p > 0 ? parseFloat(((r / p) * 100).toFixed(2)) : 0;
    });
  }

  function initChartJenisBelanjaBar() {
    const ctx = document.getElementById('chartJenisBelanjaBar');
    if (!ctx) return;

    const jbData = jbDataMaster;
    const maxY = Math.ceil(Math.max(...jbData.pagu, ...jbData.realisasi, 1) * 1.2);

    chartJenisBelanjaBarInstance = new Chart(ctx.getContext('2d'), {
      type: 'bar',
      data: {
        labels: jbData.labels,
        datasets: [
          {
            label: 'Pagu',
            data: jbData.pagu,
            backgroundColor: '#1e40af',
            borderRadius: 6,
            barPercentage: 0.78,
            categoryPercentage: 0.68,
            maxBarThickness: 64,
            minBarLength: 6
          },
          {
            label: 'Realisasi',
            data: jbData.realisasi,
            backgroundColor: '#f59e0b',
            borderRadius: 6,
            barPercentage: 0.78,
            categoryPercentage: 0.68,
            maxBarThickness: 64,
            minBarLength: 6
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: { top: 14 } },
        plugins: {
          legend: { display: false },
          datalabels: {
            display: true,
            anchor: 'end',
            align: 'top',
            offset: 4,
            textAlign: 'center',
            color: '#0f172a',
            font: { weight: '800', size: 11.5 },
            formatter: function(value, context) {
              const idx = context.dataIndex;
              const d = jbData.details[idx];
              if (currentJbMode === 'percent') {
                return `${value.toFixed(2).replace('.', ',')}%`;
              }
              if (context.datasetIndex === 0) {
                return jbData.pagu_badges[idx];
              }
              const pctTxt = d && d.persen ? `(${d.persen})` : '';
              return pctTxt ? [jbData.realisasi_badges[idx], pctTxt] : jbData.realisasi_badges[idx];
            }
          },
          tooltip: {
            callbacks: {
              label: function(ctx) {
                const idx = ctx.dataIndex;
                const d = jbData.details[idx];
                if (currentJbMode === 'percent') {
                  return d
                    ? ` Serapan: ${d.persen} (${d.realisasi} / ${d.pagu})`
                    : ` Serapan: ${ctx.parsed.y}%`;
                }
                if (!d) return ` ${ctx.dataset.label}: ${ctx.parsed.y} T`;
                return ctx.datasetIndex === 0
                  ? ` Pagu: ${d.pagu}`
                  : ` Realisasi: ${d.realisasi} (${d.persen})`;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: maxY,
            ticks: {
              stepSize: 2,
              callback: (v) => v + ' T',
              font: { size: 11.5, weight: '600' },
              color: '#334155'
            },
            grid: { color: 'rgba(226, 232, 240, 0.7)' }
          },
          x: {
            grid: { display: false },
            ticks: { font: { size: 12.5, weight: 'bold' }, color: '#0f172a' }
          }
        }
      }
    });
  }

  function switchJbView(mode) {
    if (!chartJenisBelanjaBarInstance) return;
    currentJbMode = mode;

    const btnNom = document.getElementById('btnJbNominal');
    const btnPct = document.getElementById('btnJbPercent');
    const legNom = document.getElementById('jbLegendNominal');
    const lblScale = document.getElementById('labelJbScale');

    if (mode === 'percent') {
      const pcts = getJbPctData();
      chartJenisBelanjaBarInstance.data.datasets = [{
        label: 'Persentase Serapan',
        data: pcts,
        backgroundColor: pcts.map(v => v >= 80 ? '#10b981' : (v >= 60 ? '#f59e0b' : '#ef4444')),
        borderRadius: 6,
        barPercentage: 0.55,
        categoryPercentage: 0.65,
        maxBarThickness: 72
      }];
      chartJenisBelanjaBarInstance.options.scales.y.max = 115;
      chartJenisBelanjaBarInstance.options.scales.y.ticks.stepSize = 20;
      chartJenisBelanjaBarInstance.options.scales.y.ticks.callback = (v) => v <= 100 ? v + '%' : '';

      if (btnPct) btnPct.className = 'px-2.5 py-1 rounded-md font-bold bg-white text-emerald-700 shadow-2xs transition-all';
      if (btnNom) btnNom.className = 'px-2.5 py-1 rounded-md font-semibold text-slate-500 hover:text-slate-800 transition-all';
      if (legNom) legNom.classList.add('hidden');
      if (lblScale) lblScale.textContent = 'Skala dalam Persentase Serapan (%)';
    } else {
      const maxY = Math.ceil(Math.max(...jbDataMaster.pagu, ...jbDataMaster.realisasi, 1) * 1.2);
      chartJenisBelanjaBarInstance.data.datasets = [
        {
          label: 'Pagu',
          data: jbDataMaster.pagu,
          backgroundColor: '#1e40af',
          borderRadius: 6,
          barPercentage: 0.78,
          categoryPercentage: 0.68,
          maxBarThickness: 64,
          minBarLength: 6
        },
        {
          label: 'Realisasi',
          data: jbDataMaster.realisasi,
          backgroundColor: '#f59e0b',
          borderRadius: 6,
          barPercentage: 0.78,
          categoryPercentage: 0.68,
          maxBarThickness: 64,
          minBarLength: 6
        }
      ];
      chartJenisBelanjaBarInstance.options.scales.y.max = maxY;
      chartJenisBelanjaBarInstance.options.scales.y.ticks.stepSize = 2;
      chartJenisBelanjaBarInstance.options.scales.y.ticks.callback = (v) => v + ' T';

      if (btnNom) btnNom.className = 'px-2.5 py-1 rounded-md font-bold bg-white text-blue-700 shadow-2xs transition-all';
      if (btnPct) btnPct.className = 'px-2.5 py-1 rounded-md font-semibold text-slate-500 hover:text-slate-800 transition-all';
      if (legNom) legNom.classList.remove('hidden');
      if (lblScale) lblScale.textContent = 'Skala dalam Triliun Rupiah';
    }

    chartJenisBelanjaBarInstance.update();
  }

  document.addEventListener('DOMContentLoaded', function() {
    Chart.register(ChartDataLabels);
    initChartRadarKppn();
    initChartTopKL();
    initChartSatkerBUN();
    initChartJenisBelanjaBar();
  });
</script>
@endpush
