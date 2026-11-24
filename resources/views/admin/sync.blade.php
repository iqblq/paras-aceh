@extends('layouts.app')

@section('title', 'Kelola Dataset SINTESA - PARAS Kanwil DJPb Provinsi Aceh')

@section('content')
<div class="space-y-6">

  <!-- 1. HEADER BRANDING BANNER (MIDNIGHT NAVY TREASURY) -->
  <div class="bg-gradient-to-r from-[#0b132b] via-[#1c2541] to-[#0f172a] border border-slate-700/80 rounded-2xl p-4 sm:p-6 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3.5">
      <div class="w-12 h-12 rounded-xl bg-white/10 border border-white/20 p-2 flex items-center justify-center shrink-0 shadow-inner">
        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7M4 7c0-2 1-3 3-3h10c2 0 3 1 3 3m-16 0h16m-10 5h4m-4 4h4"></path>
        </svg>
      </div>
      <div>
        <div class="flex items-center gap-2">
          <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-400/30">BACKOFFICE SINTESA</span>
          <span class="text-xs text-slate-400 font-mono">SQLite Local DB</span>
        </div>
        <h1 class="text-base sm:text-xl font-black text-white tracking-tight mt-0.5">Pusat Sinkronisasi Dataset SINTESA</h1>
        <p class="text-xs text-slate-300">Manajemen berkas CSV resmi SINTESA Kementerian Keuangan lingkup Kanwil DJPb Provinsi Aceh.</p>
      </div>
    </div>

    <!-- Quick Action: One-Click Sync Button -->
    <div class="flex items-center gap-2 sm:self-center">
      <button type="button" 
              onclick="triggerSyncProcess()" 
              id="btnTriggerSync" 
              class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md shadow-blue-900/50 transition-all hover:scale-[1.02] active:scale-[0.98]">
        <svg id="syncIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        <span id="syncText">Sinkronkan Dataset Sekarang</span>
      </button>
    </div>
  </div>

  <!-- Realtime Alert Banner -->
  <div id="syncAlertBox" class="hidden p-4 rounded-xl border text-xs font-semibold"></div>

  <!-- 2. STATUS DATA RIIL SINTESA TERAKHIR -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
    
    <!-- Card 1: Total Pagu DIPA SINTESA -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[5px] border-l-blue-600 p-4 shadow-xs">
      <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">TOTAL PAGU DIPA (SINTESA)</div>
      <div class="text-base sm:text-lg font-black text-slate-900 font-mono tracking-tight">
        {{ $kpi['total_pagu_formatted'] ?? 'Rp 63.886.239.180.000' }}
      </div>
      <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between">
        <span>Inquiry Resmi SINTESA</span>
        <span class="font-bold text-blue-600">18.503 Baris</span>
      </div>
    </div>

    <!-- Card 2: Total Realisasi Belanja -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[5px] border-l-emerald-500 p-4 shadow-xs">
      <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">REALISASI BELANJA</div>
      <div class="text-base sm:text-lg font-black text-emerald-700 font-mono tracking-tight">
        {{ $kpi['total_realisasi_formatted'] ?? 'Rp 23.169.415.443.001' }}
      </div>
      <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between">
        <span>Serapan Regional</span>
        <span class="font-bold text-emerald-600 font-mono">{{ $kpi['persen_realisasi'] ?? '36.27' }}%</span>
      </div>
    </div>

    <!-- Card 3: Pagu Terblokir -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[5px] border-l-rose-500 p-4 shadow-xs">
      <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">PAGU TERBLOKIR</div>
      <div class="text-base sm:text-lg font-black text-rose-700 font-mono tracking-tight">
        {{ $kpi['total_blokir_formatted'] ?? 'Rp 140.200.648.000' }}
      </div>
      <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between">
        <span>Automatic Adjustment</span>
        <span class="font-bold text-rose-600 font-mono">{{ $kpi['persen_blokir'] ?? '0.22' }}%</span>
      </div>
    </div>

    <!-- Card 4: Status Database & Waktu -->
    <div class="bg-white rounded-2xl border border-slate-200/90 border-l-[5px] border-l-amber-500 p-4 shadow-xs">
      <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">SINKRONISASI TERAKHIR</div>
      <div class="text-xs font-bold text-slate-800 font-mono tracking-tight">
        {{ $kpi['cutoff_time'] ?? date('d F Y H:i WIB') }}
      </div>
      <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between">
        <span>Petugas: Syahrial</span>
        <span class="inline-flex items-center gap-1 text-emerald-600 font-bold">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
        </span>
      </div>
    </div>

  </div>

  <!-- 3. FORM UPLOAD DATASET CSV BARU -->
  <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div>
        <h2 class="text-xs sm:text-sm font-black text-slate-900 tracking-tight uppercase flex items-center gap-2">
          <span>📤</span>
          <span>Upload File CSV SINTESA Baru</span>
        </h2>
        <p class="text-xs text-slate-500 mt-0.5">Unggah berkas CSV hasil unduhan dari portal sintesa.kemenkeu.go.id untuk memperbarui data dashboard.</p>
      </div>
    </div>

    <form id="uploadCsvForm" onsubmit="handleCsvUpload(event)" class="grid grid-cols-1 md:grid-cols-3 gap-3.5 items-end">
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Dataset</label>
        <select id="fileCategory" class="w-full text-xs font-medium bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
          <option value="pagu">Inquiry Pagu dan Blokir DIPA (CSV)</option>
          <option value="belanja">Belanja Kewilayahan Bulanan Real1-12 (CSV)</option>
          <option value="tematik">Tematik Prioritas Presiden (CSV)</option>
          <option value="dana_desa">Transfer Daerah: Dana Desa 2026 (CSV)</option>
          <option value="dak_fisik">Transfer Daerah: DAK Fisik (CSV)</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Berkas CSV (.csv)</label>
        <input type="file" id="csvFileInput" accept=".csv" required 
               class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
      </div>

      <div>
        <button type="submit" id="btnUploadSubmit" 
                class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-2">
          <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
          </svg>
          <span>Unggah & Sinkronkan</span>
        </button>
      </div>
    </form>
  </div>

  <!-- 4. TABEL BERKAS DATASET & RIWAYAT SINKRONISASI -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    
    <!-- Left: Berkas Dataset di Folder Server -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-xs flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
          <div class="flex items-center gap-2">
            <span class="text-sm">📁</span>
            <h3 class="text-xs sm:text-sm font-extrabold text-slate-800">Berkas CSV SINTESA di Laptop</h3>
          </div>
          <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600">{{ count($files) }} Berkas</span>
        </div>

        <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto no-scrollbar">
          @forelse ($files as $f)
            <div class="py-2.5 flex items-center justify-between text-xs">
              <div class="flex items-center gap-2 min-w-0 pr-2">
                <span class="text-slate-400">📄</span>
                <span class="font-mono font-semibold text-slate-700 truncate" title="{{ $f['name'] }}">{{ $f['name'] }}</span>
              </div>
              <div class="flex items-center gap-3 shrink-0 text-right">
                <span class="text-[10px] text-slate-400 font-mono">{{ $f['size'] }}</span>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Tersedia</span>
              </div>
            </div>
          @empty
            <div class="text-center py-6 text-xs text-slate-400 font-mono">Belum ada berkas CSV di folder dataset.</div>
          @endforelse
        </div>
      </div>

      <div class="pt-3 mt-3 border-t border-slate-100 text-[11px] text-slate-400 flex items-center justify-between font-mono">
        <span>Lokasi: dataset csv excel/</span>
        <span>Format: CSV UTF-8</span>
      </div>
    </div>

    <!-- Right: Log Sinkronisasi SINTESA -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-xs flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
          <div class="flex items-center gap-2">
            <span class="text-sm">📜</span>
            <h3 class="text-xs sm:text-sm font-extrabold text-slate-800">Riwayat Sinkronisasi (Audit Log)</h3>
          </div>
          <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">Audit Trail</span>
        </div>

        <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto no-scrollbar">
          @forelse ($logs as $log)
            <div class="py-2.5 flex items-center justify-between text-xs">
              <div>
                <div class="flex items-center gap-1.5">
                  <span class="font-mono font-bold text-slate-800 text-[11px]">{{ $log->file_type }}</span>
                  <span class="text-[9px] font-mono px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 font-bold">SUKSES</span>
                </div>
                <div class="text-[10px] text-slate-500 font-mono truncate max-w-xs">{{ $log->filename }} ({{ number_format($log->total_rows) }} baris)</div>
              </div>
              <div class="text-right shrink-0">
                <span class="text-[10px] text-slate-400 font-mono">{{ date('d M H:i', strtotime($log->created_at)) }}</span>
                <div class="text-[9px] text-slate-500">{{ $log->synced_by }}</div>
              </div>
            </div>
          @empty
            <div class="text-center py-6 text-xs text-slate-400 font-mono">Belum ada riwayat sinkronisasi.</div>
          @endforelse
        </div>
      </div>

      <div class="pt-3 mt-3 border-t border-slate-100 text-[11px] text-slate-400 flex items-center justify-between font-mono">
        <span>Status Database: SQLite Normal</span>
        <span class="text-blue-600 font-bold">InTress Aceh</span>
      </div>
    </div>

  </div>

