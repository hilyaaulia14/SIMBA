@extends('layouts.app')

@section('title', 'Bukti Kehadiran - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row">
        <div>
            <h3 class="page-title-main">Bukti Kehadiran</h3>
            <p class="page-subtitle-main">Dokumentasi absensi fisik tatap muka, foto wajib lapor, dan evaluasi kepribadian klien</p>
        </div>
        <div>
            <button class="btn-simba-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKehadiran">
                <i class="bi bi-camera-fill"></i>
                <span>Catat Kehadiran</span>
            </button>
        </div>
    </div>

    <!-- Standalone Filter Bar (Di Luar Card Tabel Sesuai Figma) -->
    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
        <form action="{{ route('pk.kehadiran.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
            <div style="max-width: 340px; width: 100%;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 50px 0 0 50px; border-color: var(--simba-border); padding-left: 1.15rem;">
                        <img src="{{ asset('assets/images/cari.svg') }}" alt="Cari" style="width: 15px; height: 15px; object-fit: contain;">
                    </span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0 filter-pill-input" placeholder="Telusuri..." value="{{ request('search') }}" style="border-radius: 0 50px 50px 0 !important; padding-left: 0.35rem;">
                </div>
            </div>

            <div style="width: 180px;">
                <input type="date" name="tgl_hadir" class="form-control form-control-sm filter-pill-input" value="{{ request('tgl_hadir') }}" onchange="this.form.submit()" title="Filter berdasarkan tanggal">
            </div>

            @if(request()->hasAny(['search', 'tgl_hadir', 'tanggal']))
                <a href="{{ route('pk.kehadiran.index') }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 50px; font-weight: 500;">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="table-card-figma">
        <div class="table-responsive">
            <table class="table-simba">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;" class="text-center">No</th>
                        <th style="width: 10%; text-align: center;" class="text-center">Tanggal</th>
                        <th style="width: 8%; text-align: center;" class="text-center">Jam</th>
                        <th style="width: 14%;">No. Register</th>
                        <th style="width: 16%;">Nama Klien</th>
                        <th style="width: 13%; text-align: center;" class="text-center">Status Kehadiran</th>
                        <th style="width: 9%; text-align: center;" class="text-center">Foto Bukti</th>
                        <th style="width: 15%;">Evaluasi Perkembangan</th>
                        <th style="width: 10%; text-align: center;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kehadiranList as $index => $kh)
                        @php
                            $stName = strtolower($kh->status_kehadiran ?? ($kh->jadwalBimbingan->statusBimbingan->nama_status ?? 'Hadir'));
                            $statusClass = match(true) {
                                str_contains($stName, 'hadir') && !str_contains($stName, 'tidak') => 'status-pill-hadir',
                                str_contains($stName, 'ulang') => 'status-pill-dijadwalkan-ulang',
                                str_contains($stName, 'tidak') || str_contains($stName, 'absen') => 'status-pill-tidak-hadir',
                                default => 'status-pill-terjadwal'
                            };
                            $displayStatus = match(true) {
                                str_contains($stName, 'hadir') && !str_contains($stName, 'tidak') => 'Hadir',
                                str_contains($stName, 'ulang') => 'Dijadwalkan Ulang',
                                str_contains($stName, 'tidak') || str_contains($stName, 'absen') => 'Tidak Hadir',
                                default => 'Terjadwal'
                            };
                        @endphp
                        <tr>
                            <td class="text-center text-muted" style="text-align: center;">{{ $kehadiranList->firstItem() + $index }}</td>
                            <td class="text-center" style="text-align: center;">{{ $kh->tgl_hadir->format('d/m/Y') }}</td>
                            <td class="text-center" style="text-align: center;">{{ substr($kh->jam_hadir, 0, 5) }}</td>
                            <td>{{ $kh->klien->no_register ?? '-' }}</td>
                            <td style="font-weight: 500;">{{ $kh->klien->nama_klien ?? 'Klien Dihapus' }}</td>
                            <td class="text-center" style="text-align: center;">
                                <span class="{{ $statusClass }}">
                                    ● {{ $displayStatus }}
                                </span>
                            </td>
                            <td class="text-center" style="text-align: center;">
                                @if($kh->foto_url)
                                    @if(str_ends_with(strtolower($kh->foto_kehadiran ?? ''), '.pdf'))
                                        <a href="{{ $kh->foto_url }}" target="_blank" class="badge text-decoration-none" style="background-color: #FEE2E2; color: #DC2626; font-size: 0.75rem; border-radius: 6px; padding: 0.35rem 0.6rem; font-weight: 500;" title="Buka Dokumen PDF">
                                            <i class="bi bi-file-earmark-pdf-fill me-1"></i>PDF
                                        </a>
                                    @else
                                        <img src="{{ $kh->foto_url }}" alt="Bukti Hadir" class="rounded shadow-sm" style="width: 40px; height: 40px; object-fit: cover; cursor: pointer; border: 2px solid #E2E8F0;" onclick="previewFoto('{{ $kh->foto_url }}', '{{ addslashes($kh->klien->nama_klien ?? '') }}', '{{ $kh->tgl_hadir->format('d/m/Y') }}')" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'badge bg-light text-muted border\'>Tanpa Foto</span>';">
                                    @endif
                                @else
                                    <span class="badge" style="background-color: #F1F5F9; color: #64748B; font-size: 0.75rem; border-radius: 6px; padding: 0.35rem 0.6rem; font-weight: 400;">Tanpa Foto</span>
                                @endif
                            </td>
                            <td style="white-space: normal; line-height: 1.5;">
                                {{ $kh->catatan ?: '-' }}
                            </td>
                            <td class="text-center" style="text-align: center;">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <button class="btn-action-square" title="Detail Kehadiran" onclick="showDetailKehadiran({{ $kh->id_kehadiran }})">
                                        <img src="{{ asset('assets/images/detail.svg') }}" alt="Detail">
                                    </button>
                                    <button class="btn-action-square" title="Edit Presensi" onclick="showEditKehadiran({{ $kh->id_kehadiran }})">
                                        <img src="{{ asset('assets/images/edit.svg') }}" alt="Edit">
                                    </button>
                                    <button class="btn-action-square btn-delete" title="Hapus Presensi" onclick="confirmDeleteKehadiran({{ $kh->id_kehadiran }}, '{{ addslashes($kh->klien->nama_klien ?? '') }}', '{{ $kh->tgl_hadir->format('d/m/Y') }}')">
                                        <img src="{{ asset('assets/images/hapus.svg') }}" alt="Hapus">
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                Tidak ada data bukti kehadiran yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kehadiranList->hasPages() || $kehadiranList->total() > 0)
            <div class="p-3 px-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-2" style="font-size: 0.84rem; color: #64748B;">
                <span>
                    @if($kehadiranList->total() > 0 && $kehadiranList->total() <= $kehadiranList->perPage())
                        Menampilkan {{ $kehadiranList->total() }} dari {{ $kehadiranList->total() }} presensi
                    @else
                        Menampilkan {{ $kehadiranList->firstItem() ?? 0 }} sampai {{ $kehadiranList->lastItem() ?? 0 }} dari {{ $kehadiranList->total() }} presensi
                    @endif
                </span>
                <div>
                    {{ $kehadiranList->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Modal Catatan Kehadiran (Disinkronkan dengan modal aksi catatan kehadiran di jadwal) -->
<div class="modal fade" id="modalTambahKehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form action="{{ route('pk.kehadiran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header-figma">
                    <div>
                        <h4 class="modal-title-figma">Catatan Kehadiran</h4>
                        <p class="modal-subtitle-figma">Dokumentasikan kehadiran fisik dan bukti foto wajib lapor klien.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label-figma">Nama Klien <span class="text-danger">*</span></label>
                            <select name="id_klien" id="tambah_kehadiran_id_klien" class="form-select form-control-figma" required>
                                <option value="">Pilih Klien Binaan</option>
                                @foreach($allKlien as $k)
                                    <option value="{{ $k->id_klien }}">{{ $k->nama_klien }} ({{ $k->no_register }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Status Kehadiran <span class="text-danger">*</span></label>
                            <select name="status_kehadiran" class="form-select form-control-figma" required>
                                <option value="Hadir" selected>Hadir</option>
                                <option value="Tidak Hadir">Tidak Hadir</option>
                                <option value="Dijadwalkan Ulang">Dijadwalkan Ulang</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label-figma mb-0">Bukti Dokumentasi</label>
                                <div class="btn-group btn-group-sm p-1 rounded-pill" style="background-color: #F1F5F9; border: 1px solid #E2E8F0;">
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-medium" id="btn_mode_upload_tambah" onclick="setKehadiranMediaMode('tambah', 'upload')" style="background-color: #102A45; color: #FFFFFF; font-size: 0.78rem;">
                                        <i class="bi bi-file-earmark-arrow-up me-1"></i> Unggah Berkas
                                    </button>
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-medium" id="btn_mode_camera_tambah" onclick="setKehadiranMediaMode('tambah', 'camera')" style="background-color: transparent; color: #64748B; font-size: 0.78rem;">
                                        <i class="bi bi-camera-video me-1"></i> Kamera Real-Time
                                    </button>
                                </div>
                            </div>

                            <!-- Opsi 1: Unggah Berkas -->
                            <div id="container_upload_tambah">
                                <div class="upload-dropzone-figma" onclick="document.getElementById('upload_presensi_file_kehadiran').click()">
                                    <img src="{{ asset('assets/images/unggah.svg') }}" alt="Unggah" style="width: 44px; height: 44px; object-fit: contain; margin-bottom: 0.5rem;">
                                    <span class="small text-muted d-block" id="presensi_filename_kehadiran">Klik untuk mengunggah bukti (JPG, PNG, WEBP, atau PDF)</span>
                                    <input type="file" name="foto_kehadiran" id="upload_presensi_file_kehadiran" class="d-none" accept="image/*,.pdf" onchange="updateFilename(this, 'presensi_filename_kehadiran')">
                                </div>
                                <div class="text-center mt-2">
                                    <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" onclick="setKehadiranMediaMode('tambah', 'camera')" style="color: var(--simba-navy); font-size: 0.82rem; font-weight: 500;">
                                        <i class="bi bi-camera-fill me-1"></i> Atau gunakan Kamera Real-Time langsung
                                    </button>
                                </div>
                            </div>

                            <!-- Opsi 2: Kamera Real-Time -->
                            <div id="container_camera_tambah" class="d-none">
                                <div class="p-3 rounded-3 text-center" style="background: #0B132B; border: 1.5px solid #1C2541;">
                                    <!-- Live Video View -->
                                    <div id="camera_feed_wrap_tambah" class="position-relative mx-auto rounded-3 overflow-hidden bg-black" style="max-width: 100%; width: 440px; height: 260px; display: flex; align-items: center; justify-content: center;">
                                        <video id="camera_video_tambah" autoplay playsinline class="w-100 h-100" style="object-fit: cover;"></video>
                                        <div class="position-absolute top-0 start-0 m-2 px-2 py-1 rounded text-white d-flex align-items-center gap-1 shadow-sm" style="background: rgba(220, 38, 38, 0.88); font-size: 0.72rem; font-weight: 600; letter-spacing: 0.5px;">
                                            <span class="spinner-grow spinner-grow-sm" style="width: 8px; height: 8px;" role="status"></span>
                                            <span>LIVE KAMERA</span>
                                        </div>
                                        <div class="position-absolute" style="width: 160px; height: 200px; border: 2px dashed rgba(255,255,255,0.45); border-radius: 24px; pointer-events: none;"></div>
                                    </div>

                                    <!-- Preview Hasil Jepretan -->
                                    <div id="camera_preview_wrap_tambah" class="d-none position-relative mx-auto rounded-3 overflow-hidden" style="max-width: 100%; width: 440px; height: 260px; background: #000;">
                                        <img id="camera_captured_img_tambah" class="w-100 h-100" style="object-fit: contain;" alt="Hasil Kamera">
                                        <div class="position-absolute top-0 start-0 m-2 px-2 py-1 rounded text-white d-flex align-items-center gap-1 shadow-sm" style="background: rgba(16, 185, 129, 0.95); font-size: 0.72rem; font-weight: 600;">
                                            <i class="bi bi-check-circle-fill"></i> Foto Siap Digunakan
                                        </div>
                                    </div>

                                    <canvas id="camera_canvas_tambah" class="d-none"></canvas>
                                    <input type="hidden" name="foto_kehadiran_base64" id="foto_kehadiran_base64_tambah">

                                    <!-- Controls -->
                                    <div class="d-flex justify-content-center align-items-center gap-2 mt-3 flex-wrap">
                                        <div id="camera_live_controls_tambah" class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-light d-inline-flex align-items-center gap-1 px-3 py-2 fw-medium shadow-sm" onclick="switchCameraFacing('tambah')" style="border-radius: 8px; font-size: 0.82rem;">
                                                <i class="bi bi-arrow-repeat"></i> Ganti Kamera
                                            </button>
                                            <button type="button" class="btn btn-sm text-white d-inline-flex align-items-center gap-2 px-4 py-2 fw-semibold shadow-sm" onclick="captureRealtimeSnapshot('tambah', 'upload_presensi_file_kehadiran')" style="background-color: #2563EB; border-color: #2563EB; border-radius: 8px; font-size: 0.85rem;">
                                                <i class="bi bi-camera-fill"></i> Ambil Foto
                                            </button>
                                        </div>

                                        <div id="camera_preview_controls_tambah" class="d-none d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-1 px-3 py-2 fw-medium" onclick="retakeRealtimeSnapshot('tambah')" style="border-radius: 8px; font-size: 0.82rem;">
                                                <i class="bi bi-arrow-counterclockwise"></i> Ambil Ulang Foto
                                            </button>
                                            <span class="badge d-inline-flex align-items-center px-3 py-2 text-white" style="background-color: #059669; font-size: 0.8rem; border-radius: 8px;">
                                                <i class="bi bi-check2-circle me-1 fs-6"></i> Foto Disematkan
                                            </span>
                                        </div>
                                    </div>

                                    <div id="camera_error_tambah" class="alert alert-danger d-none mt-2 mb-0 py-2 small text-start" role="alert"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Keterangan</label>
                            <input type="text" name="catatan" class="form-control form-control-figma" placeholder="Keterangan (Opsional)">
                        </div>

                        <input type="hidden" name="tgl_hadir" value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="jam_hadir" value="{{ date('H:i') }}">
                    </div>
                </div>
                <div class="modal-footer-figma">
                    <button type="button" class="btn btn-figma-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-figma-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Bukti Kehadiran -->
<div class="modal fade" id="modalDetailKehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <div class="modal-header-figma">
                <div>
                    <h4 class="modal-title-figma">Detail Catatan Kehadiran</h4>
                    <p class="modal-subtitle-figma" id="detail_kehadiran_subtitle">Rincian bukti kehadiran klien.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailKehadiranContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer-figma">
                <button type="button" class="btn btn-figma-cancel px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Foto Zoom -->
<div class="modal fade" id="modalPreviewFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-figma text-center p-3">
            <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                <h6 class="fw-bold mb-0 text-dark" id="previewFotoTitle" style="font-size: 0.9rem;">Foto Bukti Kehadiran</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="p-2">
                <img id="previewFotoImg" src="" alt="Bukti Kehadiran" class="img-fluid rounded-3 shadow-sm" style="max-height: 480px; width: auto; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Presensi Kehadiran -->
<div class="modal fade" id="modalEditKehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form id="formEditKehadiran" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header-figma">
                    <div>
                        <h4 class="modal-title-figma">Edit Catatan Kehadiran</h4>
                        <p class="modal-subtitle-figma">Perbarui data catatan dan bukti kehadiran klien.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label-figma">Nama Klien <span class="text-danger">*</span></label>
                            <select name="id_klien" id="edit_kehadiran_id_klien" class="form-select form-control-figma" required>
                                @foreach($allKlien as $k)
                                    <option value="{{ $k->id_klien }}">{{ $k->nama_klien }} ({{ $k->no_register }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tanggal Hadir <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_hadir" id="edit_kehadiran_tgl_hadir" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jam Hadir <span class="text-danger">*</span></label>
                            <input type="time" name="jam_hadir" id="edit_kehadiran_jam_hadir" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Status Kehadiran <span class="text-danger">*</span></label>
                            <select name="status_kehadiran" id="edit_kehadiran_status" class="form-select form-control-figma" required>
                                <option value="Hadir">Hadir</option>
                                <option value="Tidak Hadir">Tidak Hadir</option>
                                <option value="Dijadwalkan Ulang">Dijadwalkan Ulang</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label-figma mb-0">Bukti Dokumentasi</label>
                                <div class="btn-group btn-group-sm p-1 rounded-pill" style="background-color: #F1F5F9; border: 1px solid #E2E8F0;">
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-medium" id="btn_mode_upload_edit" onclick="setKehadiranMediaMode('edit', 'upload')" style="background-color: #102A45; color: #FFFFFF; font-size: 0.78rem;">
                                        <i class="bi bi-file-earmark-arrow-up me-1"></i> Unggah Berkas
                                    </button>
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-medium" id="btn_mode_camera_edit" onclick="setKehadiranMediaMode('edit', 'camera')" style="background-color: transparent; color: #64748B; font-size: 0.78rem;">
                                        <i class="bi bi-camera-video me-1"></i> Kamera Real-Time
                                    </button>
                                </div>
                            </div>

                            <!-- Opsi 1: Unggah Berkas -->
                            <div id="container_upload_edit">
                                <div class="upload-dropzone-figma" onclick="document.getElementById('edit_upload_presensi_file').click()">
                                    <img src="{{ asset('assets/images/unggah.svg') }}" alt="Unggah" style="width: 44px; height: 44px; object-fit: contain; margin-bottom: 0.5rem;">
                                    <span class="small text-muted d-block" id="edit_presensi_filename">Klik untuk mengganti foto dokumentasi (Opsional)</span>
                                    <input type="file" name="foto_kehadiran" id="edit_upload_presensi_file" class="d-none" accept="image/*,.pdf" onchange="updateFilename(this, 'edit_presensi_filename')">
                                </div>
                                <div class="text-center mt-2">
                                    <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" onclick="setKehadiranMediaMode('edit', 'camera')" style="color: var(--simba-navy); font-size: 0.82rem; font-weight: 500;">
                                        <i class="bi bi-camera-fill me-1"></i> Atau gunakan Kamera Real-Time langsung
                                    </button>
                                </div>
                            </div>

                            <!-- Opsi 2: Kamera Real-Time -->
                            <div id="container_camera_edit" class="d-none">
                                <div class="p-3 rounded-3 text-center" style="background: #0B132B; border: 1.5px solid #1C2541;">
                                    <!-- Live Video View -->
                                    <div id="camera_feed_wrap_edit" class="position-relative mx-auto rounded-3 overflow-hidden bg-black" style="max-width: 100%; width: 440px; height: 260px; display: flex; align-items: center; justify-content: center;">
                                        <video id="camera_video_edit" autoplay playsinline class="w-100 h-100" style="object-fit: cover;"></video>
                                        <div class="position-absolute top-0 start-0 m-2 px-2 py-1 rounded text-white d-flex align-items-center gap-1 shadow-sm" style="background: rgba(220, 38, 38, 0.88); font-size: 0.72rem; font-weight: 600; letter-spacing: 0.5px;">
                                            <span class="spinner-grow spinner-grow-sm" style="width: 8px; height: 8px;" role="status"></span>
                                            <span>LIVE KAMERA</span>
                                        </div>
                                        <div class="position-absolute" style="width: 160px; height: 200px; border: 2px dashed rgba(255,255,255,0.45); border-radius: 24px; pointer-events: none;"></div>
                                    </div>

                                    <!-- Preview Hasil Jepretan -->
                                    <div id="camera_preview_wrap_edit" class="d-none position-relative mx-auto rounded-3 overflow-hidden" style="max-width: 100%; width: 440px; height: 260px; background: #000;">
                                        <img id="camera_captured_img_edit" class="w-100 h-100" style="object-fit: contain;" alt="Hasil Kamera">
                                        <div class="position-absolute top-0 start-0 m-2 px-2 py-1 rounded text-white d-flex align-items-center gap-1 shadow-sm" style="background: rgba(16, 185, 129, 0.95); font-size: 0.72rem; font-weight: 600;">
                                            <i class="bi bi-check-circle-fill"></i> Foto Siap Digunakan
                                        </div>
                                    </div>

                                    <canvas id="camera_canvas_edit" class="d-none"></canvas>
                                    <input type="hidden" name="foto_kehadiran_base64" id="foto_kehadiran_base64_edit">

                                    <!-- Controls -->
                                    <div class="d-flex justify-content-center align-items-center gap-2 mt-3 flex-wrap">
                                        <div id="camera_live_controls_edit" class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-light d-inline-flex align-items-center gap-1 px-3 py-2 fw-medium shadow-sm" onclick="switchCameraFacing('edit')" style="border-radius: 8px; font-size: 0.82rem;">
                                                <i class="bi bi-arrow-repeat"></i> Ganti Kamera
                                            </button>
                                            <button type="button" class="btn btn-sm text-white d-inline-flex align-items-center gap-2 px-4 py-2 fw-semibold shadow-sm" onclick="captureRealtimeSnapshot('edit', 'edit_upload_presensi_file')" style="background-color: #2563EB; border-color: #2563EB; border-radius: 8px; font-size: 0.85rem;">
                                                <i class="bi bi-camera-fill"></i> Ambil Foto
                                            </button>
                                        </div>

                                        <div id="camera_preview_controls_edit" class="d-none d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-1 px-3 py-2 fw-medium" onclick="retakeRealtimeSnapshot('edit')" style="border-radius: 8px; font-size: 0.82rem;">
                                                <i class="bi bi-arrow-counterclockwise"></i> Ambil Ulang Foto
                                            </button>
                                            <span class="badge d-inline-flex align-items-center px-3 py-2 text-white" style="background-color: #059669; font-size: 0.8rem; border-radius: 8px;">
                                                <i class="bi bi-check2-circle me-1 fs-6"></i> Foto Disematkan
                                            </span>
                                        </div>
                                    </div>

                                    <div id="camera_error_edit" class="alert alert-danger d-none mt-2 mb-0 py-2 small text-start" role="alert"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Keterangan</label>
                            <input type="text" name="catatan" id="edit_kehadiran_catatan" class="form-control form-control-figma" placeholder="Keterangan (Opsional)">
                        </div>

                        <input type="hidden" name="id_jadwal_bimbingan" id="edit_kehadiran_id_jadwal">
                    </div>
                </div>
                <div class="modal-footer-figma">
                    <button type="button" class="btn btn-figma-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-figma-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Kehadiran -->
<div class="modal fade" id="modalDeleteKehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content modal-content-figma text-center p-4">
            <div class="modal-body p-2">
                <div class="delete-dialog-badge mx-auto">
                    <img src="{{ asset('assets/images/warning.svg') }}" alt="Peringatan">
                </div>
                <h4 class="fw-bold mb-2" style="color: #102A45; font-size: 1.5rem;">Hapus Presensi?</h4>
                <p class="text-dark small mb-4" style="line-height: 1.5;">
                    Catatan kehadiran klien <strong id="deleteKehadiranKlien"></strong> beserta jadwal bimbingan terkait akan dihapus permanen dari sistem.<br>Tindakan ini tidak dapat dibatalkan.
                </p>
                <form id="formDeleteKehadiran" method="POST" class="d-flex justify-content-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-figma-cancel px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4" style="background-color: #EF4444; border-color: #EF4444; border-radius: 8px; font-weight: 500;">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateFilename(input, targetId) {
        if (input.files && input.files[0]) {
            document.getElementById(targetId).innerText = input.files[0].name;
        }
    }

    function previewFoto(url, nama, tgl) {
        document.getElementById('previewFotoImg').src = url;
        document.getElementById('previewFotoTitle').innerText = `Foto Presensi: ${nama} (${tgl})`;
        const modal = new bootstrap.Modal(document.getElementById('modalPreviewFoto'));
        modal.show();
    }

    function showDetailKehadiran(id) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetailKehadiran'));
        modal.show();

        fetch(`/pk/kehadiran/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const kh = res.kehadiran;
            const klien = res.klien;
            const fotoUrl = res.foto_url || (kh.foto_kehadiran ? '/storage/' + kh.foto_kehadiran : null);
            const statusNama = res.status_kehadiran || kh.status_kehadiran || (res.jadwal && res.jadwal.status_bimbingan ? res.jadwal.status_bimbingan.nama_status : 'Hadir');

            let statusClass = 'status-pill-hadir';
            const sLower = statusNama.toLowerCase();
            if (sLower.includes('tidak') || sLower.includes('absen')) statusClass = 'status-pill-tidak-hadir';
            else if (sLower.includes('ulang')) statusClass = 'status-pill-dijadwalkan-ulang';
            else if (sLower.includes('terjadwal')) statusClass = 'status-pill-terjadwal';

            let fotoSection = '';
            if (fotoUrl) {
                const isPdf = fotoUrl.toLowerCase().endsWith('.pdf');
                fotoSection = isPdf
                    ? `<div class="p-3 rounded-3" style="background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                            <a href="${fotoUrl}" target="_blank" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-2 px-3 py-2" style="border-radius: 8px;">
                                <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                <span>Buka Dokumen PDF</span>
                            </a>
                       </div>`
                    : `<div class="p-3 rounded-3" style="background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                            <a href="${fotoUrl}" target="_blank" title="Klik untuk membuka ukuran penuh">
                                <img src="${fotoUrl}" class="rounded-3 shadow-sm" style="max-height: 180px; max-width: 100%; object-fit: contain; cursor: pointer; border: 2px solid #CBD5E1;">
                            </a>
                       </div>`;
            } else {
                fotoSection = `<div class="p-3 text-center rounded-3 text-muted small" style="background-color: #F8FAFC; border: 1px dashed #CBD5E1;">
                        <i class="bi bi-camera me-1"></i> Tidak ada unggahan bukti dokumentasi.
                   </div>`;
            }

            document.getElementById('detail_kehadiran_subtitle').innerText = (klien ? klien.nama_klien : 'Klien') + ' - ' + res.formatted_tgl;
            document.getElementById('detailKehadiranContent').innerHTML = `
                <div class="row g-4 text-start">
                    <div class="col-md-6">
                        <div class="form-label-figma">Nama Klien</div>
                        <div class="fw-medium text-dark">${klien ? klien.nama_klien : '-'}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">No. Register</div>
                        <div class="fw-medium text-dark">${klien ? klien.no_register : '-'}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Tanggal Hadir</div>
                        <div class="fw-medium text-dark">${res.tgl_hadir_ymd || (kh.tgl_hadir ? kh.tgl_hadir.substring(0, 10) : '-')}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Jam</div>
                        <div class="fw-medium text-dark">${kh.jam_hadir ? kh.jam_hadir.substring(0, 5) : '-'} WITA</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Status Kehadiran</div>
                        <div>
                            <span class="${statusClass}">● ${statusNama}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Keterangan / Catatan</div>
                        <div class="fw-medium text-dark">${kh.catatan || '-'}</div>
                    </div>
                    <div class="col-12 mt-3 pt-3 border-top">
                        <div class="form-label-figma mb-2" style="font-weight: 600; color: #102A45; font-size: 0.88rem;">Foto Bukti Kehadiran</div>
                        ${fotoSection}
                    </div>
                </div>
            `;
        })
        .catch(err => {
            document.getElementById('detailKehadiranContent').innerHTML = `<div class="alert alert-danger">Gagal memuat detail kehadiran.</div>`;
        });
    }

    function showEditKehadiran(id) {
        fetch(`/pk/kehadiran/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const kh = res.kehadiran;
            document.getElementById('formEditKehadiran').action = `/pk/kehadiran/${kh.id_kehadiran}`;
            document.getElementById('edit_kehadiran_id_klien').value = kh.id_klien;
            document.getElementById('edit_kehadiran_id_jadwal').value = kh.id_jadwal_bimbingan || '';
            document.getElementById('edit_kehadiran_tgl_hadir').value = res.tgl_hadir_ymd || kh.tgl_hadir.substring(0, 10);
            document.getElementById('edit_kehadiran_jam_hadir').value = kh.jam_hadir.substring(0, 5);
            document.getElementById('edit_kehadiran_status').value = res.status_kehadiran || kh.status_kehadiran || (res.jadwal && res.jadwal.status_bimbingan ? res.jadwal.status_bimbingan.nama_status : 'Hadir');
            document.getElementById('edit_kehadiran_catatan').value = '';

            const editFileInput = document.getElementById('edit_upload_presensi_file');
            if (editFileInput) {
                editFileInput.value = '';
            }
            document.getElementById('edit_presensi_filename').innerText = 'Klik untuk mengganti foto dokumentasi (Opsional)';

            const modal = new bootstrap.Modal(document.getElementById('modalEditKehadiran'));
            modal.show();
        });
    }

    function confirmDeleteKehadiran(id, namaKlien, tgl) {
        document.getElementById('formDeleteKehadiran').action = `/pk/kehadiran/${id}`;
        document.getElementById('deleteKehadiranKlien').innerText = namaKlien;

        const modal = new bootstrap.Modal(document.getElementById('modalDeleteKehadiran'));
        modal.show();
    }

    // ==========================================
    // Real-Time Camera Management for Attendance
    // ==========================================
    const activeStreams = {};
    const currentFacingModes = {
        tambah: 'user',
        edit: 'user'
    };

    async function setKehadiranMediaMode(context, mode) {
        const btnUpload = document.getElementById(`btn_mode_upload_${context}`);
        const btnCamera = document.getElementById(`btn_mode_camera_${context}`);
        const containerUpload = document.getElementById(`container_upload_${context}`);
        const containerCamera = document.getElementById(`container_camera_${context}`);

        if (mode === 'camera') {
            if (btnUpload) {
                btnUpload.style.backgroundColor = 'transparent';
                btnUpload.style.color = '#64748B';
            }
            if (btnCamera) {
                btnCamera.style.backgroundColor = '#102A45';
                btnCamera.style.color = '#FFFFFF';
            }
            if (containerUpload) containerUpload.classList.add('d-none');
            if (containerCamera) containerCamera.classList.remove('d-none');
            await startRealtimeCamera(context);
        } else {
            if (btnUpload) {
                btnUpload.style.backgroundColor = '#102A45';
                btnUpload.style.color = '#FFFFFF';
            }
            if (btnCamera) {
                btnCamera.style.backgroundColor = 'transparent';
                btnCamera.style.color = '#64748B';
            }
            if (containerUpload) containerUpload.classList.remove('d-none');
            if (containerCamera) containerCamera.classList.add('d-none');
            stopRealtimeCamera(context);
        }
    }

    async function startRealtimeCamera(context) {
        const errorEl = document.getElementById(`camera_error_${context}`);
        if (errorEl) {
            errorEl.classList.add('d-none');
            errorEl.innerText = '';
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            if (errorEl) {
                errorEl.classList.remove('d-none');
                errorEl.innerText = 'Peramban tidak mendukung akses kamera secara langsung, atau halaman tidak dibuka melalui HTTPS/Localhost. Silakan gunakan opsi Unggah Berkas.';
            }
            return;
        }

        stopRealtimeCamera(context);

        if (!currentFacingModes[context]) {
            currentFacingModes[context] = 'user';
        }

        try {
            const constraints = {
                video: {
                    facingMode: currentFacingModes[context],
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            };

            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            activeStreams[context] = stream;

            const video = document.getElementById(`camera_video_${context}`);
            if (video) {
                video.srcObject = stream;
                video.style.transform = (currentFacingModes[context] === 'user') ? 'scaleX(-1)' : 'none';
                await video.play();
            }

            const feedWrap = document.getElementById(`camera_feed_wrap_${context}`);
            const previewWrap = document.getElementById(`camera_preview_wrap_${context}`);
            const liveControls = document.getElementById(`camera_live_controls_${context}`);
            const previewControls = document.getElementById(`camera_preview_controls_${context}`);

            if (feedWrap) feedWrap.classList.remove('d-none');
            if (previewWrap) previewWrap.classList.add('d-none');
            if (liveControls) liveControls.classList.remove('d-none');
            if (previewControls) previewControls.classList.add('d-none');

        } catch (err) {
            console.error('Kamera error:', err);
            if (errorEl) {
                errorEl.classList.remove('d-none');
                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    errorEl.innerText = 'Izin kamera ditolak. Berikan izin kamera pada bilah alamat peramban Anda, atau gunakan opsi Unggah Berkas.';
                } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                    errorEl.innerText = 'Perangkat kamera tidak ditemukan pada komputer/perangkat Anda. Silakan gunakan opsi Unggah Berkas.';
                } else {
                    errorEl.innerText = 'Gagal mengakses kamera: ' + (err.message || 'Terjadi kesalahan sistem') + '. Silakan gunakan opsi Unggah Berkas.';
                }
            }
        }
    }

    function captureRealtimeSnapshot(context, fileInputId) {
        const video = document.getElementById(`camera_video_${context}`);
        const canvas = document.getElementById(`camera_canvas_${context}`);
        const previewImg = document.getElementById(`camera_captured_img_${context}`);
        const base64Input = document.getElementById(`foto_kehadiran_base64_${context}`);

        if (!video || !canvas) return;

        const width = video.videoWidth || 640;
        const height = video.videoHeight || 480;
        canvas.width = width;
        canvas.height = height;

        const ctx = canvas.getContext('2d');
        if (currentFacingModes[context] === 'user') {
            ctx.translate(width, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(video, 0, 0, width, height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.92);

        if (previewImg) previewImg.src = dataUrl;
        if (base64Input) base64Input.value = dataUrl;

        canvas.toBlob((blob) => {
            if (blob) {
                const fileName = `bukti_kehadiran_${Date.now()}.jpg`;
                const file = new File([blob], fileName, { type: 'image/jpeg' });
                const fileInput = document.getElementById(fileInputId);
                if (fileInput) {
                    try {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        fileInput.files = dataTransfer.files;
                    } catch (e) {
                        console.warn('DataTransfer files assignment not supported:', e);
                    }
                }
                const filenameSpan = document.getElementById(
                    context === 'tambah' ? 'presensi_filename_kehadiran' : 'edit_presensi_filename'
                );
                if (filenameSpan) {
                    filenameSpan.innerText = `Foto Kamera: ${fileName}`;
                }
            }
        }, 'image/jpeg', 0.92);

        stopRealtimeCamera(context);

        const feedWrap = document.getElementById(`camera_feed_wrap_${context}`);
        const previewWrap = document.getElementById(`camera_preview_wrap_${context}`);
        const liveControls = document.getElementById(`camera_live_controls_${context}`);
        const previewControls = document.getElementById(`camera_preview_controls_${context}`);

        if (feedWrap) feedWrap.classList.add('d-none');
        if (previewWrap) previewWrap.classList.remove('d-none');
        if (liveControls) liveControls.classList.add('d-none');
        if (previewControls) previewControls.classList.remove('d-none');
    }

    async function retakeRealtimeSnapshot(context) {
        const base64Input = document.getElementById(`foto_kehadiran_base64_${context}`);
        if (base64Input) base64Input.value = '';

        await startRealtimeCamera(context);
    }

    async function switchCameraFacing(context) {
        currentFacingModes[context] = (currentFacingModes[context] === 'user') ? 'environment' : 'user';
        await startRealtimeCamera(context);
    }

    function stopRealtimeCamera(context) {
        if (activeStreams[context]) {
            activeStreams[context].getTracks().forEach(track => {
                try { track.stop(); } catch(e){}
            });
            delete activeStreams[context];
        }
        const video = document.getElementById(`camera_video_${context}`);
        if (video) {
            video.srcObject = null;
        }
    }

    // Stop streams when modals close
    document.addEventListener('DOMContentLoaded', function() {
        ['modalTambahKehadiran', 'modalEditKehadiran'].forEach(modalId => {
            const el = document.getElementById(modalId);
            if (el) {
                el.addEventListener('hidden.bs.modal', function() {
                    const ctx = (modalId === 'modalTambahKehadiran') ? 'tambah' : 'edit';
                    stopRealtimeCamera(ctx);
                });
            }
        });
    });
</script>
@endsection
