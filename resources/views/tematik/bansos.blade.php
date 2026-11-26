@extends('layouts.app')

@section('title', (($summary['has_pagu'] ?? true) ? 'Bantuan Sosial (Sentra Darussa\'adah)' : 'Bantuan Sosial Kewilayahan Aceh') . ' - SIPANTAS Kanwil DJPb Provinsi Aceh')

@section('content')
@php
  $bansosHasPagu = $summary['has_pagu'] ?? true;
  $bansosKabKota = $summary['total_kabkota'] ?? ($summary['total_satker'] ?? 1);
  $bansosTopRealOut = collect($outputs)->sortByDesc('realisasi')->first();
  $bansosTopKpmOut = collect($outputs)->sortByDesc('penerima')->first();
@endphp
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
        <div class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider mt-1">Indonesian Treasury · Pengawasan Program Bantuan Sosial Provinsi Aceh</div>
      </div>
    </div>

    <div class="text-left sm:text-right border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-100">
      <div class="flex items-center sm:justify-end gap-2">
        <h1 class="text-sm sm:text-lg font-black text-slate-900 tracking-tight">{{ $bansosHasPagu ? 'Bantuan Sosial (Sentra Darussa\'adah)' : 'Bantuan Sosial Kewilayahan Aceh' }}</h1>
        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-300">{{ $summary['total_paket'] }} {{ $bansosHasPagu ? 'Paket' : 'Tahap' }}</span>
      </div>
      <p class="text-[10px] sm:text-[11px] text-slate-500 font-mono mt-0.5">
        @if($bansosHasPagu)
          T.A. 2026 · {{ $summary['total_satker'] }} Satker Pelaksana · {{ $summary['total_output'] }} Output Bantuan Aktif
        @else
          T.A. 2026 · {{ $bansosKabKota }} Kab/Kota · {{ $summary['total_output'] }} Skema Bansos ({{ collect($outputs)->pluck('kode_out')->implode(', ') }}) · Cut-off {{ $summary['cut_off'] ?? '25 November 2026' }}
        @endif
      </p>
    </div>
  </div>

  <!-- 2. TOP 3 EXECUTIVE KPI CARDS -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">

    @if($bansosHasPagu)
      <!-- Card 1: TOTAL PAGU -->
      <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-blue-600 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
        <div>
          <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1.5">TOTAL PAGU</div>
          <div class="flex items-baseline gap-1 text-blue-700 font-mono font-black tracking-tight whitespace-nowrap">
            <span class="text-xs sm:text-sm font-bold opacity-75">Rp</span>
            <span class="text-lg sm:text-2xl">{{ trim(str_replace('Rp', '', $summary['total_pagu_formatted'])) }}</span>
          </div>
        </div>
        <div class="text-[11px] text-slate-400 font-medium flex items-center justify-between pt-2.5 mt-2.5 border-t border-slate-100">
          <span>Alokasi Bantuan Sosial DIPA</span>
          <span class="font-bold text-blue-600 font-mono">100%</span>
        </div>
      </div>

      <!-- Card 2: TOTAL REALISASI -->
      <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-emerald-500 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
        <div>
          <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1.5">TOTAL REALISASI</div>
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
            <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1">PERSENTASE</div>
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
          <span>Target Nasional Q4: 90%</span>
          <span class="font-bold text-emerald-600">Sangat Tinggi (≥ 90%)</span>
        </div>
      </div>
    @else
      <!-- Card 1 (Kewilayahan): TOTAL REALISASI BANSOS -->
      <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-emerald-500 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
        <div>
          <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1.5">TOTAL REALISASI BANSOS</div>
          <div class="flex items-baseline gap-1 text-emerald-600 font-mono font-black tracking-tight whitespace-nowrap">
            <span class="text-xs sm:text-sm font-bold opacity-75">Rp</span>
            <span class="text-lg sm:text-2xl">{{ trim(str_replace('Rp', '', $summary['total_realisasi_formatted'])) }}</span>
          </div>
        </div>
        <div class="text-[11px] text-slate-400 font-medium flex items-center justify-between pt-2.5 mt-2.5 border-t border-slate-100">
          <span>Skema {{ collect($outputs)->pluck('kode_out')->implode(' · ') }}</span>
          <span class="font-bold text-emerald-600 font-mono">SINTESA Real</span>
        </div>
      </div>

      <!-- Card 2 (Kewilayahan): TOTAL PENERIMA MANFAAT (KPM) -->
      <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-blue-600 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
        <div>
          <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1.5">TOTAL PENERIMA MANFAAT (KPM)</div>
          <div class="flex items-baseline gap-1.5 text-blue-700 font-mono font-black tracking-tight whitespace-nowrap">
            <span class="text-lg sm:text-2xl">{{ $summary['total_penerima_formatted'] ?? number_format($summary['total_penerima'] ?? 0, 0, ',', '.') }}</span>
            <span class="text-xs sm:text-sm font-bold opacity-75 font-sans">KPM</span>
          </div>
        </div>
        <div class="text-[11px] text-slate-400 font-medium flex items-center justify-between pt-2.5 mt-2.5 border-t border-slate-100">
          <span>Sebaran {{ $bansosKabKota }} Kab/Kota Aceh</span>
          <span class="font-bold text-blue-600 font-mono">Data KPM</span>
        </div>
      </div>

      <!-- Card 3 (Kewilayahan): CAKUPAN WILAYAH & TAHAP -->
      <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-amber-500 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1">RINCIAN TAHAP SALUR</div>
            <div class="flex items-baseline gap-1.5 my-1">
              <span class="text-xl sm:text-3xl font-black text-amber-600 font-mono tracking-tight leading-none">{{ $summary['total_paket'] }}</span>
              <span class="text-xs font-bold text-slate-500">Tahap · {{ $bansosKabKota }} Kab/Kota</span>
            </div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-mono font-black text-sm shrink-0">
            100%
          </div>
        </div>
        <div class="text-[11px] text-slate-400 font-medium flex items-center justify-between pt-2 mt-2 border-t border-slate-100">
          <span>Cut-off: {{ $summary['cut_off'] ?? '25 November 2026' }}</span>
          <span class="font-bold text-emerald-600">Tersalur</span>
        </div>
      </div>
    @endif

  </div>

  <!-- 3. RESUME CAKUPAN & SKEMA BANTUAN SOSIAL -->
  <div class="space-y-3">
    <div class="flex items-center gap-2 text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase">
      <span class="text-base">🤝</span>
      <span>RESUME CAKUPAN & SKEMA BANTUAN SOSIAL</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
      <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-blue-600 p-4 shadow-2xs hover:shadow-md transition-all flex items-center justify-between">
        <div>
          <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">{{ $bansosHasPagu ? 'Total Satker Pelaksana' : 'Cakupan Kab/Kota' }}</span>
          <div class="flex items-baseline gap-1.5 mt-1">
            <span class="text-2xl sm:text-3xl font-black text-blue-700 font-mono">{{ $bansosHasPagu ? $summary['total_satker'] : $bansosKabKota }}</span>
            <span class="text-xs font-bold text-slate-500">{{ $bansosHasPagu ? 'Satker' : 'Kab/Kota' }}</span>
          </div>
          <span class="text-[10px] text-slate-400 mt-1 block">{{ $bansosHasPagu ? 'Satker Pelaksana Bansos Aceh' : 'Seluruh Provinsi Aceh (' . $summary['total_paket'] . ' Baris Tahap)' }}</span>
        </div>
        <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
        </div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-amber-500 p-4 shadow-2xs hover:shadow-md transition-all flex items-center justify-between">
        <div>
          <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">{{ $bansosHasPagu ? 'Total Output Aktif' : 'Skema Bansos Aktif' }}</span>
          <div class="flex items-baseline gap-1.5 mt-1">
            <span class="text-2xl sm:text-3xl font-black text-amber-600 font-mono">{{ $summary['total_output'] }}</span>
            <span class="text-xs font-bold text-slate-500">{{ $bansosHasPagu ? 'Output' : 'Skema' }}</span>
          </div>
          <span class="text-[10px] text-slate-400 mt-1 block">{{ collect($outputs)->pluck('kode_out')->map(fn($c) => '['.$c.']')->implode(' · ') }}</span>
        </div>
        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
        </div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-emerald-500 p-4 shadow-2xs hover:shadow-md transition-all flex items-center justify-between">
        <div>
          <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">{{ $bansosHasPagu ? 'Total Pagu DIPA' : 'Total Realisasi Bansos' }}</span>
          <div class="flex items-baseline gap-1.5 mt-1">
            <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-mono">{{ $bansosHasPagu ? $summary['total_pagu_short'] : ($summary['total_realisasi_short'] ?? '-') }}</span>
            <span class="text-xs font-bold text-slate-500">Rupiah</span>
          </div>
          <span class="text-[10px] text-slate-400 mt-1 block">
            @if($bansosHasPagu)
              Realisasi {{ number_format($summary['persen_realisasi'], 2, ',', '.') }}% (Sisa Rp {{ $summary['sisa_pagu_short'] }})
            @else
              {{ $summary['total_penerima_formatted'] ?? '0' }} KPM Penerima Manfaat
            @endif
          </span>
        </div>
        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
      </div>
    </div>
  </div>

  <!-- 4. INTERACTIVE CHART & OUTPUT DISTRIBUTION -->
  <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-purple-600 p-4 sm:p-5 shadow-sm space-y-4">

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-slate-100">
      <div>
        <h2 class="text-xs sm:text-sm font-black text-slate-900 tracking-tight uppercase flex items-center gap-2">
          <span>📊</span>
          <span>{{ $bansosHasPagu ? 'Pagu & Realisasi per Output Bantuan Sosial' : 'Realisasi Penyaluran per Skema Bantuan Sosial' }}</span>
        </h2>
        <p class="text-[11px] text-slate-400 mt-0.5">
          💡 Klik batang grafik atau kartu skema untuk menyaring tabel {{ count($items) }} rincian di bawah
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <!-- Toggle Skala -->
        <div id="bansosScaleToggle" class="inline-flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200">
          <button type="button" id="btnBansosNominal" onclick="switchBansosScale('nominal')"
                  class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all">
            Nominal (Rp)
          </button>
          <button type="button" id="btnBansosPersen" onclick="switchBansosScale('persen')"
                  class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all">
            {{ $bansosHasPagu ? 'Persentase (%)' : 'Porsi (%)' }}
          </button>
        </div>

        <!-- Toggle Mode Grafik vs Kartu -->
        <div class="inline-flex items-center p-1 bg-slate-200/70 rounded-xl border border-slate-200">
          <button type="button" id="btnBansosChart" onclick="switchBansosView('chart')"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all">
            <span>📊 Grafik</span>
          </button>
          <button type="button" id="btnBansosCards" onclick="switchBansosView('cards')"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all">
            <span>⊞ Skema ({{ count($outputs) }})</span>
          </button>
          <button type="button" id="btnBansosShowcase" onclick="switchBansosView('showcase')"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all">
            <span>🎁 {{ count($items) }} {{ $bansosHasPagu ? 'Paket' : 'Tahap' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Executive Insight Strip -->
    <div class="flex flex-wrap items-center justify-between gap-2 text-[11px]">
      <div class="flex flex-wrap items-center gap-2">
        @if($bansosTopRealOut)
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">
          🏆 Penyaluran Terbesar: [{{ $bansosTopRealOut['kode_out'] }}] {{ $bansosTopRealOut['output_singkat'] }} (Rp {{ $bansosTopRealOut['realisasi'] >= 1e12 ? number_format($bansosTopRealOut['realisasi']/1e12, 2, ',', '.') . ' T' : number_format($bansosTopRealOut['realisasi']/1e9, 1, ',', '.') . ' M' }} | {{ number_format($bansosTopRealOut['pct'], 2, ',', '.') }}%)
        </span>
        @endif
        @if(!$bansosHasPagu && $bansosTopKpmOut)
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-bold">
          👥 KPM Terbanyak: [{{ $bansosTopKpmOut['kode_out'] }}] {{ number_format($bansosTopKpmOut['penerima'] ?? 0, 0, ',', '.') }} KPM
        </span>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 border border-purple-200 font-bold">
          📍 Cakupan: {{ $bansosKabKota }} Kab/Kota · {{ $summary['total_paket'] }} Rincian
        </span>
        @endif
      </div>
      <div id="bansosChartLegend" class="flex items-center gap-3 font-semibold text-slate-500">
        @if($bansosHasPagu)
          <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-sky-300"></span> Pagu DIPA</span>
        @endif
        <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-blue-600"></span> Realisasi</span>
      </div>
    </div>

    <!-- VIEW 1: Modern Horizontal Bar Chart -->
    <div id="bansosChartContainer" class="relative w-full h-[220px] sm:h-[240px]">
      <canvas id="chartBantuanSosial"></canvas>
    </div>

    <!-- VIEW 2: Output / Skema Cards Grid -->
    <div id="bansosCardsContainer" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
      @foreach ($outputs as $o)
        @php
          $badge = $o['pct'] >= 95
            ? ['lbl' => 'Sangat Tinggi', 'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'bar' => 'bg-emerald-500', 'txt' => 'text-emerald-600']
            : ($o['pct'] >= 40
              ? ['lbl' => 'Dominan', 'cls' => 'bg-blue-50 text-blue-700 border-blue-200', 'bar' => 'bg-blue-600', 'txt' => 'text-blue-600']
              : ['lbl' => 'Optimal', 'cls' => 'bg-purple-50 text-purple-700 border-purple-200', 'bar' => 'bg-purple-600', 'txt' => 'text-purple-600']);
        @endphp
        <div onclick="filterBansosByOutput('{{ $o['kode_out'] }}')"
             class="bansos-out-card bg-slate-50/60 hover:bg-white rounded-xl border border-slate-200 p-4 shadow-2xs hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer flex flex-col justify-between"
             data-bansos-out="{{ $o['kode_out'] }}">
          <div>
            <div class="flex items-start justify-between gap-2 mb-2.5">
              <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-md bg-purple-600 text-white font-mono text-[11px] font-black shrink-0">{{ $o['kode_out'] }}</span>
                <span class="text-xs font-extrabold text-slate-800 leading-snug">{{ $o['output_singkat'] }}</span>
              </div>
              <span class="shrink-0 text-[9px] font-bold px-2 py-0.5 rounded-full border {{ $badge['cls'] }}">{{ $bansosHasPagu ? $badge['lbl'] : number_format($o['pct'], 1, ',', '.') . '% Porsi' }}</span>
            </div>

            <p class="text-[11px] text-slate-500 line-clamp-2 mb-3 leading-relaxed">
              {{ $o['deskripsi'] }}
            </p>

            <div class="space-y-1.5 text-xs font-mono bg-white p-3 rounded-lg border border-slate-100">
              @if($bansosHasPagu)
                <div class="flex items-baseline justify-between gap-2">
                  <span class="text-[10px] font-sans font-semibold text-slate-400">Pagu DIPA:</span>
                  <span class="font-bold text-slate-800 text-[11px] whitespace-nowrap">Rp {{ number_format($o['pagu'], 0, ',', '.') }}</span>
                </div>
              @endif
              <div class="flex items-baseline justify-between gap-2">
                <span class="text-[10px] font-sans font-semibold text-slate-400">Realisasi:</span>
                <span class="font-black {{ $badge['txt'] }} text-[11px] whitespace-nowrap">Rp {{ number_format($o['realisasi'], 0, ',', '.') }}</span>
              </div>
              <div class="flex items-baseline justify-between gap-2 pt-1 border-t border-slate-100">
                @if($bansosHasPagu)
                  <span class="text-[10px] font-sans font-semibold text-slate-400">Sisa Pagu:</span>
                  <span class="font-bold text-slate-500 text-[11px] whitespace-nowrap">Rp {{ number_format($o['sisa'], 0, ',', '.') }}</span>
                @else
                  <span class="text-[10px] font-sans font-semibold text-slate-400">Penerima Manfaat:</span>
                  <span class="font-bold text-blue-700 text-[11px] whitespace-nowrap">{{ number_format($o['penerima'] ?? 0, 0, ',', '.') }} KPM</span>
                @endif
              </div>
            </div>
          </div>

          <div class="mt-3 pt-2.5 border-t border-slate-200/70">
            <div class="flex items-center justify-between text-xs mb-1.5">
              <span class="text-[10px] text-slate-500 font-semibold">{{ $o['jumlah_paket'] }} {{ $bansosHasPagu ? 'Paket Bantuan' : 'Baris Tahap Salur' }}</span>
              <span class="font-mono font-black {{ $badge['txt'] }} text-xs">{{ number_format($o['pct'], 2, ',', '.') }}%</span>
            </div>
            <div class="w-full bg-slate-200/80 rounded-full h-2 overflow-hidden">
              <div class="{{ $badge['bar'] }} h-2 rounded-full" style="width: {{ min($o['pct'], 100) }}%"></div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <!-- VIEW 3: Rincian Bantuan Showcase Grid -->
    <div id="bansosShowcaseContainer" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 max-h-[560px] overflow-y-auto pr-1">
      @foreach ($items as $it)
        @php
          $badge = $it['pct'] >= 95
            ? ['lbl' => 'Optimal', 'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'bar' => 'bg-emerald-500', 'txt' => 'text-emerald-600']
            : ($it['pct'] >= 90
              ? ['lbl' => 'On Track', 'cls' => 'bg-blue-50 text-blue-700 border-blue-200', 'bar' => 'bg-blue-600', 'txt' => 'text-blue-600']
              : ['lbl' => 'Perhatian', 'cls' => 'bg-amber-50 text-amber-700 border-amber-200', 'bar' => 'bg-amber-500', 'txt' => 'text-amber-600']);
        @endphp
        <div onclick="openBansosModalByIndex({{ $loop->index }})"
             class="bg-slate-50/60 hover:bg-white rounded-xl border border-slate-200 p-4 shadow-2xs hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer flex flex-col justify-between">
          <div>
            <div class="flex items-start justify-between gap-2 mb-2">
              <div class="flex items-center gap-1.5">
                <span class="px-2 py-0.5 rounded font-mono text-[10px] font-black bg-slate-100 text-slate-800 border border-slate-300">{{ $it['kode_out'] }}</span>
                <span class="px-1.5 py-0.5 rounded font-mono text-[10px] font-bold bg-slate-100 text-slate-600">{{ $it['kegiatan_kode'] }}</span>
              </div>
              <span class="shrink-0 text-[9px] font-bold px-2 py-0.5 rounded-full border {{ $badge['cls'] }}">{{ $it['status'] }}</span>
            </div>

            <h3 class="text-xs font-black text-slate-800 leading-snug line-clamp-2 mb-1.5">
              {{ $it['kegiatan_nama'] }}
            </h3>

            <p class="text-[11px] text-slate-400 line-clamp-2 leading-relaxed mb-3">
              {{ $it['deskripsi'] }}
            </p>

            <div class="space-y-1 text-xs font-mono bg-white p-2.5 rounded-lg border border-slate-100">
              @if($bansosHasPagu)
                <div class="flex items-baseline justify-between gap-2">
                  <span class="text-[10px] font-sans font-semibold text-slate-400">Pagu:</span>
                  <span class="font-bold text-slate-800 text-[11px] whitespace-nowrap">Rp {{ number_format($it['pagu'], 0, ',', '.') }}</span>
                </div>
              @else
                <div class="flex items-baseline justify-between gap-2">
                  <span class="text-[10px] font-sans font-semibold text-slate-400">Penerima:</span>
                  <span class="font-bold text-blue-700 text-[11px] whitespace-nowrap">{{ number_format($it['penerima'] ?? 0, 0, ',', '.') }} KPM</span>
                </div>
              @endif
              <div class="flex items-baseline justify-between gap-2">
                <span class="text-[10px] font-sans font-semibold text-slate-400">Realisasi:</span>
                <span class="font-black {{ $badge['txt'] }} text-[11px] whitespace-nowrap">Rp {{ number_format($it['realisasi'], 0, ',', '.') }}</span>
              </div>
            </div>
          </div>

          <div class="mt-3 pt-2.5 border-t border-slate-200/70">
            <div class="flex items-center justify-between text-xs mb-1.5">
              <span class="text-[10px] text-slate-500 font-semibold">{{ $it['label_singkat'] }}</span>
              <span class="font-mono font-black {{ $badge['txt'] }} text-xs">{{ number_format($it['pct'], 2, ',', '.') }}%</span>
            </div>
            <div class="w-full bg-slate-200/80 rounded-full h-1.5 overflow-hidden">
              <div class="{{ $badge['bar'] }} h-1.5 rounded-full" style="width: {{ min($it['pct'], 100) }}%"></div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

  </div>

  <!-- 5. SECTION TABEL RINCIAN BANTUAN SOSIAL -->
  <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">

    <!-- Toolbar Search + Filter Pills + Export CSV -->
    <div class="p-3.5 sm:p-4 border-b border-slate-200/80 bg-slate-50/70 space-y-3">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <div class="w-full lg:w-96 relative">
          <input type="text"
                 id="bansosSearchInput"
                 onkeyup="filterBansosTable()"
                 placeholder="🔍 Cari Kab/Kota, skema ({{ collect($outputs)->pluck('kode_out')->implode(', ') }}), tahap, atau nominal..."
                 class="w-full bg-white border border-slate-200 rounded-xl pl-3.5 pr-10 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-600/20 focus:border-purple-600 transition-all font-medium shadow-2xs">
        </div>

        <div class="flex items-center gap-1.5 text-xs overflow-x-auto pb-1 lg:pb-0 no-scrollbar sm:flex-wrap">
          <button onclick="resetBansosFilters()" id="btnBansosFilterAll" class="shrink-0 bansos-filter-btn px-3 py-1.5 bg-purple-600 text-white rounded-lg font-bold shadow-xs transition-all border border-purple-600">
            Semua ({{ count($items) }})
          </button>
          @foreach ($outputs as $o)
            <button onclick="filterBansosByOutput('{{ $o['kode_out'] }}', this)" class="shrink-0 bansos-filter-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-purple-50 hover:text-purple-700 rounded-lg font-semibold transition-all border border-slate-200">
              [{{ $o['kode_out'] }}] {{ $o['output_singkat'] }} ({{ $o['jumlah_paket'] }})
            </button>
          @endforeach
          @if($bansosHasPagu)
            <button onclick="filterBansosStatus('optimal', this)" class="shrink-0 bansos-filter-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg font-semibold transition-all border border-slate-200">
              Serapan &ge; 95%
            </button>
          @endif
          <a href="{{ route('tematik.bansos.export') }}" class="shrink-0 sm:ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-semibold shadow-xs transition-all">
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span>Unduh CSV</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Active Filter Info Banner -->
    <div id="bansosFilterBanner" class="hidden px-4 py-2 bg-purple-50 border-b border-purple-100 flex items-center justify-between text-xs text-purple-800">
      <span id="bansosFilterText">Filter aktif...</span>
      <button onclick="resetBansosFilters()" class="text-[11px] font-bold text-purple-700 hover:underline">Reset Filter ✕</button>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse" id="bansosDataTable">
        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-extrabold border-b border-slate-200/90 text-[10px]">
          <tr>
            <th class="py-3 px-2.5 w-10 text-center select-none">NO</th>
            <th class="py-3 px-2.5 w-20 select-none">KODE SATKER</th>
            <th class="py-3 px-3 min-w-[180px] select-none">{{ $bansosHasPagu ? 'NAMA SATKER' : 'SATKER & KAB/KOTA' }}</th>
            <th class="py-3 px-3 min-w-[240px] select-none">{{ $bansosHasPagu ? 'PROGRAM & KEGIATAN' : 'SKEMA & TAHAP PENYALURAN' }}</th>
            <th onclick="sortBansosTable('pagu')" class="py-3 px-2.5 text-right whitespace-nowrap cursor-pointer hover:text-purple-600 transition-colors select-none">
              <span>{{ $bansosHasPagu ? 'PAGU' : 'PENERIMA (KPM)' }}</span> <span id="bansosSort-pagu" class="text-[9px] text-slate-400">↕</span>
            </th>
            <th onclick="sortBansosTable('realisasi')" class="py-3 px-2.5 text-right whitespace-nowrap cursor-pointer hover:text-purple-600 transition-colors select-none">
              <span>REALISASI</span> <span id="bansosSort-realisasi" class="text-[9px] text-slate-400">↕</span>
            </th>
            <th onclick="sortBansosTable('pct')" class="py-3 px-2.5 text-left w-28 whitespace-nowrap cursor-pointer hover:text-purple-600 transition-colors select-none">
              <span>{{ $bansosHasPagu ? '% REALISASI' : 'STATUS' }}</span> <span id="bansosSort-pct" class="text-[9px] text-slate-400">↕</span>
            </th>
            <th class="py-3 px-2 text-center w-14 select-none">AKSI</th>
          </tr>
        </thead>
        <tbody id="bansosTableBody" class="divide-y divide-slate-100 text-slate-700">
          @foreach ($items as $idx => $it)
            @php
              $sisa = max(0, $it['pagu'] - $it['realisasi']);
              $barColor = $it['pct'] >= 95 ? 'bg-emerald-500' : ($it['pct'] >= 90 ? 'bg-blue-600' : 'bg-amber-500');
              $pctColor = $it['pct'] >= 95 ? 'text-emerald-700' : ($it['pct'] >= 90 ? 'text-blue-700' : 'text-amber-700');
              $searchStr = strtolower($it['kode_satker'] . ' ' . $it['nama_satker'] . ' ' . $it['kl'] . ' ' . $it['wilayah'] . ' ' . $it['kode_out'] . ' ' . $it['kegiatan'] . ' ' . $it['output'] . ' ' . $it['program'] . ' ' . $it['label_singkat']);
            @endphp
            <tr onclick="openBansosModal(this)"
                class="bansos-row hover:bg-purple-50/40 transition-colors cursor-pointer group"
                data-index="{{ $idx }}"
                data-search="{{ $searchStr }}"
                data-satker-kode="{{ $it['kode_satker'] }}"
                data-out-kode="{{ $it['kode_out'] }}"
                data-status="{{ strtolower($it['status']) }}"
                data-pagu="{{ $bansosHasPagu ? $it['pagu'] : ($it['penerima'] ?? 0) }}"
                data-penerima="{{ $it['penerima'] ?? 0 }}"
                data-realisasi="{{ $it['realisasi'] }}"
                data-pct="{{ $it['pct'] }}"
                data-satker="{{ $it['nama_satker'] }}"
                data-kl="{{ $it['kl'] }}"
                data-wilayah="{{ $it['wilayah'] }}"
                data-kegiatan="{{ $it['kegiatan'] }}"
                data-output="{{ $it['output'] }}"
                data-program="{{ $it['program'] }}"
                data-deskripsi="{{ $it['deskripsi'] }}"
                data-pagu-fmt="{{ $bansosHasPagu ? ('Rp ' . number_format($it['pagu'], 0, ',', '.')) : (number_format($it['penerima'] ?? 0, 0, ',', '.') . ' KPM') }}"
                data-real-fmt="Rp {{ number_format($it['realisasi'], 0, ',', '.') }}"
                data-sisa-fmt="{{ $bansosHasPagu ? ('Rp ' . number_format($sisa, 0, ',', '.')) : ($it['wilayah'] ?? '-') }}">
              <td class="py-3 px-2.5 text-center font-mono text-slate-400 font-semibold">{{ $it['no'] }}</td>
              <td class="py-3 px-2.5 font-mono font-extrabold text-slate-900">{{ $it['kode_satker'] }}</td>
              <td class="py-3 px-3">
                <div class="font-extrabold text-slate-900 group-hover:text-purple-700 transition-colors leading-snug">
                  {{ $bansosHasPagu ? $it['nama_satker'] : $it['wilayah'] }}
                </div>
                <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wide mt-0.5">
                  {{ $it['kl'] }} • {{ $bansosHasPagu ? $it['wilayah'] : $it['nama_satker'] }}
                </div>
              </td>
              <td class="py-3 px-3">
                <div class="text-[11px] text-slate-700 leading-snug">
                  <span class="font-bold text-slate-500">{{ $bansosHasPagu ? 'Kegiatan:' : 'Tahap:' }}</span> <strong class="text-slate-900">{{ $it['kegiatan'] }}</strong>
                </div>
                <div class="flex items-start gap-1.5 mt-1">
                  <span class="shrink-0 px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200 font-mono text-[10px] font-black leading-none mt-0.5">
                    {{ $it['kode_out'] }}
                  </span>
                  <span class="font-bold text-slate-800 leading-snug">{{ $it['output'] }}</span>
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5 leading-snug">
                  Program: {{ $it['program'] }}
                </div>
              </td>
              <td class="py-3 px-2.5 text-right font-mono font-bold {{ $bansosHasPagu ? 'text-slate-900' : 'text-blue-700' }} whitespace-nowrap">
                {{ $bansosHasPagu ? ('Rp ' . number_format($it['pagu'], 0, ',', '.')) : (number_format($it['penerima'] ?? 0, 0, ',', '.') . ' KPM') }}
              </td>
              <td class="py-3 px-2.5 text-right font-mono font-extrabold text-emerald-600 whitespace-nowrap">
                Rp {{ number_format($it['realisasi'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-2.5">
                <div class="flex items-center justify-between font-mono text-[11px] font-black {{ $pctColor }} mb-1">
                  <span>{{ number_format($it['pct'], 2, '.', ',') }}%</span>
                  <span class="text-[9px] px-1.5 py-0.2 rounded font-sans font-bold {{ $it['pct'] >= 95 ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">{{ $it['status'] }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                  <div class="{{ $barColor }} h-2 rounded-full transition-all duration-300" style="width: {{ min($it['pct'], 100) }}%"></div>
                </div>
              </td>
              <td class="py-3 px-2 text-center">
                <button type="button" class="px-2 py-1 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 transition-colors">
                  Detail
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Footer Counter -->
    <div class="px-4 py-3 bg-slate-50/70 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] text-slate-500">
      <span id="bansosCountInfo">Menampilkan <strong class="text-slate-800">{{ count($items) }}</strong> dari {{ count($items) }} rincian Bantuan Sosial</span>
      <span class="font-mono text-[10px] text-slate-400">Sumber Data SINTESA Bansos Kewilayahan · Kanwil DJPb Provinsi Aceh</span>
    </div>

  </div>

  <!-- 6. MODAL DETAIL INSPECTOR -->
  <div id="bansosDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="relative w-full max-w-xl max-h-[92vh] flex flex-col bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto">

      <div class="p-4 sm:p-5 bg-gradient-to-r from-[#0b132b] via-[#1c2541] to-[#0f172a] border-b border-slate-700/80 text-white flex items-start justify-between gap-3 shrink-0">
        <div>
          <div class="flex flex-wrap items-center gap-2 mb-1.5">
            <span id="mbKode" class="px-2 py-0.5 rounded-md font-mono text-xs font-black bg-purple-500/20 text-purple-200 border border-purple-400/30">-</span>
            <span id="mbOutBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30">Skema Bansos</span>
            <span id="mbStatusBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">Tersalur</span>
          </div>
          <h3 id="mbSatker" class="text-sm sm:text-base font-black text-white leading-snug">Detail Bansos</h3>
          <p id="mbKlWilayah" class="text-xs text-slate-300 mt-1 font-medium">KEMENTERIAN SOSIAL</p>
        </div>
        <button onclick="closeBansosModal()" class="p-1 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white transition-colors shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1">
        <div class="p-3.5 rounded-xl bg-purple-50/70 border border-purple-100 space-y-1.5 text-xs">
          <div>
            <span class="text-[10px] font-bold text-purple-700 uppercase tracking-wider block">Kegiatan Bantuan Sosial</span>
            <span id="mbKegiatan" class="font-extrabold text-slate-900 text-xs sm:text-sm"></span>
          </div>
          <div class="pt-1 border-t border-purple-100">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Output DIPA</span>
            <span id="mbOutput" class="text-slate-800 font-semibold"></span>
          </div>
          <div class="pt-1 border-t border-purple-100">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Program APBN</span>
            <span id="mbProgram" class="text-slate-600 font-medium"></span>
          </div>
          <div class="pt-1 border-t border-purple-100">
            <span class="text-[10px] font-bold text-purple-700 uppercase tracking-wider block">Fokus Layanan & Penerima Manfaat</span>
            <p id="mbDeskripsi" class="text-slate-700 leading-relaxed font-normal mt-0.5"></p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
            <div class="text-[10px] font-bold text-slate-500 uppercase">{{ $bansosHasPagu ? 'PAGU DIPA' : 'PENERIMA (KPM)' }}</div>
            <div id="mbPagu" class="text-xs sm:text-sm font-black text-slate-900 font-mono mt-0.5">0</div>
          </div>
          <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/80">
            <div class="text-[10px] font-bold text-emerald-700 uppercase">REALISASI</div>
            <div id="mbReal" class="text-xs sm:text-sm font-black text-emerald-600 font-mono mt-0.5">Rp 0</div>
          </div>
          <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/80">
            <div class="text-[10px] font-bold text-amber-700 uppercase">{{ $bansosHasPagu ? 'SISA PAGU' : 'WILAYAH SALUR' }}</div>
            <div id="mbSisa" class="text-xs sm:text-sm font-black text-amber-600 font-mono mt-0.5">-</div>
          </div>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1.5">
          <div class="flex items-center justify-between text-xs">
            <span class="font-semibold text-slate-600">Capaian Serapan Paket:</span>
            <span id="mbPct" class="font-mono font-black text-slate-900 text-sm">0%</span>
          </div>
          <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
            <div id="mbBar" class="bg-purple-600 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
          </div>
        </div>
      </div>

      <div class="p-3.5 sm:p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
        <span class="text-[10px] text-slate-400 font-mono">Bantuan Sosial · Kanwil DJPb Provinsi Aceh</span>
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <button onclick="copyBansosInfo()" id="btnCopyBansos" class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-xs font-bold text-slate-700 transition-colors">
            Salin Info
          </button>
          <button onclick="closeBansosModal()" class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-xs font-bold text-white transition-colors">
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
  const bansosHasPagu = @json($summary['has_pagu'] ?? true);
  const bansosOutputsData = @json($outputs);
  const bansosItemsData = @json($items);
  let bansosChart = null;
  let activeBansosScale = 'nominal';
  let activeBansosOut = 'all';
  let activeBansosStatus = 'all';

  function formatBansosRp(val) {
    return 'Rp ' + Math.round(val).toLocaleString('id-ID');
  }

  function renderBansosChart() {
    const canvas = document.getElementById('chartBantuanSosial');
    if (!canvas || typeof Chart === 'undefined') return;
    if (bansosChart) bansosChart.destroy();

    const labels = bansosOutputsData.map(o => `[${o.kode_out}] ${o.output_singkat}`);
    const isNominal = activeBansosScale === 'nominal';

    const datasets = isNominal
      ? (bansosHasPagu
          ? [
              {
                label: 'Pagu DIPA',
                data: bansosOutputsData.map(o => +(o.pagu / 1e9).toFixed(3)),
                backgroundColor: 'rgba(125, 211, 252, 0.85)',
                borderColor: '#38bdf8',
                borderWidth: 1,
                borderRadius: 5,
                barPercentage: 0.65,
                categoryPercentage: 0.75,
              },
              {
                label: 'Realisasi',
                data: bansosOutputsData.map(o => +(o.realisasi / 1e9).toFixed(3)),
                backgroundColor: 'rgba(37, 99, 235, 0.95)',
                borderColor: '#1d4ed8',
                borderWidth: 1,
                borderRadius: 5,
                barPercentage: 0.65,
                categoryPercentage: 0.75,
              }
            ]
          : [
              {
                label: 'Realisasi Penyaluran',
                data: bansosOutputsData.map(o => +(o.realisasi / 1e9).toFixed(2)),
                backgroundColor: 'rgba(37, 99, 235, 0.95)',
                borderColor: '#1d4ed8',
                borderWidth: 1,
                borderRadius: 5,
                barPercentage: 0.55,
                categoryPercentage: 0.65,
              }
            ])
      : [
          {
            label: bansosHasPagu ? 'Realisasi (%)' : 'Porsi Penyaluran (%)',
            data: bansosOutputsData.map(o => +(o.pct).toFixed(2)),
            backgroundColor: bansosOutputsData.map(o => o.pct >= 50 ? 'rgba(16, 185, 129, 0.9)' : 'rgba(147, 51, 234, 0.9)'),
            borderColor: bansosOutputsData.map(o => o.pct >= 50 ? '#059669' : '#7e22ce'),
            borderWidth: 1,
            borderRadius: 5,
            barPercentage: 0.55,
            categoryPercentage: 0.65,
          }
        ];

    bansosChart = new Chart(canvas, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: datasets
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        onClick: (evt, elements) => {
          if (elements.length > 0) {
            const idx = elements[0].index;
            const clickedOut = bansosOutputsData[idx].kode_out;
            filterBansosByOutput(clickedOut);
          }
        },
        interaction: {
          mode: 'index',
          intersect: false
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.95)',
            titleFont: { size: 13, weight: 'bold' },
            bodyFont: { size: 12.5 },
            padding: 12,
            cornerRadius: 8,
            callbacks: {
              label: function(ctx) {
                const idx = ctx.dataIndex;
                const out = bansosOutputsData[idx];
                if (isNominal) {
                  const val = (bansosHasPagu && ctx.datasetIndex === 0) ? out.pagu : out.realisasi;
                  const unit = val >= 1e12 ? `${(val / 1e12).toFixed(2)} Triliun` : `${(val / 1e9).toFixed(2)} Miliar`;
                  const extra = (!bansosHasPagu && out.penerima) ? ` · ${Number(out.penerima).toLocaleString('id-ID')} KPM` : '';
                  return ` ${ctx.dataset.label}: ${formatBansosRp(val)} (${unit}${extra})`;
                } else {
                  return bansosHasPagu
                    ? ` Realisasi: ${out.pct.toFixed(2)}% (Sisa: ${formatBansosRp(out.sisa)})`
                    : ` Porsi Penyaluran: ${out.pct.toFixed(2)}% (${Number(out.penerima || 0).toLocaleString('id-ID')} KPM)`;
                }
              }
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            grid: { color: '#e2e8f0' },
            ticks: {
              font: { size: 12, family: 'monospace', weight: 'bold' },
              color: '#334155',
              callback: function(v) {
                if (!isNominal) return v + '%';
                return v >= 1000 ? (v / 1000).toFixed(1) + ' T' : v + ' M';
              }
            },
            max: isNominal ? undefined : 105
          },
          y: {
            grid: { display: false },
            ticks: {
              font: { size: 12, weight: '800' },
              color: '#0f172a'
            }
          }
        }
      }
    });
  }

  function switchBansosScale(mode) {
    activeBansosScale = mode;
    const btnNom = document.getElementById('btnBansosNominal');
    const btnPct = document.getElementById('btnBansosPersen');
    const legend = document.getElementById('bansosChartLegend');

    if (mode === 'nominal') {
      btnNom.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all';
      btnPct.className = 'px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all';
      if (legend) {
        legend.innerHTML = bansosHasPagu
          ? `
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-sky-300"></span> Pagu DIPA</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-blue-600"></span> Realisasi</span>
          `
          : `
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-blue-600"></span> Realisasi</span>
          `;
      }
    } else {
      btnPct.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all';
      btnNom.className = 'px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all';
      if (legend) {
        legend.innerHTML = `
          <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-blue-600"></span> ${bansosHasPagu ? 'Persentase Realisasi (%)' : 'Porsi Penyaluran (%)'}</span>
        `;
      }
    }
    renderBansosChart();
  }

  function switchBansosView(mode) {
    const cChart = document.getElementById('bansosChartContainer');
    const cCards = document.getElementById('bansosCardsContainer');
    const cShow = document.getElementById('bansosShowcaseContainer');
    const bChart = document.getElementById('btnBansosChart');
    const bCards = document.getElementById('btnBansosCards');
    const bShow = document.getElementById('btnBansosShowcase');
    const sToggle = document.getElementById('bansosScaleToggle');

    cChart.classList.add('hidden');
    cCards.classList.add('hidden');
    cShow.classList.add('hidden');

    bChart.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all';
    bCards.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all';
    bShow.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-semibold text-slate-600 hover:text-slate-900 transition-all';

    if (mode === 'chart') {
      cChart.classList.remove('hidden');
      bChart.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold bg-white text-blue-700 shadow-2xs transition-all';
      sToggle.classList.remove('hidden');
      renderBansosChart();
    } else if (mode === 'cards') {
      cCards.classList.remove('hidden');
      bCards.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold bg-white text-purple-700 shadow-2xs transition-all';
      sToggle.classList.add('hidden');
    } else {
      cShow.classList.remove('hidden');
      bShow.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold bg-white text-indigo-700 shadow-2xs transition-all';
      sToggle.classList.add('hidden');
    }
  }

  function filterBansosByOutput(kode, btnElement = null) {
    activeBansosOut = kode;
    activeBansosStatus = 'all';

    // Update filter buttons
    document.querySelectorAll('.bansos-filter-btn').forEach(b => {
      b.className = 'shrink-0 bansos-filter-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-purple-50 hover:text-purple-700 rounded-lg font-semibold transition-all border border-slate-200';
    });
    if (btnElement) {
      btnElement.className = 'shrink-0 bansos-filter-btn px-3 py-1.5 bg-purple-600 text-white rounded-lg font-bold shadow-xs transition-all border border-purple-600';
    }

    // Update Banner
    const banner = document.getElementById('bansosFilterBanner');
    const bText = document.getElementById('bansosFilterText');
    const matchedOut = bansosOutputsData.find(o => o.kode_out === kode);
    if (banner && bText) {
      banner.classList.remove('hidden');
      bText.innerHTML = `Filter Aktif: <strong>[${kode}] ${matchedOut ? matchedOut.output_singkat : ''}</strong>`;
    }

    applyBansosFilters();
  }

  function filterBansosStatus(status, btnElement) {
    activeBansosStatus = status;

    document.querySelectorAll('.bansos-filter-btn').forEach(b => {
      b.className = 'shrink-0 bansos-filter-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-purple-50 hover:text-purple-700 rounded-lg font-semibold transition-all border border-slate-200';
    });
    if (btnElement) {
      btnElement.className = 'shrink-0 bansos-filter-btn px-3 py-1.5 bg-purple-600 text-white rounded-lg font-bold shadow-xs transition-all border border-purple-600';
    }

    const banner = document.getElementById('bansosFilterBanner');
    const bText = document.getElementById('bansosFilterText');
    if (banner && bText) {
      banner.classList.remove('hidden');
      bText.innerHTML = `Filter Status Serapan: <strong>${status === 'optimal' ? '≥ 95% (Optimal)' : status}</strong>`;
    }

    applyBansosFilters();
  }

  function resetBansosFilters() {
    activeBansosOut = 'all';
    activeBansosStatus = 'all';
    const sInput = document.getElementById('bansosSearchInput');
    if (sInput) sInput.value = '';

    document.querySelectorAll('.bansos-filter-btn').forEach(b => {
      b.className = 'shrink-0 bansos-filter-btn px-3 py-1.5 bg-white text-slate-700 hover:bg-purple-50 hover:text-purple-700 rounded-lg font-semibold transition-all border border-slate-200';
    });
    const btnAll = document.getElementById('btnBansosFilterAll');
    if (btnAll) {
      btnAll.className = 'shrink-0 bansos-filter-btn px-3 py-1.5 bg-purple-600 text-white rounded-lg font-bold shadow-xs transition-all border border-purple-600';
    }

    const banner = document.getElementById('bansosFilterBanner');
    if (banner) banner.classList.add('hidden');

    applyBansosFilters();
  }

  function filterBansosTable() {
    applyBansosFilters();
  }

  function applyBansosFilters() {
    const sQuery = (document.getElementById('bansosSearchInput')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#bansosTableBody tr');
    let visibleCount = 0;

    rows.forEach(r => {
      const rowSearch = r.getAttribute('data-search') || '';
      const rowOut = r.getAttribute('data-out-kode') || '';
      const rowPct = parseFloat(r.getAttribute('data-pct') || '0');

      let matchSearch = !sQuery || rowSearch.includes(sQuery);
      let matchOut = (activeBansosOut === 'all') || (rowOut === activeBansosOut);
      let matchStatus = true;
      if (activeBansosStatus === 'optimal') {
        matchStatus = rowPct >= 95;
      }

      if (matchSearch && matchOut && matchStatus) {
        r.classList.remove('hidden');
        visibleCount++;
      } else {
        r.classList.add('hidden');
      }
    });

    const countInfo = document.getElementById('bansosCountInfo');
    if (countInfo) {
      countInfo.innerHTML = `Menampilkan <strong class="text-slate-800">${visibleCount}</strong> dari ${rows.length} rincian paket Bantuan Sosial`;
    }
  }

  // Sorting
  let currentSortCol = '';
  let currentSortAsc = true;

  function sortBansosTable(col) {
    const tbody = document.getElementById('bansosTableBody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    if (currentSortCol === col) {
      currentSortAsc = !currentSortAsc;
    } else {
      currentSortCol = col;
      currentSortAsc = false;
    }

    rows.sort((a, b) => {
      let valA, valB;
      if (col === 'pagu' || col === 'realisasi' || col === 'pct') {
        valA = parseFloat(a.getAttribute(`data-${col}`) || '0');
        valB = parseFloat(b.getAttribute(`data-${col}`) || '0');
      } else {
        valA = (a.getAttribute(`data-${col}`) || '').toLowerCase();
        valB = (b.getAttribute(`data-${col}`) || '').toLowerCase();
      }
      if (valA < valB) return currentSortAsc ? -1 : 1;
      if (valA > valB) return currentSortAsc ? 1 : -1;
      return 0;
    });

    rows.forEach(r => tbody.appendChild(r));

    document.querySelectorAll('[id^="bansosSort-"]').forEach(el => el.textContent = '↕');
    const icon = document.getElementById(`bansosSort-${col}`);
    if (icon) icon.textContent = currentSortAsc ? '↑' : '↓';
  }

  // Modal Inspector
  let activeBansosItem = null;

  function openBansosModal(row) {
    const data = {
      kode_satker: row.getAttribute('data-satker-kode'),
      nama_satker: row.getAttribute('data-satker'),
      kl: row.getAttribute('data-kl'),
      wilayah: row.getAttribute('data-wilayah'),
      kode_out: row.getAttribute('data-out-kode'),
      kegiatan: row.getAttribute('data-kegiatan'),
      output: row.getAttribute('data-output'),
      program: row.getAttribute('data-program'),
      deskripsi: row.getAttribute('data-deskripsi'),
      status: row.getAttribute('data-status'),
      pct: parseFloat(row.getAttribute('data-pct') || '0'),
      pagu_fmt: row.getAttribute('data-pagu-fmt'),
      real_fmt: row.getAttribute('data-real-fmt'),
      sisa_fmt: row.getAttribute('data-sisa-fmt')
    };
    populateAndShowModal(data);
  }

  function openBansosModalByIndex(idx) {
    const item = bansosItemsData[idx];
    if (!item) return;
    const sisa = Math.max(0, item.pagu - item.realisasi);
    const data = {
      kode_satker: item.kode_satker,
      nama_satker: item.nama_satker,
      kl: item.kl,
      wilayah: item.wilayah,
      kode_out: item.kode_out,
      kegiatan: item.kegiatan,
      output: item.output,
      program: item.program,
      deskripsi: item.deskripsi,
      status: item.status,
      pct: item.pct,
      pagu_fmt: bansosHasPagu ? ('Rp ' + item.pagu.toLocaleString('id-ID')) : ((item.penerima || 0).toLocaleString('id-ID') + ' KPM'),
      real_fmt: 'Rp ' + item.realisasi.toLocaleString('id-ID'),
      sisa_fmt: bansosHasPagu ? ('Rp ' + sisa.toLocaleString('id-ID')) : (item.wilayah || '-')
    };
    populateAndShowModal(data);
  }

  function populateAndShowModal(data) {
    activeBansosItem = data;

    document.getElementById('mbKode').textContent = data.kode_satker;
    document.getElementById('mbOutBadge').textContent = `[${data.kode_out}] Output`;
    document.getElementById('mbStatusBadge').textContent = data.status.toUpperCase();
    document.getElementById('mbSatker').textContent = data.nama_satker;
    document.getElementById('mbKlWilayah').textContent = `${data.kl} • ${data.wilayah}`;
    document.getElementById('mbKegiatan').textContent = data.kegiatan;
    document.getElementById('mbOutput').textContent = data.output;
    document.getElementById('mbProgram').textContent = data.program;
    document.getElementById('mbDeskripsi').textContent = data.deskripsi;

    document.getElementById('mbPagu').textContent = data.pagu_fmt;
    document.getElementById('mbReal').textContent = data.real_fmt;
    document.getElementById('mbSisa').textContent = data.sisa_fmt;
    document.getElementById('mbPct').textContent = data.pct.toFixed(2) + '%';

    const bar = document.getElementById('mbBar');
    bar.style.width = Math.min(data.pct, 100) + '%';
    bar.className = (data.pct >= 95 ? 'bg-emerald-600' : 'bg-purple-600') + ' h-2.5 rounded-full transition-all duration-500';

    document.getElementById('bansosDetailModal').classList.remove('hidden');
  }

  function closeBansosModal() {
    document.getElementById('bansosDetailModal').classList.add('hidden');
  }

  function copyBansosInfo() {
    if (!activeBansosItem) return;
    const text = `[SIPANTAS Kanwil DJPb Aceh - Bantuan Sosial]\nSatker: ${activeBansosItem.nama_satker} (${activeBansosItem.kode_satker})\nKegiatan: ${activeBansosItem.kegiatan}\nOutput: ${activeBansosItem.output}\nPagu: ${activeBansosItem.pagu_fmt}\nRealisasi: ${activeBansosItem.real_fmt} (${activeBansosItem.pct.toFixed(2)}%)\nSisa: ${activeBansosItem.sisa_fmt}`;
    navigator.clipboard.writeText(text).then(() => {
      const btn = document.getElementById('btnCopyBansos');
      const orig = btn.textContent;
      btn.textContent = '✓ Disalin!';
      setTimeout(() => btn.textContent = orig, 1800);
    });
  }

  // Close modal on escape key or backdrop click
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeBansosModal();
  });
  document.getElementById('bansosDetailModal')?.addEventListener('click', e => {
    if (e.target.id === 'bansosDetailModal') closeBansosModal();
  });

  // Init chart on DOM load
  document.addEventListener('DOMContentLoaded', () => {
    renderBansosChart();
  });
</script>
@endpush
