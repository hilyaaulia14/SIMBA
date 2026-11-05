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
                    <input type="text" name="search" class="form-control form-control-sm border-start-0 filter-pill-input" placeholder="Cari nama atau nomor register..." value="{{ request('search') }}" style="border-radius: 0 50px 50px 0 !important; padding-left: 0.35rem;">
                </div>
            </div>

            <div style="width: 180px;">
                <input type="date" name="tgl_hadir" class="form-control form-control-sm filter-pill-input" value="{{ request('tgl_hadir') }}" onchange="this.form.submit()">
            </div>

            @if(request()->hasAny(['search', 'tgl_hadir']))
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
                                    <img src="{{ $kh->foto_url }}" alt="Bukti Hadir" class="rounded shadow-sm" style="width: 40px; height: 40px; object-fit: cover; cursor: pointer; border: 2px solid #E2E8F0;" onclick="previewFoto('{{ $kh->foto_url }}', '{{ addslashes($kh->klien->nama_klien ?? '') }}', '{{ $kh->tgl_hadir->format('d/m/Y') }}')" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'badge bg-light text-muted border\'>Tanpa Foto</span>';">
                                @else
                                    <span class="badge" style="background-color: #F1F5F9; color: #64748B; font-size: 0.75rem; border-radius: 6px; padding: 0.35rem 0.6rem; font-weight: 400;">Tanpa Foto</span>
                                @endif
                            </td>
                            <td style="white-space: normal; line-height: 1.5;">
                                {{ $kh->catatan }}
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
                            <label class="form-label-figma">Bukti Dokumentasi <span class="text-danger">*</span></label>
                            <div class="upload-dropzone-figma" onclick="document.getElementById('upload_presensi_file_kehadiran').click()">
                                <img src="{{ asset('assets/images/unggah.svg') }}" alt="Unggah" style="width: 44px; height: 44px; object-fit: contain; margin-bottom: 0.5rem;">
                                <span class="small text-muted d-block" id="presensi_filename_kehadiran">Klik untuk mengunggah bukti</span>
                                <input type="file" name="foto_kehadiran" id="upload_presensi_file_kehadiran" class="d-none" accept="image/*,.pdf" onchange="updateFilename(this, 'presensi_filename_kehadiran')">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Keterangan</label>
                            <input type="text" name="catatan" class="form-control form-control-figma" placeholder="Keterangan (Opsional)" value="Klien hadir bimbingan tatap muka sesuai jadwal." required>
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
                            <label class="form-label-figma">Bukti Dokumentasi</label>
                            <div class="upload-dropzone-figma" onclick="document.getElementById('edit_upload_presensi_file').click()">
                                <img src="{{ asset('assets/images/unggah.svg') }}" alt="Unggah" style="width: 44px; height: 44px; object-fit: contain; margin-bottom: 0.5rem;">
                                <span class="small text-muted d-block" id="edit_presensi_filename">Klik untuk mengganti foto dokumentasi (Opsional)</span>
                                <input type="file" name="foto_kehadiran" id="edit_upload_presensi_file" class="d-none" accept="image/*,.pdf" onchange="updateFilename(this, 'edit_presensi_filename')">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Keterangan</label>
                            <input type="text" name="catatan" id="edit_kehadiran_catatan" class="form-control form-control-figma" placeholder="Keterangan (Opsional)" required>
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
                    Catatan kehadiran klien <strong id="deleteKehadiranKlien"></strong> akan dihapus permanen dari sistem.<br>Tindakan ini tidak dapat dibatalkan.
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

            let fotoSection = fotoUrl 
                ? `<div class="p-3 rounded-3" style="background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                        <a href="${fotoUrl}" target="_blank" title="Klik untuk membuka ukuran penuh">
                            <img src="${fotoUrl}" class="rounded-3 shadow-sm" style="max-height: 180px; max-width: 100%; object-fit: contain; cursor: pointer; border: 2px solid #CBD5E1;">
                        </a>
                   </div>`
                : `<div class="p-3 text-center rounded-3 text-muted small" style="background-color: #F8FAFC; border: 1px dashed #CBD5E1;">
                        <i class="bi bi-camera me-1"></i> Tidak ada unggahan foto fisik dokumentasi.
                   </div>`;

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
                        <div class="fw-medium text-dark">${kh.tgl_hadir ? kh.tgl_hadir.substring(0, 10) : '-'}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Jam Kedatangan</div>
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
            document.getElementById('edit_kehadiran_tgl_hadir').value = kh.tgl_hadir.substring(0, 10);
            document.getElementById('edit_kehadiran_jam_hadir').value = kh.jam_hadir.substring(0, 5);
            document.getElementById('edit_kehadiran_status').value = res.status_kehadiran || kh.status_kehadiran || (res.jadwal && res.jadwal.status_bimbingan ? res.jadwal.status_bimbingan.nama_status : 'Hadir');
            document.getElementById('edit_kehadiran_catatan').value = kh.catatan;

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
</script>
@endsection