</div>

<script>
  function triggerSyncProcess() {
    const btn = document.getElementById('btnTriggerSync');
    const icon = document.getElementById('syncIcon');
    const text = document.getElementById('syncText');
    const alertBox = document.getElementById('syncAlertBox');

    btn.disabled = true;
    icon.classList.add('animate-spin');
    text.textContent = 'Memproses Sinkronisasi Data...';

    fetch('{{ route('admin.sync.process') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      icon.classList.remove('animate-spin');
      text.textContent = 'Sinkronkan Dataset Sekarang';

      alertBox.className = 'p-4 rounded-xl border text-xs font-semibold bg-emerald-50 text-emerald-800 border-emerald-200 block';
      alertBox.innerHTML = `✓ <strong>Sukses:</strong> ${data.message} Halaman akan diperbarui otomatis dalam 2 detik...`;
      setTimeout(() => location.reload(), 2000);
    })
    .catch(err => {
      btn.disabled = false;
      icon.classList.remove('animate-spin');
      text.textContent = 'Sinkronkan Dataset Sekarang';

      alertBox.className = 'p-4 rounded-xl border text-xs font-semibold bg-rose-50 text-rose-800 border-rose-200 block';
      alertBox.innerHTML = `✕ <strong>Gagal:</strong> Terjadi kesalahan saat memproses data.`;
    });
  }

  function handleCsvUpload(e) {
    e.preventDefault();
    const fileInput = document.getElementById('csvFileInput');
    const category = document.getElementById('fileCategory').value;
    const btn = document.getElementById('btnUploadSubmit');
    const alertBox = document.getElementById('syncAlertBox');

    if (!fileInput.files || fileInput.files.length === 0) {
      alert('Pilih berkas CSV terlebih dahulu!');
      return;
    }

    const formData = new FormData();
    formData.append('csv_file', fileInput.files[0]);
    formData.append('file_category', category);
    formData.append('_token', '{{ csrf_token() }}');

    btn.disabled = true;
    btn.innerHTML = `<span class="animate-spin mr-1">⏳</span> Mengunggah & Memproses...`;

    fetch('{{ route('admin.sync.upload') }}', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      btn.innerHTML = `<span>Unggah & Sinkronkan</span>`;

      if (data.status === 'success') {
        alertBox.className = 'p-4 rounded-xl border text-xs font-semibold bg-emerald-50 text-emerald-800 border-emerald-200 block';
        alertBox.innerHTML = `✓ <strong>Sukses:</strong> ${data.message} Halaman akan diperbarui dalam 2 detik...`;
        setTimeout(() => location.reload(), 2000);
      } else {
        alertBox.className = 'p-4 rounded-xl border text-xs font-semibold bg-rose-50 text-rose-800 border-rose-200 block';
        alertBox.innerHTML = `✕ <strong>Gagal:</strong> ${data.message}`;
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.innerHTML = `<span>Unggah & Sinkronkan</span>`;
      alertBox.className = 'p-4 rounded-xl border text-xs font-semibold bg-rose-50 text-rose-800 border-rose-200 block';
      alertBox.innerHTML = `✕ <strong>Error:</strong> Gagal mengunggah berkas.`;
    });
  }
</script>
@endsection
