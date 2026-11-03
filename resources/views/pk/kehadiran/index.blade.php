@extends('layouts.app')

@section('title', 'Bukti Kehadiran Klien - SIMBA')
@section('page_title', 'Pencatatan Bukti Kehadiran Wajib Lapor Klien')

@section('content')
<div class="container-fluid px-0">

    <!-- Header Actions -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Pencatatan Bukti Kehadiran Klien</h4>
            <p class="text-muted small mb-0">Dokumentasi absensi fisik tatap muka, verifikasi foto wajib lapor, dan pencatatan evaluasi perkembangan kepribadian klien.</p>
        </div>
        <div>
            <button class="btn btn-success fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKehadiran">
                <i class="bi bi-camera-fill fs-6"></i>
                <span>Catat Bukti Kehadiran</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('pk.kehadiran.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Nama Klien atau No. Register..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <input type="date" name="tgl_hadir" class="form-control form-control-sm" value="{{ request('tgl_hadir') }}" title="Filter Tanggal Kehadiran">
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-success flex-grow-1">Filter</button>
                    @if(request()->hasAny(['search', 'tgl_hadir']))
                        <a href="{{ route('pk.kehadiran.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Tanggal & Jam Hadir</th>
                            <th>No. Register Klien</th>
                            <th>Nama Lengkap Klien</th>
                            <th class="text-center">Foto Bukti</th>
                            <th>Evaluasi Perkembangan Kepribadian/Kemandirian</th>
                            <th style="width: 140px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kehadiranList as $index => $kh)
                            <tr>
                                <td class="text-center text-muted small">{{ $kehadiranList->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        <i class="bi bi-calendar3 me-1 text-muted"></i> {{ $kh->tgl_hadir->format('d/m/Y') }}
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i> {{ substr($kh->jam_hadir, 0, 5) }} WITA
                                    </small>
                                </td>
                                <td>
                                    <span class="fw-bold font-monospace text-dark">{{ $kh->klien->no_register ?? '-' }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $kh->klien->nama_klien ?? 'Klien Dihapus' }}</div>
                                </td>
                                <td class="text-center">
                                    @if($kh->foto_kehadiran)
                                        <img src="{{ $kh->foto_url }}" alt="Foto Presensi" class="rounded shadow-sm" style="width: 48px; height: 48px; object-fit: cover; cursor: pointer;" onclick="previewFoto('{{ $kh->foto_url }}', '{{ addslashes($kh->klien->nama_klien ?? '') }}', '{{ $kh->tgl_hadir->format('d/m/Y') }}')">
                                    @else
                                        <span class="badge bg-light text-muted border">Tanpa Foto</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 250px;" title="{{ $kh->catatan }}">
                                        {{ $kh->catatan }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-info" title="Detail Bukti Kehadiran" onclick="showDetailKehadiran({{ $kh->id_kehadiran }})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" title="Edit Presensi" onclick="showEditKehadiran({{ $kh->id_kehadiran }})">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Hapus Presensi" onclick="confirmDeleteKehadiran({{ $kh->id_kehadiran }}, '{{ addslashes($kh->klien->nama_klien ?? '') }}', '{{ $kh->tgl_hadir->format('d/m/Y') }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-camera fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada rekaman bukti kehadiran klien yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($kehadiranList->hasPages())
                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Menampilkan {{ $kehadiranList->firstItem() }} - {{ $kehadiranList->lastItem() }} dari {{ $kehadiranList->total() }} presensi
                    </small>
                    {{ $kehadiranList->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Modal Catat Bukti Kehadiran Klien -->
<div class="modal fade" id="modalTambahKehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('pk.kehadiran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-camera-fill text-success me-2"></i> Input Bukti Kehadiran Klien Pemasyarakatan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Pilih Klien Pemasyarakatan <span class="text-danger">*</span></label>
                            <select name="id_klien" class="form-select" required>
                                <option value="">Pilih Klien</option>
                                @foreach($allKlien as $k)
                                    <option value="{{ $k->id_klien }}">{{ $k->no_register }} - {{ $k->nama_klien }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Kaitkan dengan Sesi Jadwal (Opsional)</label>
                            <select name="id_jadwal_bimbingan" class="form-select">
                                <option value="">-- Tanpa Kaitan Jadwal Khusus --</option>
                                @foreach($recentJadwal as $rj)
                                    <option value="{{ $rj->id_jadwal_bimbingan }}">
                                        {{ $rj->tgl_bimbingan->format('d/m/Y') }} - {{ $rj->klien->nama_klien ?? '' }} ({{ substr($rj->jam_mulai, 0, 5) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Jika dipilih, status jadwal otomatis diperbarui menjadi "Terlaksana".</small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Hadir Wajib Lapor <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_hadir" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Jam Kedatangan Fisik <span class="text-danger">*</span></label>
                            <input type="time" name="jam_hadir" class="form-control" value="{{ date('H:i') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Foto Bukti Kehadiran Klien (Opsional/Disarankan)</label>
                            <input type="file" name="foto_kehadiran" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <small class="text-muted">Foto fisik kedatangan/wajib lapor klien di kantor Bapas. Maksimal 5MB.</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Catatan Evaluasi Perkembangan Kepribadian / Kemandirian <span class="text-danger">*</span></label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Tuliskan catatan observasi, evaluasi sikap kepribadian, kedisiplinan, atau kegiatan positif klien selama masa bimbingan..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-semibold">Simpan Rekaman Kehadiran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Bukti Kehadiran -->
<div class="modal fade" id="modalDetailKehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-info-circle-fill text-info me-2"></i> Rincian Bukti Presensi Kehadiran Klien
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailKehadiranContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-success" role="status"></div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Foto Zoom -->
<div class="modal fade" id="modalPreviewFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="previewFotoTitle">Foto Bukti Kehadiran</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img id="previewFotoImg" src="" alt="Bukti Kehadiran" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Presensi Kehadiran -->
<div class="modal fade" id="modalEditKehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditKehadiran" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square text-warning me-2"></i> Edit Data Presensi Kehadiran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Klien Pemasyarakatan <span class="text-danger">*</span></label>
                            <select name="id_klien" id="edit_kehadiran_id_klien" class="form-select" required>
                                @foreach($allKlien as $k)
                                    <option value="{{ $k->id_klien }}">{{ $k->no_register }} - {{ $k->nama_klien }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Kaitkan Jadwal Bimbingan</label>
                            <select name="id_jadwal_bimbingan" id="edit_kehadiran_id_jadwal" class="form-select">
                                <option value="">-- Tanpa Kaitan Jadwal --</option>
                                @foreach($recentJadwal as $rj)
                                    <option value="{{ $rj->id_jadwal_bimbingan }}">
                                        {{ $rj->tgl_bimbingan->format('d/m/Y') }} - {{ $rj->klien->nama_klien ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Hadir <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_hadir" id="edit_kehadiran_tgl_hadir" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Jam Hadir <span class="text-danger">*</span></label>
                            <input type="time" name="jam_hadir" id="edit_kehadiran_jam_hadir" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Ganti Foto Bukti (Opsional)</label>
                            <input type="file" name="foto_kehadiran" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengganti foto bukti yang sudah ada.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Catatan Evaluasi Perkembangan <span class="text-danger">*</span></label>
                            <textarea name="catatan" id="edit_kehadiran_catatan" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-semibold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Kehadiran -->
<div class="modal fade" id="modalDeleteKehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formDeleteKehadiran" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Hapus Presensi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="mb-2">Apakah Anda yakin ingin menghapus catatan bukti kehadiran ini?</p>
                    <div class="fw-bold fs-5 text-dark" id="deleteKehadiranKlien">-</div>
                    <small class="text-muted d-block mt-2" id="deleteKehadiranTgl">-</small>
                </div>
                <div class="modal-footer bg-light justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 fw-semibold">Ya, Hapus Presensi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
                        <img src="${res.foto_url}" class="img-fluid rounded shadow" style="max-height: 250px;">
                   </div>`
                : `<span class="badge bg-secondary">Tidak ada unggahan foto fisik</span>`;

            document.getElementById('detailKehadiranContent').innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Nama Klien Pemasyarakatan:</div>
                        <div class="fw-bold fs-6 text-dark">${klien ? klien.nama_klien : '-'}</div>
                        <small class="text-muted font-monospace">${klien ? klien.no_register : ''}</small>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Waktu Kehadiran:</div>
                        <div class="fw-semibold text-dark">${res.formatted_tgl} &bull; ${kh.jam_hadir.substring(0, 5)} WITA</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Catatan Observasi / Perkembangan PK:</div>
                        <div class="p-3 bg-light rounded-3 mt-1 text-dark">${kh.catatan}</div>
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
