<!-- SIDEBAR NAVIGASI SIPANTAS - LARAVEL MVC EDITION -->
<aside id="sidebarMenu" class="fixed inset-y-0 left-0 z-50 w-72 bg-[#0b1120] text-slate-300 flex flex-col justify-between transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static border-r border-slate-800/80 shadow-2xl lg:shadow-none select-none">
  
  <div class="flex flex-col h-full overflow-hidden">
    
    <!-- 1. BRAND HEADER & EMBLEM -->
    <div class="px-4 py-3.5 border-b border-slate-800/70 bg-[#080d19] flex items-center justify-between">
      <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
        <!-- Logo Emblem SIPANTAS -->
        <div class="w-10 h-10 rounded-xl bg-white p-1 flex items-center justify-center shadow-md shadow-blue-500/10 ring-1 ring-slate-700/60 shrink-0 overflow-hidden group-hover:scale-105 transition-transform">
          <img src="{{ asset('images/paras.png') }}" alt="Logo SIPANTAS" class="w-full h-full object-contain">
        </div>
        <div>
          <div class="flex items-center gap-1.5">
            <span class="text-base font-black text-white tracking-tight group-hover:text-amber-400 transition-colors">SIPANTAS</span>
          </div>
          <div class="text-[10px] text-slate-400 font-medium tracking-wide">Kanwil DJPb Provinsi Aceh</div>
        </div>
      </a>
      
      <button id="sidebarCloseBtn" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-md transition-colors" title="Tutup Menu">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- 2. REALTIME MONITORING STATUS BAR -->
    <div class="px-5 py-2.5 bg-slate-900/40 border-b border-slate-800/50 flex items-center justify-between text-[11px] text-slate-400 font-mono">
      <div class="flex items-center gap-1.5">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
        </span>
        <span class="font-sans font-medium text-slate-300">Laravel MVC</span>
      </div>
      <div id="liveClock" class="font-semibold text-slate-300">{{ date('H:i') }} WIB</div>
    </div>

    <!-- 3. NAVIGATION LIST (SCROLLABLE) -->
    <div class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto no-scrollbar">
      
      <div class="px-3 pb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
        Ringkasan & Modul Utama
      </div>

      <!-- Tab 1: Dashboard Utama -->
      <a href="{{ route('dashboard') }}" 
         class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[14px] font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-[#1e40af] text-white shadow-md shadow-blue-950/50' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
        <span>Dashboard Utama</span>
        <span class="text-xs font-mono font-black px-2 py-0.5 rounded-md {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'bg-slate-800/90 text-slate-300 border border-slate-700/70' }} uppercase tracking-wider">KPI</span>
      </a>

      <!-- Tab 2: Realisasi Anggaran 789 Satker -->
      <a href="{{ route('satker.index') }}" 
         class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[14px] font-semibold transition-all {{ request()->routeIs('satker.*') ? 'bg-[#1e40af] text-white shadow-md shadow-blue-950/50' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
        <span>Realisasi Anggaran</span>
        <span class="text-xs font-mono font-black px-2 py-0.5 rounded-md {{ request()->routeIs('satker.*') ? 'bg-white/20 text-white' : 'bg-slate-800/90 text-slate-300 border border-slate-700/70' }}">789</span>
      </a>

      <div class="pt-4 px-3.5 pb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
        Pengawasan Tematik
      </div>

      <!-- Tab 3: Prioritas Presiden -->
      <a href="{{ route('tematik.prioritas') }}" 
         class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[14px] font-semibold transition-all {{ request()->routeIs('tematik.prioritas') ? 'bg-[#1e40af] text-white shadow-md shadow-blue-950/50' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
        <span>Prioritas Presiden</span>
        <span class="text-xs font-mono font-black px-2 py-0.5 rounded-md {{ request()->routeIs('tematik.prioritas') ? 'bg-white/20 text-white' : 'bg-slate-800/90 text-slate-300 border border-slate-700/70' }}">72</span>
      </a>

      <!-- Tab 4: Program Strategis -->
      <a href="{{ route('tematik.strategis') }}" 
         class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[14px] font-semibold transition-all {{ request()->routeIs('tematik.strategis') ? 'bg-[#1e40af] text-white shadow-md shadow-blue-950/50' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
        <span>Program Strategis</span>
        <span class="text-xs font-mono font-black px-2 py-0.5 rounded-md {{ request()->routeIs('tematik.strategis') ? 'bg-white/20 text-white' : 'bg-slate-800/90 text-slate-300 border border-slate-700/70' }}">52</span>
      </a>

      <!-- Tab 5: Penanganan Bencana -->
      <a href="{{ route('tematik.bencana') }}" 
         class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[14px] font-semibold transition-all {{ request()->routeIs('tematik.bencana') ? 'bg-[#1e40af] text-white shadow-md shadow-blue-950/50' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
        <span>Penanganan Bencana</span>
        <span class="text-xs font-mono font-black px-2 py-0.5 rounded-md {{ request()->routeIs('tematik.bencana') ? 'bg-white/20 text-white' : 'bg-slate-800/90 text-slate-300 border border-slate-700/70' }}">22</span>
      </a>

      <!-- Tab 6: Preservasi Jalan -->
      <a href="{{ route('tematik.jalan') }}" 
         class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[14px] font-semibold transition-all {{ request()->routeIs('tematik.jalan') ? 'bg-[#1e40af] text-white shadow-md shadow-blue-950/50' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
        <span>Preservasi Jalan</span>
        <span class="text-xs font-mono font-black px-2 py-0.5 rounded-md {{ request()->routeIs('tematik.jalan') ? 'bg-white/20 text-white' : 'bg-slate-800/90 text-slate-300 border border-slate-700/70' }}">26</span>
      </a>

      <!-- Tab 7: Bantuan Sosial -->
      <a href="{{ route('tematik.bansos') }}" 
         class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[14px] font-semibold transition-all {{ request()->routeIs('tematik.bansos') ? 'bg-[#1e40af] text-white shadow-md shadow-blue-950/50' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
        <span>Bantuan Sosial</span>
        <span class="text-xs font-mono font-black px-2 py-0.5 rounded-md {{ request()->routeIs('tematik.bansos') ? 'bg-white/20 text-white' : 'bg-slate-800/90 text-slate-300 border border-slate-700/70' }}">6</span>
      </a>

      <!-- Tab 8: Transfer Ke Daerah -->
      <a href="{{ route('tkd.index') }}" 
         class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[14px] font-semibold transition-all {{ request()->routeIs('tkd.*') ? 'bg-[#1e40af] text-white shadow-md shadow-blue-950/50' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
        <span>Transfer Ke Daerah</span>
        <span class="text-xs font-mono font-black px-2 py-0.5 rounded-md {{ request()->routeIs('tkd.*') ? 'bg-white/20 text-white' : 'bg-slate-800/90 text-slate-300 border border-slate-700/70' }}">TKD</span>
      </a>

    </div>

    <!-- 4. BOTTOM ACTION & ADMIN LOGIN -->
    <div class="p-4 border-t border-slate-800/80 bg-[#080d19] space-y-2.5">
      <button onclick="openAdminModal()" 
              class="w-full flex items-center justify-center gap-2 py-2.5 px-3 bg-slate-800/80 hover:bg-slate-700/90 text-slate-200 hover:text-white rounded-lg text-xs font-semibold border border-slate-700/80 transition-all shadow-sm">
        <svg class="w-4 h-4 text-amber-400/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
        </svg>
        <span>Akses Administrator</span>
      </button>

      <div class="flex items-center justify-between text-[10px] text-slate-500 font-medium px-1">
        <span>InTress Aceh &copy; 2026</span>
        <span class="font-mono text-slate-400">Laravel v11</span>
      </div>
    </div>

  </div>

</aside>
