<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'SIPANTAS - Portal Pemantauan Anggaran Kanwil DJPb Provinsi Aceh')</title>
  
  <!-- Favicon SIPANTAS -->
  <link rel="icon" type="image/png" href="{{ asset('images/paras.png') }}?v=2">
  <link rel="apple-touch-icon" href="{{ asset('images/paras.png') }}?v=2">
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- Google Font: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <!-- Chart.js & DataLabels CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
          },
          colors: {
            treasury: {
              navy: '#0b1120',
              blue: '#1e40af',
              amber: '#f59e0b',
              slate: '#0f172a'
            }
          }
        }
      }
    }
  </script>
  <style>
    /* ========================================================
       ACCESSIBILITY & SENIOR-FRIENDLY TYPOGRAPHY ENHANCEMENT
       ======================================================== */
    :root {
      --app-font-scale: 1;
    }
    
    html {
      font-size: calc(15.5px * var(--app-font-scale));
      scroll-behavior: smooth;
    }

    body {
      background-color: #f8fafc;
      color: #0f172a;
      line-height: 1.5;
    }

    /* High contrast color overrides for senior readability */
    .text-slate-400 {
      color: #64748b !important; /* darkened from faint #94a3b8 */
    }
    .text-slate-500 {
      color: #334155 !important; /* darkened from #64748b */
    }
    
    /* Minimum text sizes across all micro-classes (no tiny text) */
    .text-\[9px\] {
      font-size: 0.75rem !important; /* ~11.6px - 13px */
      font-weight: 600;
    }
    .text-\[10px\] {
      font-size: 0.795rem !important; /* ~12.3px - 14px */
      font-weight: 600;
    }
    .text-\[11px\] {
      font-size: 0.845rem !important; /* ~13.1px - 15px */
    }
    
    /* Tabular numbers for clear financial reading */
    .font-mono {
      font-variant-numeric: tabular-nums;
      font-feature-settings: "tnum";
    }

    /* Font Scale Classes (Senior-friendly tuning) */
    html.font-scale-large {
      --app-font-scale: 1.08;
    }
    html.font-scale-xlarge {
      --app-font-scale: 1.16;
    }
    
    /* Better table rows vertical spacing */
    tbody tr td {
      padding-top: 0.75rem !important;
      padding-bottom: 0.75rem !important;
    }

    .no-scrollbar::-webkit-scrollbar {
      display: none;
    }
    .no-scrollbar {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
  </style>
  @stack('styles')
</head>
<body class="h-full antialiased font-sans flex flex-col lg:flex-row overflow-hidden">

  <!-- Mobile Backdrop Overlay for Sidebar Drawer -->
  <div id="sidebarBackdrop" class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-xs hidden transition-opacity lg:hidden" onclick="closeSidebarDrawer()"></div>

  <!-- 1. SIDEBAR NAVIGASI SIPANTAS -->
  @include('layouts.sidebar')

  <!-- 2. MAIN CONTAINER AREA -->
  <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto overflow-x-hidden">
    
    <!-- 2. EXECUTIVE TOP NAVIGATION BAR (PERSIS MYINTRESS MODERN + COMMAND PALETTE + MOBILE DRAWER) -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/90 sticky top-0 z-30 px-3.5 sm:px-6 py-2.5 transition-all shadow-xs">
      <div class="flex items-center justify-between gap-2.5 sm:gap-4 max-w-[1600px] mx-auto">
        
        <!-- Left: Branding & Status (Desktop & Mobile) -->
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
          <!-- Mobile Hamburger Toggle -->
          <button id="sidebarToggle" class="lg:hidden p-1.5 -ml-1 text-slate-700 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors" title="Buka Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </button>

          <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}" class="w-8 h-8 rounded-lg bg-white p-0.5 border border-slate-200/90 flex items-center justify-center shadow-xs shrink-0 overflow-hidden hover:ring-2 hover:ring-blue-500/20 transition-all">
              <img src="{{ asset('images/paras.png') }}" alt="Logo SIPANTAS" class="w-full h-full object-contain">
            </a>
            <div class="hidden sm:block">
              <div class="flex items-center gap-1.5 leading-none">
                <span class="text-xs font-black text-blue-900 tracking-tight">Kanwil DJPb Provinsi Aceh</span>
              </div>
              <div class="flex items-center gap-1.5 mt-0.5">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-[10px] font-semibold text-emerald-700 tracking-wide">Live High-Fidelity Data · Portal SIPANTAS</span>
              </div>
            </div>
            <div class="sm:hidden flex flex-col leading-none">
              <span class="text-xs font-black text-slate-900">SIPANTAS</span>
              <span class="text-[9px] font-bold text-blue-600 mt-0.5">Kanwil DJPb</span>
            </div>
          </div>
        </div>

        <!-- Center: Quick Command Palette Search Bar -->
        <div class="flex-1 min-w-0 max-w-xl mx-1 sm:mx-2">
          <button onclick="openGlobalSearch()" type="button" class="w-full flex items-center justify-between gap-2 px-2.5 sm:px-3.5 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100/90 border border-slate-200/90 text-xs text-slate-400 hover:text-slate-600 transition-all group shadow-2xs overflow-hidden">
            <div class="flex items-center gap-2 min-w-0">
              <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <span class="truncate">Cari Satker, Kode DIPA, atau Akun...</span>
            </div>
            <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 text-[10px] font-mono font-bold bg-white text-slate-500 border border-slate-200 rounded shadow-2xs shrink-0">
              /
            </kbd>
          </button>
        </div>

        <!-- Right: Actions & User Info -->
        <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
          <!-- T.A. 2026 Badge -->
          <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50/80 border border-blue-200/80 text-[11px] font-mono font-bold text-blue-700">
            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>T.A. 2026</span>
          </div>

          <!-- Font Scale Switcher (Ramah Segala Usia / Senior Friendly) -->
          <div class="flex items-center bg-slate-100 p-0.5 rounded-lg border border-slate-200/90 shadow-2xs" title="Sesuaikan Ukuran Tulisan (Ramah Mata)">
            <span class="text-[10px] font-bold text-slate-500 px-1.5 hidden md:inline">Teks:</span>
            <button type="button" onclick="setFontScale('normal')" id="btnFontNormal" class="px-2 py-0.5 rounded text-xs font-bold transition-all bg-white text-blue-700 shadow-2xs" title="Ukuran Teks Standar">
              A
            </button>
            <button type="button" onclick="setFontScale('large')" id="btnFontLarge" class="px-2 py-0.5 rounded text-xs font-bold transition-all text-slate-600 hover:text-slate-900" title="Ukuran Teks Lebih Besar (+12%)">
              A⁺
            </button>
            <button type="button" onclick="setFontScale('xlarge')" id="btnFontXLarge" class="px-2 py-0.5 rounded text-xs font-black transition-all text-slate-600 hover:text-slate-900" title="Ukuran Teks Jumbo (+24%)">
              A⁺⁺
            </button>
          </div>

          <!-- Mode TV / Fullscreen Presentasi -->
          <button onclick="toggleFullScreen()" title="Mode TV Dashboard / Presentasi Layar Penuh" class="hidden md:flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all">
            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
            </svg>
            <span class="text-[11px]">Mode TV</span>
          </button>

          <!-- Cetak Ringkasan Eksekutif -->
          <button onclick="window.print()" title="Cetak Ringkasan Eksekutif" class="hidden sm:inline-flex p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
          </button>

          <!-- User Profile Pill -->
          <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
            <div class="text-right hidden xl:block">
              <div class="text-xs font-extrabold text-slate-800 leading-tight">SYAHRIAL</div>
              <div class="text-[10px] text-slate-400 font-medium">KANWIL DJPb ACEH</div>
            </div>
            <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-amber-500 to-amber-600 text-slate-950 font-black text-xs flex items-center justify-center shadow-2xs">
              S
            </div>
          </div>
        </div>

      </div>
    </header>

    <!-- Main Content Blade Section -->
    <main class="flex-1 p-3.5 sm:p-6 lg:p-7 max-w-[1600px] w-full mx-auto space-y-4 sm:space-y-6">
      @yield('content')
    </main>

    <!-- Institutional Footer Bar -->
    <footer class="mt-auto px-6 py-3 border-t border-slate-200/80 bg-white flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-400">
      <div class="flex items-center gap-2">
        <span class="font-bold text-slate-700">SIPANTAS</span>
        <span>·</span>
        <span>Kantor Wilayah Direktorat Jenderal Perbendaharaan (Kanwil DJPb) Provinsi Aceh</span>
      </div>
      <div>
        <span>Kementerian Keuangan Republik Indonesia &copy; 2026 (Laravel 11 MVC)</span>
      </div>
    </footer>

  </div>

  <!-- Modal Admin PIN -->
  @include('partials.admin_modal')

  <!-- 3. MODAL SPOTLIGHT COMMAND PALETTE SEARCH (MODERN SHORTCUT / ATAU CTRL+K) -->
  <div id="globalSearchModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-start justify-center p-4 sm:pt-20">
    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all animate-in fade-in zoom-in-95 duration-150">
      
      <!-- Input Search -->
      <div class="p-4 border-b border-slate-200 flex items-center gap-3 bg-slate-50/50">
        <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <input type="text" 
               id="globalSearchInput" 
               placeholder="Ketik nama Satker, Kode DIPA, atau kata kunci..." 
               class="w-full bg-transparent text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none"
               autocomplete="off">
        <button onclick="closeGlobalSearch()" class="text-xs px-2 py-1 rounded-md bg-slate-200 hover:bg-slate-300 text-slate-600 font-mono">
          ESC
        </button>
      </div>

      <!-- Quick Nav Suggestions -->
      <div class="max-h-80 overflow-y-auto p-3 space-y-1 text-xs" id="globalSearchResults">
        <div class="px-2 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Akses Cepat Modul</div>
        <a href="{{ route('dashboard') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-blue-50 text-slate-700 hover:text-blue-700 transition-colors group">
          <div class="flex items-center gap-2.5">
            <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold">📊</span>
            <div>
              <div class="font-bold text-slate-900 group-hover:text-blue-700">Dashboard Utama Eksekutif</div>
              <div class="text-[11px] text-slate-400">Pagu Total Rp 40,24 T · Realisasi 71,51% · Grafik 10 K/L</div>
            </div>
          </div>
          <span class="text-[10px] text-slate-400">&rarr;</span>
        </a>
        <a href="{{ route('satker.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-blue-50 text-slate-700 hover:text-blue-700 transition-colors group">
          <div class="flex items-center gap-2.5">
            <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold">📑</span>
            <div>
              <div class="font-bold text-slate-900 group-hover:text-blue-700">Realisasi Anggaran (311 Satker)</div>
              <div class="text-[11px] text-slate-400">10 Rincian Jenis Belanja & Monitoring Per Satker</div>
            </div>
          </div>
          <span class="text-[10px] text-slate-400">&rarr;</span>
        </a>
        <a href="{{ route('tkd.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-blue-50 text-slate-700 hover:text-blue-700 transition-colors group">
          <div class="flex items-center gap-2.5">
            <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">🏛️</span>
            <div>
              <div class="font-bold text-slate-900 group-hover:text-blue-700">Transfer Ke Daerah (TKD Aceh)</div>
              <div class="text-[11px] text-slate-400">Dana Desa, Otsus Aceh, DAU, DAK Fisik/Nonfisik</div>
            </div>
          </div>
          <span class="text-[10px] text-slate-400">&rarr;</span>
        </a>

        <div class="px-2 pt-3 pb-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Satker Populer Kanwil DJPb Provinsi Aceh</div>
        <a href="{{ route('satker.index') }}?search=SABANG" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition-colors">
          <div class="flex items-center gap-2.5">
            <span class="font-mono text-blue-600 font-bold">019700</span>
            <span class="font-semibold text-slate-800">BPS Kota Sabang</span>
          </div>
          <span class="text-[11px] font-bold text-emerald-600">86,29%</span>
        </a>
        <a href="{{ route('satker.index') }}?search=PIDIE" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition-colors">
          <div class="flex items-center gap-2.5">
            <span class="font-mono text-blue-600 font-bold">061820</span>
            <span class="font-semibold text-slate-800">Kantor Pertanahan Kab. Pidie Jaya</span>
          </div>
          <span class="text-[11px] font-bold text-emerald-600">72,07%</span>
        </a>
        <a href="{{ route('satker.index') }}?search=BKKBN" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition-colors">
          <div class="flex items-center gap-2.5">
            <span class="font-mono text-blue-600 font-bold">018377</span>
            <span class="font-semibold text-slate-800">Perwakilan BKKBN Provinsi Aceh</span>
          </div>
          <span class="text-[11px] font-bold text-emerald-600">85,92%</span>
        </a>
      </div>

      <!-- Footer Command Bar -->
      <div class="p-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-[11px] text-slate-400 font-mono">
        <span>Gunakan tombol panah &uarr; &darr; lalu tekan Enter</span>
        <span>Kanwil DJPb Provinsi Aceh</span>
      </div>

    </div>
  </div>

  <!-- Global Scripts -->
  <script>
    // Realtime Clock Update
    setInterval(function() {
      const d = new Date();
      const h = String(d.getHours()).padStart(2, '0');
      const m = String(d.getMinutes()).padStart(2, '0');
      const el = document.getElementById('liveClock');
      if (el) el.textContent = `${h}:${m} WIB`;
    }, 1000);

    // Mobile Sidebar Drawer Toggle & Backdrop
    const toggle = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('sidebarCloseBtn');
    const sidebar = document.getElementById('sidebarMenu');
    const backdrop = document.getElementById('sidebarBackdrop');

    function openSidebarDrawer() {
      if (sidebar) sidebar.classList.remove('-translate-x-full');
      if (backdrop) backdrop.classList.remove('hidden');
    }

    function closeSidebarDrawer() {
      if (sidebar) sidebar.classList.add('-translate-x-full');
      if (backdrop) backdrop.classList.add('hidden');
    }

    if (toggle) {
      toggle.addEventListener('click', () => {
        if (sidebar && sidebar.classList.contains('-translate-x-full')) {
          openSidebarDrawer();
        } else {
          closeSidebarDrawer();
        }
      });
    }

    if (closeBtn) {
      closeBtn.addEventListener('click', closeSidebarDrawer);
    }

    // Auto-close drawer on mobile when link is clicked
    document.querySelectorAll('#sidebarMenu a').forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 1024) closeSidebarDrawer();
      });
    });

    // Fullscreen Mode TV Toggle
    function toggleFullScreen() {
      if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(err => {
          console.log(`Error attempting to enable fullscreen: ${err.message}`);
        });
      } else {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        }
      }
    }

    // Spotlight Command Palette
    function openGlobalSearch() {
      const modal = document.getElementById('globalSearchModal');
      const input = document.getElementById('globalSearchInput');
      if (modal) {
        modal.classList.remove('hidden');
        if (input) setTimeout(() => input.focus(), 50);
      }
    }

    function closeGlobalSearch() {
      const modal = document.getElementById('globalSearchModal');
      if (modal) modal.classList.add('hidden');
    }

    // Keyboard Shortcuts: '/' or 'Ctrl+K'
    document.addEventListener('keydown', function(e) {
      if ((e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') || 
          (e.key === 'k' && (e.ctrlKey || e.metaKey))) {
        e.preventDefault();
        openGlobalSearch();
      }
      if (e.key === 'Escape') {
        closeGlobalSearch();
      }
    });

    // Close on outside click
    document.getElementById('globalSearchModal')?.addEventListener('click', function(e) {
      if (e.target === this) closeGlobalSearch();
    });

    // ========================================================
    // SENIOR-FRIENDLY ACCESSIBILITY & CHART TYPOGRAPHY LOGIC
    // ========================================================
    if (typeof Chart !== 'undefined') {
      Chart.defaults.font.family = '"Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, sans-serif';
      Chart.defaults.font.size = 12.5;
      Chart.defaults.font.weight = '600';
      Chart.defaults.color = '#1e293b';
      
      if (Chart.defaults.plugins) {
        if (Chart.defaults.plugins.tooltip) {
          Chart.defaults.plugins.tooltip.titleFont = { size: 13, weight: 'bold', family: '"Plus Jakarta Sans"' };
          Chart.defaults.plugins.tooltip.bodyFont = { size: 12.5, weight: '500', family: '"Plus Jakarta Sans"' };
          Chart.defaults.plugins.tooltip.padding = 10;
          Chart.defaults.plugins.tooltip.cornerRadius = 8;
        }
        if (Chart.defaults.plugins.legend && Chart.defaults.plugins.legend.labels) {
          Chart.defaults.plugins.legend.labels.font = { size: 12.5, weight: 'bold', family: '"Plus Jakarta Sans"' };
          Chart.defaults.plugins.legend.labels.color = '#0f172a';
          Chart.defaults.plugins.legend.labels.padding = 14;
        }
      }
    }

    function setFontScale(scale) {
      const html = document.documentElement;
      html.classList.remove('font-scale-large', 'font-scale-xlarge');
      
      const btnNorm = document.getElementById('btnFontNormal');
      const btnLg = document.getElementById('btnFontLarge');
      const btnXl = document.getElementById('btnFontXLarge');
      
      [btnNorm, btnLg, btnXl].forEach(b => {
        if (b) {
          b.className = 'px-2 py-0.5 rounded text-xs font-semibold text-slate-600 hover:text-slate-900 transition-all';
        }
      });

      if (scale === 'large') {
        html.classList.add('font-scale-large');
        if (btnLg) btnLg.className = 'px-2 py-0.5 rounded text-xs font-bold bg-white text-blue-700 shadow-2xs transition-all';
      } else if (scale === 'xlarge') {
        html.classList.add('font-scale-xlarge');
        if (btnXl) btnXl.className = 'px-2 py-0.5 rounded text-xs font-black bg-white text-blue-700 shadow-2xs transition-all';
      } else {
        if (btnNorm) btnNorm.className = 'px-2 py-0.5 rounded text-xs font-bold bg-white text-blue-700 shadow-2xs transition-all';
      }

      localStorage.setItem('paras_font_scale', scale);
    }

    // Apply stored font scale immediately
    (function() {
      const saved = localStorage.getItem('paras_font_scale') || 'normal';
      setFontScale(saved);
    })();
  </script>

  @stack('scripts')
</body>
</html>
