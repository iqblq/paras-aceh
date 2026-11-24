@extends('layouts.app')

@section('title', 'Transfer Ke Daerah (TKD) - PARAS Kanwil DJPb Provinsi Aceh')

@section('content')
<div class="space-y-5 sm:space-y-6">

  <!-- 1. HEADER BRANDING BANNER -->
  <div class="bg-white rounded-2xl border border-slate-200/90 p-3.5 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
    <div class="flex items-center gap-3">
      <!-- Logo PARAS -->
      <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white p-1 border border-slate-200/90 flex items-center justify-center shadow-xs shrink-0 overflow-hidden">
        <img src="{{ asset('images/paras.png') }}" alt="Logo PARAS" class="w-full h-full object-contain">
      </div>
      <div>
        <div class="flex items-center gap-1.5 leading-none">
          <span class="text-xs sm:text-sm font-black text-[#1e40af] tracking-tight">Kanwil DJPb Provinsi Aceh</span>
        </div>
        <div class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider mt-1">Indonesian Treasury · Pengawasan Penyaluran Transfer Ke Daerah & BA BUN</div>
      </div>
    </div>

    <div class="text-left sm:text-right border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-100">
      <div class="flex items-center sm:justify-end gap-2">
        <h1 class="text-sm sm:text-lg font-black text-slate-900 tracking-tight">Transfer Ke Daerah (TKD)</h1>
        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-300">BA BUN Penyaluran</span>
      </div>
      <p class="text-[10px] sm:text-[11px] text-slate-500 font-mono mt-0.5">T.A. 2026 · {{ $summary['total_satker'] }} Satker BA BUN · {{ $summary['total_jenis_dbh'] }} Jenis DBH · Otsus Aceh</p>
    </div>
  </div>

  <!-- 2. TOP 3 EXECUTIVE KPI CARDS -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">

    <!-- Card 1: TOTAL ALOKASI TKD (BA BUN) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-blue-600 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
      <div>
        <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1.5">
          TOTAL ALOKASI TKD (BA BUN)
        </div>
        <div class="flex items-baseline gap-1 text-blue-700 font-mono font-black tracking-tight whitespace-nowrap">
          <span class="text-xs sm:text-sm font-bold opacity-75">Rp</span>
          <span class="text-lg sm:text-2xl">{{ trim(str_replace('Rp', '', $summary['total_pagu_formatted'])) }}</span>
        </div>
      </div>
      <div class="text-[11px] text-slate-400 font-medium flex items-center justify-between pt-2.5 mt-2.5 border-t border-slate-100">
        <span>Alokasi BA BUN Penyaluran Aceh</span>
        <span class="font-bold text-blue-600 font-mono">100%</span>
      </div>
    </div>

    <!-- Card 2: TOTAL REALISASI SALUR -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-emerald-500 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
      <div>
        <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1.5">
          TOTAL REALISASI PENYALURAN
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
    <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[6px] border-l-emerald-600 p-4 sm:p-5 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
      <div class="flex items-center justify-between gap-3">
        <div>
          <div class="text-[11px] font-extrabold text-slate-500 tracking-wider uppercase mb-1">
            PERSENTASE SERAPAN
          </div>
          <div class="text-xl sm:text-3xl font-black text-emerald-600 font-mono tracking-tight leading-none my-1">
            {{ number_format($summary['persen_realisasi'], 2, '.', ',') }}%
          </div>
        </div>
        <div class="relative w-14 h-14 sm:w-16 sm:h-16 shrink-0 flex items-center justify-center">
          <svg class="w-full h-full -rotate-90" viewBox="0 0 64 64">
            <circle cx="32" cy="32" r="26" fill="none" stroke="#d1fae5" stroke-width="6"></circle>
            <circle cx="32" cy="32" r="26" fill="none" stroke="#059669" stroke-width="6"
                    stroke-linecap="round"
                    stroke-dasharray="163.36"
                    stroke-dashoffset="{{ round(163.36 * (1 - ($summary['persen_realisasi'] / 100)), 2) }}"></circle>
          </svg>
          <span class="absolute text-[10px] font-mono font-black text-emerald-800">{{ number_format($summary['persen_realisasi'], 1) }}%</span>
        </div>
      </div>
      <div class="text-[11px] text-slate-400 font-medium flex items-center justify-between pt-2 mt-2 border-t border-slate-100">
        <span>Target Nasional Q4: 90%</span>
        <span class="font-bold text-emerald-600">Sangat Tinggi (≥ 90%)</span>
      </div>
    </div>

  </div>

  <!-- 3. RESUME 3 SATKER BA BUN PENYALURAN -->
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2 text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase">
        <span class="text-base">🏛️</span>
        <span>RESUME SATKER PENYALUR TRANSFER KE DAERAH (BA BUN)</span>
      </div>
      <span class="text-[10px] text-slate-400 font-mono">Total Pagu: {{ $summary['total_pagu_short'] }} · Salur: {{ $summary['total_realisasi_short'] }}</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
      @foreach ($satkerBUN as $sb)
        <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] {{ $loop->index === 0 ? 'border-t-blue-600' : ($loop->index === 1 ? 'border-t-amber-500' : 'border-t-emerald-600') }} p-4 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
          <div>
            <div class="flex items-start justify-between gap-2 mb-2">
              <span class="px-2 py-0.5 rounded font-mono text-[10px] font-black {{ $loop->index === 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : ($loop->index === 1 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200') }}">
                Satker {{ $sb['kode'] }}
              </span>
              <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full border {{ $sb['pct'] >= 95 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($sb['pct'] >= 90 ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                {{ $sb['status'] }}
              </span>
            </div>

            <h3 class="text-xs font-black text-slate-800 leading-snug mb-1">
              {{ $sb['nama'] }}
            </h3>
            <p class="text-[10px] text-slate-400 font-medium mb-3">
              {{ $sb['sub'] }}
            </p>

            <div class="space-y-1 text-xs font-mono bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
              <div class="flex items-baseline justify-between gap-2">
                <span class="text-[10px] font-sans font-semibold text-slate-400">Pagu:</span>
                <span class="font-bold text-slate-800 text-[11px] whitespace-nowrap">Rp {{ number_format($sb['pagu'], 0, ',', '.') }}</span>
              </div>
              <div class="flex items-baseline justify-between gap-2">
                <span class="text-[10px] font-sans font-semibold text-slate-400">Realisasi:</span>
                <span class="font-black {{ $sb['pct'] >= 95 ? 'text-emerald-600' : 'text-blue-600' }} text-[11px] whitespace-nowrap">Rp {{ number_format($sb['realisasi'], 0, ',', '.') }}</span>
              </div>
            </div>
          </div>

          <div class="mt-3 pt-2.5 border-t border-slate-200/70">
            <div class="flex items-center justify-between text-xs mb-1.5">
              <span class="text-[10px] text-slate-500 font-semibold">Capaian Salur</span>
              <span class="font-mono font-black text-xs {{ $sb['pct'] >= 95 ? 'text-emerald-600' : 'text-blue-600' }}">{{ number_format($sb['pct'], 2, ',', '.') }}%</span>
            </div>
            <div class="w-full bg-slate-200/80 rounded-full h-1.5 overflow-hidden">
              <div class="{{ $sb['pct'] >= 95 ? 'bg-emerald-500' : ($sb['pct'] >= 90 ? 'bg-blue-600' : 'bg-amber-500') }} h-1.5 rounded-full" style="width: {{ min($sb['pct'], 100) }}%"></div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- 4. RESUME PORSI DBH & OTONOMI KHUSUS (2 CHART DONUT / PIE PERSIS GAMBAR 1) -->
  <div class="space-y-3">
    <div class="flex items-center gap-2 text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase">
      <span class="text-base">📈</span>
      <span>RESUME PORSI DBH & OTONOMI KHUSUS</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

      <!-- Chart 1: Porsi Realisasi DBH (Per Daerah) -->
      <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-blue-600 p-4 sm:p-5 shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <div>
            <h3 class="text-xs sm:text-sm font-extrabold text-slate-800">Porsi Realisasi DBH (Per Daerah)</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Total Realisasi Salur DBH: Rp 402,71 Miliar</p>
          </div>
          <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">6 Daerah</span>
        </div>

        <div class="relative w-full h-[260px] sm:h-[280px] my-3 flex items-center justify-center">
          <canvas id="chartPorsiDbhDaerah"></canvas>
        </div>

        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-center gap-2 text-[11px]">
          <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> ACEH: 72,3%</span>
          <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-red-50 text-red-700 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> KOTA BANDA ACEH: 12,9%</span>
          <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> KAB. ACEH BESAR: 5,6%</span>
          <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> KAB. PIDIE: 4,1%</span>
          <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-purple-50 text-purple-700 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> KAB. PIDIE JAYA: 4,0%</span>
          <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-cyan-50 text-cyan-700 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span> KOTA SABANG: 1,1%</span>
        </div>
      </div>

      <!-- Chart 2: Porsi Pagu Otonomi Khusus (Per Uraian) -->
      <div class="bg-white rounded-2xl border border-slate-200/90 border-t-[4px] border-t-amber-500 p-4 sm:p-5 shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <div>
            <h3 class="text-xs sm:text-sm font-extrabold text-slate-800">Porsi Pagu Otonomi Khusus (Per Uraian)</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Total Alokasi Otsus & Tambahan DBH: Rp 4,14 Triliun</p>
          </div>
          <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">3 Uraian</span>
        </div>

        <div class="relative w-full h-[260px] sm:h-[280px] my-3 flex items-center justify-center">
          <canvas id="chartPorsiOtsusUraian"></canvas>
        </div>

        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-center gap-2 text-[11px]">
          <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-amber-50 text-amber-800 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Dana Otsus Aceh: 98,5%</span>
          <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Tambahan Gas Bumi: 0,8%</span>
          <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-blue-50 text-blue-800 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Tambahan Minyak Bumi: 0,7%</span>
        </div>
      </div>

    </div>
  </div>

  <!-- 5. SECTION RINCIAN DANA BAGI HASIL (DBH) (TABEL 16 BARIS PERSIS GAMBAR 2 & 3) -->
  <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">

    <!-- Header & Toolbar -->
    <div class="p-3.5 sm:p-4 border-b border-slate-200/80 bg-slate-50/70 space-y-3">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <div>
          <h2 class="text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase flex items-center gap-2">
            <span>📊</span>
            <span>RINCIAN DANA BAGI HASIL (DBH)</span>
          </h2>
          <p class="text-[11px] text-slate-400 mt-0.5">Alokasi & Penyaluran 16 Jenis DBH Pajak dan Sumber Daya Alam se-Aceh</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <!-- Filter Daerah Dropdown (Persis Gambar 2) -->
          <div class="flex items-center gap-1.5">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">FILTER DAERAH:</span>
            <select id="dbhDaerahSelect" onchange="filterDbhByDaerah(this.value)" class="bg-white border border-slate-200 text-xs font-bold text-blue-700 rounded-xl px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 cursor-pointer shadow-2xs">
              <option value="total">TOTAL DBH</option>
              <option value="aceh">ACEH (PROVINSI)</option>
              <option value="banda_aceh">KOTA BANDA ACEH</option>
              <option value="aceh_besar">KAB. ACEH BESAR</option>
              <option value="pidie">KAB. PIDIE</option>
              <option value="pidie_jaya">KAB. PIDIE JAYA</option>
              <option value="sabang">KOTA SABANG</option>
            </select>
          </div>

          <!-- Tombol Unduh CSV -->
          <a href="{{ route('tkd.export') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-xs transition-all">
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span>Unduh CSV</span>
          </a>
        </div>
      </div>

      <!-- Quick Search Bar -->
      <div class="w-full sm:w-96 relative">
        <input type="text"
               id="dbhSearchInput"
               onkeyup="filterDbhTable()"
               placeholder="🔍 Cari jenis DBH (PPh, PBB, Cukai, Migas, Sawit)..."
               class="w-full bg-white border border-slate-200 rounded-xl pl-3.5 pr-10 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all font-medium shadow-2xs">
      </div>
    </div>

    <!-- Active Daerah Banner -->
    <div id="dbhDaerahBanner" class="px-4 py-2 bg-blue-50 border-b border-blue-100 flex items-center justify-between text-xs text-blue-800 font-semibold">
      <span id="dbhDaerahBannerText">Menampilkan Data: <strong>TOTAL KESELURUHAN DBH ACEH</strong></span>
      <span class="text-[11px] font-mono text-blue-600">Pagu: Rp 409,25 M · Realisasi: Rp 402,71 M (98,40%)</span>
    </div>

    <!-- DBH Table (Kolom Persis Gambar 2 & 3: URAIAN DANA BAGI HASIL, PAGU, REALISASI, %) -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse" id="dbhDataTable">
        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-extrabold border-b border-slate-200/90 text-[10px]">
          <tr>
            <th class="py-3 px-3 w-12 text-center select-none">NO</th>
            <th class="py-3 px-3 min-w-[280px] select-none">URAIAN DANA BAGI HASIL</th>
            <th onclick="sortDbhTable('pagu')" class="py-3 px-3 text-right whitespace-nowrap cursor-pointer hover:text-blue-600 transition-colors select-none">
              <span>PAGU</span> <span id="dbhSort-pagu" class="text-[9px] text-slate-400">↕</span>
            </th>
            <th onclick="sortDbhTable('realisasi')" class="py-3 px-3 text-right whitespace-nowrap cursor-pointer hover:text-blue-600 transition-colors select-none">
              <span>REALISASI</span> <span id="dbhSort-realisasi" class="text-[9px] text-slate-400">↕</span>
            </th>
            <th onclick="sortDbhTable('pct')" class="py-3 px-3 text-left w-36 whitespace-nowrap cursor-pointer hover:text-blue-600 transition-colors select-none">
              <span>% REALISASI</span> <span id="dbhSort-pct" class="text-[9px] text-slate-400">↕</span>
            </th>
            <th class="py-3 px-2.5 text-center w-16 select-none">DETAIL</th>
          </tr>
        </thead>
        <tbody id="dbhTableBody" class="divide-y divide-slate-100 text-slate-700">
          @foreach ($dbhItems as $idx => $it)
            @php
              $sisa = max(0, $it['pagu'] - $it['realisasi']);
              $barColor = $it['pct'] >= 100 ? 'bg-emerald-500' : ($it['pct'] >= 95 ? 'bg-blue-600' : 'bg-amber-500');
              $pctColor = $it['pct'] >= 100 ? 'text-emerald-700' : ($it['pct'] >= 95 ? 'text-blue-700' : 'text-amber-700');
              $searchStr = strtolower($it['uraian'] . ' ' . $it['kategori']);
            @endphp
            <tr onclick="openDbhModal(this)"
                class="dbh-row hover:bg-blue-50/40 transition-colors cursor-pointer group"
                data-index="{{ $idx }}"
                data-search="{{ $searchStr }}"
                data-pagu="{{ $it['pagu'] }}"
                data-realisasi="{{ $it['realisasi'] }}"
                data-pct="{{ $it['pct'] }}"
                data-uraian="{{ $it['uraian'] }}"
                data-kategori="{{ $it['kategori'] }}"
                data-status="{{ $it['status'] }}"
                data-pagu-fmt="Rp {{ number_format($it['pagu'], 0, ',', '.') }}"
                data-real-fmt="Rp {{ number_format($it['realisasi'], 0, ',', '.') }}"
                data-sisa-fmt="Rp {{ number_format($sisa, 0, ',', '.') }}">
              <td class="py-3 px-3 text-center font-mono text-slate-400 font-semibold">{{ $it['no'] }}</td>
              <td class="py-3 px-3">
                <div class="font-extrabold text-slate-900 group-hover:text-blue-700 transition-colors leading-snug">
                  {{ $it['uraian'] }}
                </div>
                <div class="text-[10px] text-slate-400 font-semibold mt-0.5">
                  Kategori: <span class="text-blue-600 font-mono">{{ $it['kategori'] }}</span>
                </div>
              </td>
              <td class="py-3 px-3 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                Rp {{ number_format($it['pagu'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-3 text-right font-mono font-extrabold text-emerald-600 whitespace-nowrap">
                Rp {{ number_format($it['realisasi'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-3">
                <div class="flex items-center justify-between font-mono text-[11px] font-black {{ $pctColor }} mb-1">
                  <span>{{ number_format($it['pct'], 2, '.', ',') }}%</span>
                  <span class="text-[9px] px-1.5 py-0.2 rounded font-sans font-bold {{ $it['pct'] >= 100 ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">{{ $it['status'] }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                  <div class="{{ $barColor }} h-2 rounded-full transition-all duration-300" style="width: {{ min($it['pct'], 100) }}%"></div>
                </div>
              </td>
              <td class="py-3 px-2.5 text-center">
                <button type="button" class="px-2 py-1 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition-colors">
                  Rincian
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
        <!-- Total Row Persis Gambar 3 -->
        <tfoot class="bg-slate-100/80 border-t-2 border-slate-300 font-mono font-black text-xs text-slate-900">
          <tr>
            <td colspan="2" id="dbhFooterTitle" class="py-3.5 px-3 text-center uppercase tracking-wider font-extrabold text-slate-800">
              TOTAL DANA BAGI HASIL
            </td>
            <td id="dbhFooterPagu" class="py-3.5 px-3 text-right font-bold whitespace-nowrap">
              Rp 409.250.181.000
            </td>
            <td id="dbhFooterReal" class="py-3.5 px-3 text-right font-extrabold text-emerald-700 whitespace-nowrap">
              Rp 402.705.996.900
            </td>
            <td id="dbhFooterPct" class="py-3.5 px-3">
              <div class="flex items-center justify-between text-emerald-700 font-extrabold mb-1">
                <span id="dbhFooterPctText">98,40%</span>
                <span id="dbhFooterBadge" class="text-[9px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 uppercase font-sans">Optimal</span>
              </div>
              <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                <div id="dbhFooterBar" class="bg-emerald-600 h-2 rounded-full" style="width: 98.4%"></div>
              </div>
            </td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Footer Counter -->
    <div class="px-4 py-3 bg-slate-50/70 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] text-slate-500">
      <span id="dbhCountInfo">Menampilkan <strong class="text-slate-800">16</strong> dari 16 jenis Dana Bagi Hasil</span>
      <span class="font-mono text-[10px] text-slate-400">Penyaluran via KPPN Banda Aceh · Kanwil DJPb Provinsi Aceh</span>
    </div>

  </div>

  <!-- 6. SECTION RINCIAN OTONOMI KHUSUS BERDASARKAN DAERAH (PERSIS GAMBAR 2 & 4) -->
  <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">

    <div class="p-3.5 sm:p-4 border-b border-slate-200/80 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
      <div>
        <h2 class="text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase flex items-center gap-2">
          <span>🏛️</span>
          <span>RINCIAN OTONOMI KHUSUS BERDASARKAN DAERAH</span>
        </h2>
        <p class="text-[11px] text-slate-400 mt-0.5">Alokasi Dana Otsus Aceh (DOKA) dan Tambahan DBH Migas Otonomi Khusus</p>
      </div>
      <span class="self-start sm:self-auto px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200">
        Provinsi Aceh · DOKA TA 2026
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-extrabold border-b border-slate-200/90 text-[10px]">
          <tr>
            <th class="py-3 px-3 min-w-[320px]">URAIAN OTONOMI KHUSUS</th>
            <th class="py-3 px-3 text-right whitespace-nowrap">PAGU</th>
            <th class="py-3 px-3 text-right whitespace-nowrap">REALISASI</th>
            <th class="py-3 px-3 text-left w-36 whitespace-nowrap">% REALISASI</th>
            <th class="py-3 px-2.5 text-center w-16">DETAIL</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          @foreach ($otsusItems as $idx => $it)
            @php
              $sisa = max(0, $it['pagu'] - $it['realisasi']);
              $barColor = $it['pct'] >= 95 ? 'bg-emerald-500' : ($it['pct'] >= 90 ? 'bg-blue-600' : 'bg-amber-500');
              $pctColor = $it['pct'] >= 95 ? 'text-emerald-700' : ($it['pct'] >= 90 ? 'text-blue-700' : 'text-amber-700');
            @endphp
            <tr onclick="openOtsusModal({{ $idx }})" class="hover:bg-amber-50/40 transition-colors cursor-pointer group">
              <td class="py-3.5 px-3">
                <div class="font-extrabold text-slate-900 group-hover:text-amber-700 transition-colors leading-snug">
                  {{ $it['uraian'] }}
                </div>
                <div class="text-[10px] text-slate-400 font-semibold mt-0.5">
                  Kode Satker Penyalur: <span class="font-mono text-amber-700 font-bold">{{ $it['kode_satker'] }}</span> · {{ $it['nama_satker'] }}
                </div>
              </td>
              <td class="py-3.5 px-3 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                Rp {{ number_format($it['pagu'], 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-3 text-right font-mono font-extrabold text-emerald-600 whitespace-nowrap">
                Rp {{ number_format($it['realisasi'], 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-3">
                <div class="flex items-center justify-between font-mono text-[11px] font-black {{ $pctColor }} mb-1">
                  <span>{{ number_format($it['pct'], 2, '.', ',') }}%</span>
                  <span class="text-[9px] px-1.5 py-0.2 rounded font-sans font-bold {{ $it['pct'] >= 95 ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">{{ $it['status'] }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                  <div class="{{ $barColor }} h-2 rounded-full transition-all duration-300" style="width: {{ min($it['pct'], 100) }}%"></div>
                </div>
              </td>
              <td class="py-3.5 px-2.5 text-center">
                <button type="button" class="px-2 py-1 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200 transition-colors">
                  Rincian
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
        <!-- Total Otsus Persis Gambar 4 -->
        <tfoot class="bg-amber-50/70 border-t-2 border-amber-200 font-mono font-black text-xs text-slate-900">
          <tr>
            <td class="py-3.5 px-3 font-extrabold text-slate-800 uppercase tracking-wide">
              Total Otonomi Khusus Aceh
            </td>
            <td class="py-3.5 px-3 text-right font-bold whitespace-nowrap">
              Rp 4.136.238.270.000
            </td>
            <td class="py-3.5 px-3 text-right font-extrabold text-emerald-700 whitespace-nowrap">
              Rp 4.084.988.425.350
            </td>
            <td class="py-3.5 px-3">
              <div class="flex items-center justify-between text-emerald-700 font-extrabold mb-1">
                <span>98,76%</span>
                <span class="text-[9px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 uppercase font-sans">Optimal</span>
              </div>
              <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                <div class="bg-emerald-600 h-2 rounded-full" style="width: 98.76%"></div>
              </div>
            </td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>

  </div>

  <!-- 7. SECTION PAGU REALISASI PER SATKER BA BUN (PERSIS GAMBAR 4 BAWAH) -->
  <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">

    <div class="p-3.5 sm:p-4 border-b border-slate-200/80 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
      <div>
        <h2 class="text-xs sm:text-sm font-black text-slate-800 tracking-tight uppercase flex items-center gap-2">
          <span>🏛️</span>
          <span>PAGU REALISASI PER SATKER BA BUN (KPPN BANDA ACEH)</span>
        </h2>
        <p class="text-[11px] text-slate-400 mt-0.5">Satker Penyalur Transfer Umum, Transfer Khusus, dan Dana Desa / Otsus</p>
      </div>
      <span class="self-start sm:self-auto px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">
        3 Satker Penyalur
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-extrabold border-b border-slate-200/90 text-[10px]">
          <tr>
            <th class="py-3 px-3 w-12 text-center select-none">NO</th>
            <th class="py-3 px-3 w-24 select-none">KODE</th>
            <th class="py-3 px-3 min-w-[300px] select-none">NAMA SATKER</th>
            <th class="py-3 px-3 text-right whitespace-nowrap select-none">PAGU</th>
            <th class="py-3 px-3 text-right whitespace-nowrap select-none">REALISASI</th>
            <th class="py-3 px-3 text-left w-36 whitespace-nowrap select-none">% REALISASI</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          @foreach ($satkerBUN as $sb)
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="py-3.5 px-3 text-center font-mono text-slate-400 font-semibold">{{ $sb['no'] }}</td>
              <td class="py-3.5 px-3 font-mono font-black text-slate-900">{{ $sb['kode'] }}</td>
              <td class="py-3.5 px-3">
                <div class="font-extrabold text-slate-900 leading-snug">
                  {{ $sb['nama'] }}
                </div>
                <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wide mt-0.5">
                  {{ $sb['sub'] }}
                </div>
              </td>
              <td class="py-3.5 px-3 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                Rp {{ number_format($sb['pagu'], 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-3 text-right font-mono font-extrabold text-emerald-600 whitespace-nowrap">
                Rp {{ number_format($sb['realisasi'], 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-3">
                <div class="flex items-center justify-between font-mono text-[11px] font-black {{ $sb['pct'] >= 95 ? 'text-emerald-700' : 'text-blue-700' }} mb-1">
                  <span>{{ number_format($sb['pct'], 2, '.', ',') }}%</span>
                  <span class="text-[9px] px-1.5 py-0.2 rounded font-sans font-bold {{ $sb['pct'] >= 95 ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">{{ $sb['status'] }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                  <div class="{{ $sb['pct'] >= 95 ? 'bg-emerald-500' : 'bg-blue-600' }} h-2 rounded-full transition-all duration-300" style="width: {{ min($sb['pct'], 100) }}%"></div>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot class="bg-slate-100/90 border-t-2 border-slate-300 font-mono font-black text-xs text-slate-900">
          <tr>
            <td colspan="3" class="py-3.5 px-3 text-center uppercase tracking-wider font-extrabold text-slate-800">
              TOTAL ALOKASI TKD KPPN BANDA ACEH
            </td>
            <td class="py-3.5 px-3 text-right font-bold whitespace-nowrap">
              Rp 12.931.920.218.000
            </td>
            <td class="py-3.5 px-3 text-right font-extrabold text-emerald-700 whitespace-nowrap">
              Rp 12.237.037.761.537
            </td>
            <td class="py-3.5 px-3">
              <div class="flex items-center justify-between text-emerald-700 font-extrabold mb-1">
                <span>94,63%</span>
                <span class="text-[9px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 uppercase font-sans">Optimal</span>
              </div>
              <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                <div class="bg-emerald-600 h-2 rounded-full" style="width: 94.63%"></div>
              </div>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

  </div>

  <!-- 8. MODAL DETAIL INSPECTOR TKD -->
  <div id="tkdDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="relative w-full max-w-xl max-h-[92vh] flex flex-col bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto">

      <div class="p-4 sm:p-5 bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-950 text-white flex items-start justify-between gap-3 shrink-0">
        <div>
          <div class="flex flex-wrap items-center gap-2 mb-1.5">
            <span id="mtkdBadge" class="px-2 py-0.5 rounded-md font-mono text-xs font-black bg-blue-500/20 text-blue-200 border border-blue-400/30">DBH</span>
            <span id="mtkdStatusBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">Optimal</span>
          </div>
          <h3 id="mtkdTitle" class="text-sm sm:text-base font-black text-white leading-snug">Uraian Komponen Transfer</h3>
          <p id="mtkdSubtitle" class="text-xs text-slate-300 mt-1 font-medium">Klasifikasi & Penyaluran Daerah</p>
        </div>
        <button onclick="closeTkdModal()" class="p-1 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white transition-colors shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div class="p-4 sm:p-5 space-y-4 overflow-y-auto flex-1">
        <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-100 space-y-1.5 text-xs">
          <div>
            <span class="text-[10px] font-bold text-blue-700 uppercase tracking-wider block">Uraian Komponen</span>
            <span id="mtkdUraian" class="font-extrabold text-slate-900 text-xs sm:text-sm"></span>
          </div>
          <div class="pt-1 border-t border-blue-100">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Kategori / Sumber Dana</span>
            <span id="mtkdKategori" class="text-slate-800 font-semibold"></span>
          </div>
          <div class="pt-1 border-t border-blue-100">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Keterangan / Regulasi</span>
            <p id="mtkdKeterangan" class="text-slate-700 leading-relaxed font-normal mt-0.5"></p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
            <div class="text-[10px] font-bold text-slate-500 uppercase">PAGU ALOKASI</div>
            <div id="mtkdPagu" class="text-xs sm:text-sm font-black text-slate-900 font-mono mt-0.5">Rp 0</div>
          </div>
          <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/80">
            <div class="text-[10px] font-bold text-emerald-700 uppercase">REALISASI SALUR</div>
            <div id="mtkdReal" class="text-xs sm:text-sm font-black text-emerald-600 font-mono mt-0.5">Rp 0</div>
          </div>
          <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/80">
            <div class="text-[10px] font-bold text-amber-700 uppercase">SISA ALOKASI</div>
            <div id="mtkdSisa" class="text-xs sm:text-sm font-black text-amber-600 font-mono mt-0.5">Rp 0</div>
          </div>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1.5">
          <div class="flex items-center justify-between text-xs">
            <span class="font-semibold text-slate-600">Persentase Penyaluran:</span>
            <span id="mtkdPct" class="font-mono font-black text-slate-900 text-sm">0%</span>
          </div>
          <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
            <div id="mtkdBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
          </div>
        </div>
      </div>

      <div class="p-3.5 sm:p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
        <span class="text-[10px] text-slate-400 font-mono">Transfer Ke Daerah · Kanwil DJPb Provinsi Aceh</span>
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <button onclick="copyTkdInfo()" id="btnCopyTkd" class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-xs font-bold text-slate-700 transition-colors">
            Salin Info
          </button>
          <button onclick="closeTkdModal()" class="flex-1 sm:flex-none px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-xs font-bold text-white transition-colors">
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
  const dbhItemsData = @json($dbhItems);
  const otsusItemsData = @json($otsusItems);
  const dbhDaerahData = @json($dbhDaerah);
  const otsusPorsiData = @json($otsusPorsi);

  let chartDbhDaerah = null;
  let chartOtsusUraian = null;
  let activeDbhDaerah = 'total';

  function formatRp(val) {
    return 'Rp ' + Math.round(val).toLocaleString('id-ID');
  }

  // 1. Inisialisasi Chart Donut DBH per Daerah
  function initChartDbhDaerah() {
    const ctx = document.getElementById('chartPorsiDbhDaerah');
    if (!ctx || typeof Chart === 'undefined') return;

    const daerahKeys = ['aceh', 'banda_aceh', 'aceh_besar', 'pidie', 'pidie_jaya', 'sabang'];
    const labels = ['ACEH', 'KOTA BANDA ACEH', 'KAB. ACEH BESAR', 'KAB. PIDIE', 'KAB. PIDIE JAYA', 'KOTA SABANG'];
    const dataValues = daerahKeys.map(k => dbhDaerahData[k]?.porsi || 0);
    const bgColors = ['#2563eb', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4'];

    chartDbhDaerah = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: dataValues,
          backgroundColor: bgColors,
          borderWidth: 2,
          borderColor: '#ffffff',
          hoverOffset: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '58%',
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
                const key = daerahKeys[ctx.dataIndex];
                const item = dbhDaerahData[key];
                return ` ${ctx.label}: ${ctx.raw}% (${formatRp(item.realisasi)})`;
              }
            }
          }
        },
        onClick: (evt, elements) => {
          if (elements.length > 0) {
            const idx = elements[0].index;
            const key = daerahKeys[idx];
            const selectEl = document.getElementById('dbhDaerahSelect');
            if (selectEl) {
              selectEl.value = key;
              filterDbhByDaerah(key);
            }
          }
        }
      }
    });
  }

  // 2. Inisialisasi Chart Donut Otsus per Uraian
  function initChartOtsusUraian() {
    const ctx = document.getElementById('chartPorsiOtsusUraian');
    if (!ctx || typeof Chart === 'undefined') return;

    const labels = otsusPorsiData.map(o => o.label_singkat);
    const dataValues = otsusPorsiData.map(o => o.porsi);
    const bgColors = otsusPorsiData.map(o => o.color);

    chartOtsusUraian = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: dataValues,
          backgroundColor: bgColors,
          borderWidth: 2,
          borderColor: '#ffffff',
          hoverOffset: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '58%',
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
                const item = otsusPorsiData[ctx.dataIndex];
                return ` ${item.label}: ${ctx.raw}% (${formatRp(item.pagu)})`;
              }
            }
          }
        }
      }
    });
  }

  // 3. Filter DBH Berdasarkan Daerah
  function filterDbhByDaerah(regionKey) {
    activeDbhDaerah = regionKey;
    const banner = document.getElementById('dbhDaerahBanner');
    const bannerText = document.getElementById('dbhDaerahBannerText');
    const item = dbhDaerahData[regionKey] || dbhDaerahData['total'];

    if (bannerText) {
      bannerText.innerHTML = `Menampilkan Data: <strong>${item.nama.toUpperCase()}</strong> (Porsi: ${item.porsi}%)`;
    }

    // Jika daerah spesifik dipilih, lakukan simulasi proporsi nominal per daerah
    const porsiFactor = item.porsi / 100.0;
    const rows = document.querySelectorAll('#dbhTableBody tr');

    rows.forEach(r => {
      const idx = parseInt(r.getAttribute('data-index') || '0');
      const baseItem = dbhItemsData[idx];
      if (!baseItem) return;

      const calcPagu = Math.round(baseItem.pagu * porsiFactor);
      const calcReal = Math.round(baseItem.realisasi * porsiFactor);
      const calcPct = calcPagu > 0 ? (calcReal / calcPagu) * 100 : 0;

      r.cells[2].textContent = formatRp(calcPagu);
      r.cells[3].textContent = formatRp(calcReal);
      r.cells[4].querySelector('span').textContent = calcPct.toFixed(2) + '%';
      r.cells[4].querySelector('.h-2 > div').style.width = Math.min(calcPct, 100) + '%';
    });

    // Update Baris Footer Total Sinkron dengan Filter
    const baseTotalPagu = 409250181000;
    const baseTotalReal = 402705996900;
    const calcTotalPagu = Math.round(baseTotalPagu * porsiFactor);
    const calcTotalReal = Math.round(baseTotalReal * porsiFactor);
    const calcTotalPct = calcTotalPagu > 0 ? (calcTotalReal / calcTotalPagu) * 100 : 0;

    const footerTitle = document.getElementById('dbhFooterTitle');
    const footerPagu = document.getElementById('dbhFooterPagu');
    const footerReal = document.getElementById('dbhFooterReal');
    const footerPctText = document.getElementById('dbhFooterPctText');
    const footerBar = document.getElementById('dbhFooterBar');

    if (footerTitle) {
      footerTitle.textContent = regionKey === 'total' 
        ? 'TOTAL DANA BAGI HASIL (KONSOLIDASI SE-ACEH)' 
        : `TOTAL DANA BAGI HASIL (${item.nama.toUpperCase()})`;
    }
    if (footerPagu) footerPagu.textContent = formatRp(calcTotalPagu);
    if (footerReal) footerReal.textContent = formatRp(calcTotalReal);
    if (footerPctText) footerPctText.textContent = calcTotalPct.toFixed(2) + '%';
    if (footerBar) footerBar.style.width = Math.min(calcTotalPct, 100) + '%';
  }

  // 4. Live Search Filter Table
  function filterDbhTable() {
    const q = (document.getElementById('dbhSearchInput')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#dbhTableBody tr');
    let visible = 0;

    rows.forEach(r => {
      const searchStr = r.getAttribute('data-search') || '';
      if (!q || searchStr.includes(q)) {
        r.classList.remove('hidden');
        visible++;
      } else {
        r.classList.add('hidden');
      }
    });

    const info = document.getElementById('dbhCountInfo');
    if (info) {
      info.innerHTML = `Menampilkan <strong class="text-slate-800">${visible}</strong> dari ${rows.length} jenis Dana Bagi Hasil`;
    }
  }

  // 5. Sorting Table
  let dbhSortCol = '';
  let dbhSortAsc = true;

  function sortDbhTable(col) {
    const tbody = document.getElementById('dbhTableBody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    if (dbhSortCol === col) {
      dbhSortAsc = !dbhSortAsc;
    } else {
      dbhSortCol = col;
      dbhSortAsc = false;
    }

    rows.sort((a, b) => {
      const valA = parseFloat(a.getAttribute(`data-${col}`) || '0');
      const valB = parseFloat(b.getAttribute(`data-${col}`) || '0');
      return dbhSortAsc ? valA - valB : valB - valA;
    });

    rows.forEach(r => tbody.appendChild(r));

    document.querySelectorAll('[id^="dbhSort-"]').forEach(el => el.textContent = '↕');
    const icon = document.getElementById(`dbhSort-${col}`);
    if (icon) icon.textContent = dbhSortAsc ? '↑' : '↓';
  }

  // 6. Modal Inspector
  let activeTkdModalData = null;

  function openDbhModal(row) {
    const idx = parseInt(row.getAttribute('data-index') || '0');
    const item = dbhItemsData[idx];
    if (!item) return;

    const currentDaerah = dbhDaerahData[activeDbhDaerah] || dbhDaerahData['total'];
    const porsiFactor = currentDaerah.porsi / 100.0;
    const calcPagu = Math.round(item.pagu * porsiFactor);
    const calcReal = Math.round(item.realisasi * porsiFactor);
    const calcSisa = Math.max(0, calcPagu - calcReal);

    const regionInfo = activeDbhDaerah !== 'total' 
      ? ` • Porsi ${currentDaerah.nama} (${currentDaerah.porsi}%): Alokasi ${formatRp(calcPagu)}, Salur ${formatRp(calcReal)}`
      : '';

    activeTkdModalData = {
      badge: 'DANA BAGI HASIL',
      status: item.status,
      title: item.uraian,
      subtitle: `Kategori: ${item.kategori} · Penyaluran Daerah Provinsi Aceh`,
      uraian: item.uraian,
      kategori: `${item.kategori} (Bagian Anggaran BUN Transfer Umum / Satker 500133)`,
      keterangan: 'Penyaluran dana bagi hasil pusat-daerah untuk pendapatan asli daerah sesuai realisasi penerimaan perpajakan dan sumber daya alam.' + regionInfo,
      pagu: formatRp(calcPagu),
      real: formatRp(calcReal),
      sisa: formatRp(calcSisa),
      pct: item.pct
    };

    renderTkdModal();
  }

  function openOtsusModal(idx) {
    const item = otsusItemsData[idx];
    if (!item) return;

    activeTkdModalData = {
      badge: 'OTONOMI KHUSUS ACEH',
      status: item.status,
      title: item.uraian,
      subtitle: `Satker: ${item.kode_satker} · ${item.nama_satker}`,
      uraian: item.uraian,
      kategori: `Dana Otonomi Khusus & Tambahan DBH Migas (UUPA No. 11/2006)`,
      keterangan: item.keterangan,
      pagu: formatRp(item.pagu),
      real: formatRp(item.realisasi),
      sisa: formatRp(item.sisa),
      pct: item.pct
    };

    renderTkdModal();
  }

  function renderTkdModal() {
    if (!activeTkdModalData) return;
    const d = activeTkdModalData;

    document.getElementById('mtkdBadge').textContent = d.badge;
    document.getElementById('mtkdStatusBadge').textContent = d.status.toUpperCase();
    document.getElementById('mtkdTitle').textContent = d.title;
    document.getElementById('mtkdSubtitle').textContent = d.subtitle;
    document.getElementById('mtkdUraian').textContent = d.uraian;
    document.getElementById('mtkdKategori').textContent = d.kategori;
    document.getElementById('mtkdKeterangan').textContent = d.keterangan;

    document.getElementById('mtkdPagu').textContent = d.pagu;
    document.getElementById('mtkdReal').textContent = d.real;
    document.getElementById('mtkdSisa').textContent = d.sisa;
    document.getElementById('mtkdPct').textContent = d.pct.toFixed(2) + '%';

    const bar = document.getElementById('mtkdBar');
    bar.style.width = Math.min(d.pct, 100) + '%';
    bar.className = (d.pct >= 95 ? 'bg-emerald-600' : 'bg-blue-600') + ' h-2.5 rounded-full transition-all duration-500';

    document.getElementById('tkdDetailModal').classList.remove('hidden');
  }

  function closeTkdModal() {
    document.getElementById('tkdDetailModal').classList.add('hidden');
  }

  function copyTkdInfo() {
    if (!activeTkdModalData) return;
    const d = activeTkdModalData;
    const text = `[PARAS Kanwil DJPb Aceh - Transfer Ke Daerah]\nKomponen: ${d.title}\nKategori: ${d.kategori}\nPagu Alokasi: ${d.pagu}\nRealisasi Salur: ${d.real} (${d.pct.toFixed(2)}%)\nSisa Dana: ${d.sisa}`;
    navigator.clipboard.writeText(text).then(() => {
      const btn = document.getElementById('btnCopyTkd');
      const orig = btn.textContent;
      btn.textContent = '✓ Disalin!';
      setTimeout(() => btn.textContent = orig, 1800);
    });
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeTkdModal();
  });
  document.getElementById('tkdDetailModal')?.addEventListener('click', e => {
    if (e.target.id === 'tkdDetailModal') closeTkdModal();
  });

  // DOM Loaded
  document.addEventListener('DOMContentLoaded', () => {
    initChartDbhDaerah();
    initChartOtsusUraian();
  });
</script>
@endpush
