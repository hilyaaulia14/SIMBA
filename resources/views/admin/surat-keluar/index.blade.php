@extends('layouts.app')

@section('title', 'Surat Keluar - Panel Admin')
@section('page_title', 'Kelola Arsip Surat Keluar')

@section('content')
<div class="container-fluid px-0">

    <!-- Header Actions -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Daftar Surat Keluar</h4>
            <p class="text-muted small mb-0">Pencatatan dan pengarsipan digital surat dinas yang diterbitkan oleh Bapas Kelas I Banjarmasin.</p>
        </div>
        <div>
            <button class="btn btn-success fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSuratKeluar">
                <i class="bi bi-plus-circle fs-6"></i>
                <span>Tambah Surat Keluar</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.surat-keluar.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No. Surat, Tujuan/Penerima, atau Perihal..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <select name="id_surat" class="form-select form-select-sm">
                        <option value="">Semua Klasifikasi Surat</option>
                        @foreach($klasifikasiSurat as $klas)
                            <option value="{{ $klas->id_surat }}" {{ request('id_surat') == $klas->id_surat ? 'selected' : '' }}>
                                {{ $klas->nama_surat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-success flex-grow-1">Filter</button>
                    @if(request()->hasAny(['search', 'id_surat']))
                        <a href="{{ route('admin.surat-keluar.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                            <th>No. Agenda / Surat</th>
                            <th>Klasifikasi</th>
                            <th>Tujuan / Penerima</th>
                            <th>Tgl. Keluar</th>
                            <th>Perihal</th>
                            <th class="text-center">Lampiran</th>
                            <th style="width: 140px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suratKeluarList as $index => $sk)
                            <tr>
                                <td class="text-center text-muted small">{{ $suratKeluarList->firstItem() + $index }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $sk->no_surat }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        {{ $sk->surat->nama_surat ?? 'Umum' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $sk->tujuan_surat }}</div>
                                </td>
                                <td>
                                    <span class="text-nowrap small text-muted">
                                        <i class="bi bi-calendar3 me-1"></i> {{ $sk->tgl_keluar->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $sk->perihal }}">
                                        {{ $sk->perihal }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($sk->upload_dokumen)
                                        <a href="{{ route('admin.surat-keluar.download', $sk->id_surat_keluar) }}" class="btn btn-sm btn-outline-success px-2 py-1 d-inline-flex align-items-center gap-1" title="Unduh Berkas">
                                            <i class="bi bi-file-earmark-arrow-down-fill"></i>
                                            <span class="small">Unduh</span>
                                        </a>
                                    @else
                                        <span class="badge bg-light text-muted border">Tidak Ada</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-info" title="Detail Surat" onclick="showDetailKeluar({{ $sk->id_surat_keluar }})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" title="Edit Surat" onclick="showEditKeluar({{ $sk->id_surat_keluar }})">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Hapus Surat" onclick="confirmDeleteKeluar({{ $sk->id_surat_keluar }}, '{{ addslashes($sk->no_surat) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-send fs-1 d-block mb-2 text-secondary"></i>
                                    Tidak ada data surat keluar yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($suratKeluarList->hasPages())
                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Menampilkan {{ $suratKeluarList->firstItem() }} - {{ $suratKeluarList->lastItem() }} dari {{ $suratKeluarList->total() }} data
                    </small>
                    {{ $suratKeluarList->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Modal Tambah Surat Keluar -->
<div class="modal fade" id="modalTambahSuratKeluar" tabindex="-1" aria-labelledby="modalTambahKeluarTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.surat-keluar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTambahKeluarTitle">
                        <i class="bi bi-send-fill text-success me-2"></i> Terbitkan Surat Keluar Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Klasifikasi Surat <span class="text-danger">*</span></label>
                            <select name="id_surat" class="form-select" required>
                                <option value="">Pilih Klasifikasi Arsip</option>
                                @foreach($klasifikasiSurat as $klas)
                                    <option value="{{ $klas->id_surat }}">{{ $klas->nama_surat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Nomor Agenda / Surat <span class="text-danger">*</span></label>
                            <input type="text" name="no_surat" class="form-control" placeholder="Contoh: W19.PAS.PAS.10-PK.02.01-094" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Tujuan Surat (Penerima) <span class="text-danger">*</span></label>
                            <input type="text" name="tujuan_surat" class="form-control" placeholder="Contoh: Lapas Kelas IIA Banjarmasin" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Keluar <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_keluar" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Perihal Surat <span class="text-danger">*</span></label>
                            <textarea name="perihal" class="form-control" rows="3" placeholder="Uraikan ringkasan pokok perihal surat keluar..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Upload Scan Dokumen (PDF, DOC, JPG, PNG)</label>
                            <input type="file" name="upload_dokumen" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">Maksimal ukuran file: 10MB.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-semibold">Simpan Surat Keluar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Surat Keluar -->
<div class="modal fade" id="modalDetailSuratKeluar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-info-circle-fill text-info me-2"></i> Rincian Informasi Surat Keluar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailKeluarContent">
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

<!-- Modal Edit Surat Keluar -->
<div class="modal fade" id="modalEditSuratKeluar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditSuratKeluar" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square text-warning me-2"></i> Edit Data Surat Keluar
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Klasifikasi Surat <span class="text-danger">*</span></label>
                            <select name="id_surat" id="edit_keluar_id_surat" class="form-select" required>
                                @foreach($klasifikasiSurat as $klas)
                                    <option value="{{ $klas->id_surat }}">{{ $klas->nama_surat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Nomor Agenda / Surat <span class="text-danger">*</span></label>
                            <input type="text" name="no_surat" id="edit_keluar_no_surat" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Tujuan Surat (Penerima) <span class="text-danger">*</span></label>
                            <input type="text" name="tujuan_surat" id="edit_keluar_tujuan_surat" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Keluar <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_keluar" id="edit_keluar_tgl_keluar" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Perihal Surat <span class="text-danger">*</span></label>
                            <textarea name="perihal" id="edit_keluar_perihal" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Ganti Berkas Scan (Opsional)</label>
                            <input type="file" name="upload_dokumen" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah berkas saat ini.</small>
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

<!-- Modal Konfirmasi Hapus Surat Keluar -->
<div class="modal fade" id="modalDeleteSuratKeluar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formDeleteSuratKeluar" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Hapus Surat Keluar
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="mb-2">Apakah Anda yakin ingin menghapus data surat keluar ini?</p>
                    <div class="fw-bold fs-5 text-dark" id="deleteKeluarNumber">-</div>
                    <small class="text-muted d-block mt-2">Data yang telah dihapus beserta berkas lampiran digitalnya tidak dapat dikembalikan.</small>
                </div>
                <div class="modal-footer bg-light justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 fw-semibold">Ya, Hapus Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showDetailKeluar(id) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetailSuratKeluar'));
        modal.show();

        fetch(`/admin/surat-keluar/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const data = res.data;
            let downloadBtn = data.upload_dokumen 
                ? `<a href="/admin/surat-keluar/${data.id_surat_keluar}/download" class="btn btn-sm btn-success mt-2"><i class="bi bi-download me-1"></i> Unduh Berkas Digital</a>`
                : `<span class="badge bg-secondary">Berkas digital belum diunggah</span>`;

            document.getElementById('detailKeluarContent').innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Nomor Agenda / Surat:</div>
                        <div class="fw-bold text-dark fs-6">${data.no_surat}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Klasifikasi Surat:</div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">${data.surat ? data.surat.nama_surat : '-'}</span>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Tujuan Surat (Penerima):</div>
                        <div class="fw-semibold text-dark">${data.tujuan_surat}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Tanggal Keluar:</div>
                        <div class="fw-semibold text-dark">${res.formatted_date}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Perihal Surat:</div>
                        <div class="p-3 bg-light rounded-3 mt-1 text-dark">${data.perihal}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Berkas Lampiran Digital:</div>
                        <div class="mt-1">${downloadBtn}</div>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            document.getElementById('detailKeluarContent').innerHTML = `<div class="alert alert-danger">Gagal memuat rincian surat keluar.</div>`;
        });
    }

    function showEditKeluar(id) {
        fetch(`/admin/surat-keluar/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const data = res.data;
            document.getElementById('formEditSuratKeluar').action = `/admin/surat-keluar/${data.id_surat_keluar}`;
            document.getElementById('edit_keluar_id_surat').value = data.id_surat;
            document.getElementById('edit_keluar_no_surat').value = data.no_surat;
            document.getElementById('edit_keluar_tujuan_surat').value = data.tujuan_surat;
            document.getElementById('edit_keluar_tgl_keluar').value = data.tgl_keluar.substring(0, 10);
            document.getElementById('edit_keluar_perihal').value = data.perihal;
            
            const modal = new bootstrap.Modal(document.getElementById('modalEditSuratKeluar'));
            modal.show();
        });
    }

    function confirmDeleteKeluar(id, noSurat) {
        document.getElementById('formDeleteSuratKeluar').action = `/admin/surat-keluar/${id}`;
        document.getElementById('deleteKeluarNumber').innerText = noSurat;
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteSuratKeluar'));
        modal.show();
    }
</script>
@endsection
