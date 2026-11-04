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
                        <th style="width: 5%;" class="text-center">No</th>
                        <th style="width: 17%;">Tanggal & Jam Hadir</th>
                        <th style="width: 16%;">No. Register</th>
                        <th style="width: 18%;">Nama Klien</th>
                        <th style="width: 12%;" class="text-center">Foto Bukti</th>
                        <th style="width: 22%;">Evaluasi Perkembangan</th>
                        <th style="width: 10%;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kehadiranList as $index => $kh)
                        <tr>
                            <td class="text-center text-muted">{{ $kehadiranList->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $kh->tgl_hadir->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ substr($kh->jam_hadir, 0, 5) }} WITA</small>
                            </td>
                            <td>
                                <span class="font-monospace fw-bold text-dark">{{ $kh->klien->no_register ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $kh->klien->nama_klien ?? 'Klien Dihapus' }}</span>
                            </td>
                            <td class="text-center">
                                @if($kh->foto_url)
                                    <img src="{{ $kh->foto_url }}" alt="Bukti Hadir" class="rounded shadow-sm" style="width: 44px; height: 44px; object-fit: cover; cursor: pointer; border: 2px solid #E2E8F0;" onclick="previewFoto('{{ $kh->foto_url }}', '{{ addslashes($kh->klien->nama_klien ?? '') }}', '{{ $kh->tgl_hadir->format('d/m/Y') }}')" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'badge bg-light text-muted border\'>Tanpa Foto</span>';">
                                @else
                                    <span class="badge" style="background-color: #F1F5F9; color: #64748B; font-size: 0.75rem; border-radius: 6px; padding: 0.35rem 0.6rem;">Tanpa Foto</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;" title="{{ $kh->catatan }}">
                                    {{ $kh->catatan }}
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <button class="btn-action-square" title="Detail Kehadiran" onclick="showDetailKehadiran({{ $kh->id_kehadiran }})">
                                        <img src="{{ asset('assets/images/detail.svg') }}" alt="Detail">
                                    </button>
                                    <button class="btn-action-square" title="Edit Presensi" onclick="showEditKehadiran({{ $kh->id_kehadiran }})">
                                        <img src="{{ asset('assets/images/edit.svg') }}" alt="Edit">
                                    </button>
                                    <button class="btn-action-square" title="Hapus Presensi" onclick="confirmDeleteKehadiran({{ $kh->id_kehadiran }}, '{{ addslashes($kh->klien->nama_klien ?? '') }}', '{{ $kh->tgl_hadir->format('d/m/Y') }}')">
                                        <img src="{{ asset('assets/images/hapus.svg') }}" alt="Hapus">
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Tidak ada data bukti kehadiran yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kehadiranList->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2" style="font-size: 0.82rem;">
                <span class="text-muted">
                    Menampilkan {{ $kehadiranList->firstItem() }} - {{ $kehadiranList->lastItem() }} dari {{ $kehadiranList->total() }} presensi
                </span>
                <div>
                    {{ $kehadiranList->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Modal Catat Bukti Kehadiran Klien -->
<div class="modal fade" id="modalTambahKehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form action="{{ route('pk.kehadiran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header-figma">
                    <div>
                        <h5 class="modal-title-figma">Input Bukti Kehadiran Klien</h5>
                        <p class="modal-subtitle-figma">Catat kehadiran fisik, upload foto dokumentasi dan evaluasi sikap klien</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Pilih Klien <span class="text-danger">*</span></label>
                            <select name="id_klien" class="form-select form-control-figma" required>
                                <option value="">Pilih Klien</option>
                                @foreach($allKlien as $k)
                                    <option value="{{ $k->id_klien }}">{{ $k->no_register }} - {{ $k->nama_klien }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Kaitkan Jadwal Bimbingan (Opsional)</label>
                            <select name="id_jadwal_bimbingan" class="form-select form-control-figma">
                                <option value="">-- Tanpa Kaitan Jadwal --</option>
                                @foreach($recentJadwal as $rj)
                                    <option value="{{ $rj->id_jadwal_bimbingan }}">
                                        {{ $rj->tgl_bimbingan->format('d/m/Y') }} - {{ $rj->klien->nama_klien ?? '' }} ({{ substr($rj->jam_mulai, 0, 5) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tanggal Hadir <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_hadir" class="form-control form-control-figma" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jam Kedatangan <span class="text-danger">*</span></label>
                            <input type="time" name="jam_hadir" class="form-control form-control-figma" value="{{ date('H:i') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Foto Bukti Fisik / Dokumentasi (Opsional)</label>
                            <div class="upload-dropzone p-3 text-center" style="border: 2px dashed #93C5FD; border-radius: 12px; background: #F8FAFC; cursor: pointer;" onclick="document.getElementById('input_foto_kehadiran').click()">
                                <img src="{{ asset('assets/images/unggah.svg') }}" alt="Unggah" style="width: 44px; height: 44px; object-fit: contain; margin-bottom: 0.5rem;">
                                <span class="fw-semibold text-dark d-block" style="font-size: 0.84rem;">Klik untuk mengunggah foto kehadiran</span>
                                <div class="text-muted" style="font-size: 0.75rem;">Format JPG, svg, WEBP (Maksimal 5MB)</div>
                                <input type="file" id="input_foto_kehadiran" name="foto_kehadiran" class="d-none" accept="image/jpeg,image/svg,image/jpg,image/webp" onchange="previewUploadName(this, 'tambah_foto_label')">
                                <div id="tambah_foto_label" class="mt-2 text-primary fw-semibold small"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Catatan Evaluasi Perkembangan Kepribadian / Kemandirian <span class="text-danger">*</span></label>
                            <textarea name="catatan" class="form-control form-control-figma" rows="3" placeholder="Tuliskan catatan observasi, evaluasi sikap kepribadian, kedisiplinan, atau kegiatan positif klien..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-figma">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.84rem;">Batal</button>
                    <button type="submit" class="btn-simba-primary px-4">Simpan Rekaman</button>
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
                    <h5 class="modal-title-figma">Rincian Bukti Kehadiran</h5>
                    <p class="modal-subtitle-figma">Detail dokumentasi presensi dan catatan observasi klien</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailKehadiranContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer-figma">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.84rem;">Tutup</button>
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
                        <h5 class="modal-title-figma">Edit Data Presensi Kehadiran</h5>
                        <p class="modal-subtitle-figma">Perbarui data bukti kehadiran dan catatan evaluasi</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Klien Pemasyarakatan <span class="text-danger">*</span></label>
                            <select name="id_klien" id="edit_kehadiran_id_klien" class="form-select form-control-figma" required>
                                @foreach($allKlien as $k)
                                    <option value="{{ $k->id_klien }}">{{ $k->no_register }} - {{ $k->nama_klien }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Kaitkan Jadwal Bimbingan</label>
                            <select name="id_jadwal_bimbingan" id="edit_kehadiran_id_jadwal" class="form-select form-control-figma">
                                <option value="">-- Tanpa Kaitan Jadwal --</option>
                                @foreach($recentJadwal as $rj)
                                    <option value="{{ $rj->id_jadwal_bimbingan }}">
                                        {{ $rj->tgl_bimbingan->format('d/m/Y') }} - {{ $rj->klien->nama_klien ?? '' }}
                                    </option>
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
                            <label class="form-label-figma">Ganti Foto Bukti (Opsional)</label>
                            <input type="file" name="foto_kehadiran" class="form-control form-control-figma" accept="image/jpeg,image/svg,image/jpg,image/webp">
                            <div class="text-muted" style="font-size: 0.75rem;">Biarkan kosong jika tidak ingin mengubah foto dokumentasi.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label-figma">Catatan Evaluasi Perkembangan <span class="text-danger">*</span></label>
                            <textarea name="catatan" id="edit_kehadiran_catatan" class="form-control form-control-figma" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-figma">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.84rem;">Batal</button>
                    <button type="submit" class="btn-simba-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Kehadiran -->
<div class="modal fade" id="modalDeleteKehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content modal-content-figma text-center p-3">
            <div class="modal-body p-3">
                <div class="delete-dialog-badge mx-auto">
                    <img src="{{ asset('assets/images/warning.svg') }}" alt="Peringatan">
                </div>
                <h5 class="fw-bold mb-2" style="color: #1E293B;">Hapus Presensi?</h5>
                <p class="text-muted small mb-2">
                    Apakah Anda yakin ingin menghapus catatan bukti kehadiran klien <strong id="deleteKehadiranKlien" class="text-dark"></strong>?
                </p>
                <div class="text-muted small mb-4" id="deleteKehadiranTgl" style="font-size: 0.78rem;"></div>
                <form id="formDeleteKehadiran" method="POST" class="d-flex justify-content-center gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.84rem;">Batal</button>
                    <button type="submit" class="btn btn-danger px-4" style="border-radius: 8px; font-size: 0.84rem; background-color: #EF4444; border-color: #EF4444;">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewUploadName(input, labelId) {
        if (input.files && input.files[0]) {
            document.getElementById(labelId).innerText = "File terpilih: " + input.files[0].name;
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
            let fotoSection = res.foto_url 
                ? `<div class="mt-2 text-center">
                        <img src="${res.foto_url}" class="img-fluid rounded shadow-sm" style="max-height: 240px; border: 2px solid #E2E8F0;">
                   </div>`
                : `<span class="badge-simba" style="background-color: #F1F5F9; color: #94A3B8;">Tidak ada unggahan foto fisik</span>`;

            document.getElementById('detailKehadiranContent').innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Nama Klien Pemasyarakatan:</div>
                        <div class="fw-bold fs-6" style="color: var(--simba-navy);">${klien ? klien.nama_klien : '-'}</div>
                        <small class="text-muted font-monospace">${klien ? klien.no_register : ''}</small>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Waktu Kehadiran:</div>
                        <div class="fw-semibold text-dark">${res.formatted_tgl} &bull; ${kh.jam_hadir.substring(0, 5)} WITA</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Catatan Observasi / Perkembangan PK:</div>
                        <div class="p-3 rounded-3 mt-1 text-dark" style="background-color: var(--simba-bg);">${kh.catatan}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Foto Bukti Kehadiran Wajib Lapor:</div>
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
            document.getElementById('edit_kehadiran_catatan').value = kh.catatan;

            const modal = new bootstrap.Modal(document.getElementById('modalEditKehadiran'));
            modal.show();
        });
    }

    function confirmDeleteKehadiran(id, namaKlien, tgl) {
        document.getElementById('formDeleteKehadiran').action = `/pk/kehadiran/${id}`;
        document.getElementById('deleteKehadiranKlien').innerText = namaKlien;
        document.getElementById('deleteKehadiranTgl').innerText = `Tanggal Hadir: ${tgl}`;

        const modal = new bootstrap.Modal(document.getElementById('modalDeleteKehadiran'));
        modal.show();
    }
</script>
@endsection
