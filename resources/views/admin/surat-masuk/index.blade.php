@extends('layouts.app')

@section('title', 'Surat Masuk - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row">
        <div>
            <h3 class="page-title-main">Surat Masuk</h3>
            <p class="page-subtitle-main">Kelola data arsip persuratan masuk Bapas Kelas I Banjarmasin</p>
        </div>
        <div>
            <button class="btn-simba-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSuratMasuk">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Surat Masuk</span>
            </button>
        </div>
    </div>

    <!-- Table Card with Filter Bar -->
    <div class="table-card-figma">
        <div class="table-filter-bar">
            <form action="{{ route('admin.surat-masuk.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
                <div style="max-width: 320px; width: 100%;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px; border-color: var(--simba-border);">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Cari surat..." value="{{ request('search') }}" style="border-radius: 0 8px 8px 0; border-color: var(--simba-border);">
                    </div>
                </div>

                <div style="max-width: 240px; width: 100%;">
                    <select name="id_surat" class="form-select form-select-sm" style="border-radius: 8px; border-color: var(--simba-border);" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($klasifikasiSurat as $klas)
                            <option value="{{ $klas->id_surat }}" {{ request('id_surat') == $klas->id_surat ? 'selected' : '' }}>
                                {{ $klas->nama_surat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(request()->hasAny(['search', 'id_surat']))
                    <a href="{{ route('admin.surat-masuk.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table-simba">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>No. Surat</th>
                        <th>Pengirim</th>
                        <th>Tanggal Masuk</th>
                        <th>Perihal</th>
                        <th>Klasifikasi</th>
                        <th class="text-center">Lampiran</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratMasukList as $index => $sm)
                        <tr>
                            <td class="text-center text-muted">{{ $suratMasukList->firstItem() + $index }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $sm->no_surat }}</span>
                            </td>
                            <td>{{ $sm->asal_surat }}</td>
                            <td>{{ $sm->tgl_masuk->format('d/m/Y') }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 220px;" title="{{ $sm->perihal }}">
                                    {{ $sm->perihal }}
                                </div>
                            </td>
                            <td>
                                <span class="badge-simba badge-simba-info">
                                    {{ $sm->surat->nama_surat ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($sm->upload_dokumen)
                                    <a href="{{ route('admin.surat-masuk.download', $sm->id_surat_masuk) }}" class="btn-action-icon btn-action-view" title="Unduh Berkas">
                                        <i class="bi bi-file-earmark-arrow-down-fill text-primary"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <button class="btn-action-icon btn-action-view" title="Detail Surat" onclick="showDetail({{ $sm->id_surat_masuk }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn-action-icon btn-action-edit" title="Edit Surat" onclick="showEdit({{ $sm->id_surat_masuk }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn-action-icon btn-action-delete" title="Hapus Surat" onclick="confirmDelete({{ $sm->id_surat_masuk }}, '{{ addslashes($sm->no_surat) }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Tidak ada data surat masuk yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suratMasukList->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center" style="font-size: 0.8rem;">
                <span class="text-muted">
                    Menampilkan {{ $suratMasukList->firstItem() }} - {{ $suratMasukList->lastItem() }} dari {{ $suratMasukList->total() }} data
                </span>
                {{ $suratMasukList->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Surat Masuk -->
<div class="modal fade" id="modalTambahSuratMasuk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form action="{{ route('admin.surat-masuk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header-figma">
                    <div>
                        <h5 class="modal-title-figma">Tambah Surat Masuk</h5>
                        <p class="modal-subtitle-figma">Masukkan data surat masuk baru ke dalam sistem arsip SIMBA</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nomor Agenda / Surat <span class="text-danger">*</span></label>
                            <input type="text" name="no_surat" class="form-control form-control-figma" placeholder="Contoh: W19.PAS-UM.01.01-445" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Klasifikasi Surat <span class="text-danger">*</span></label>
                            <select name="id_surat" class="form-select form-control-figma" required>
                                <option value="">Pilih Kategori Surat</option>
                                @foreach($klasifikasiSurat as $klas)
                                    <option value="{{ $klas->id_surat }}">{{ $klas->nama_surat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Pengirim (Asal Surat) <span class="text-danger">*</span></label>
                            <input type="text" name="asal_surat" class="form-control form-control-figma" placeholder="Contoh: Kanwil Kemenkumham Kalsel" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_masuk" class="form-control form-control-figma" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-figma">Perihal <span class="text-danger">*</span></label>
                            <textarea name="perihal" class="form-control form-control-figma" rows="3" placeholder="Uraikan pokok ringkasan perihal surat..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label-figma">Unggah Berkas Lampiran</label>
                            <label class="upload-dropzone-figma d-block mb-0">
                                <i class="bi bi-cloud-arrow-up-fill fs-2" style="color: var(--simba-navy);"></i>
                                <div class="fw-bold mt-1" style="font-size: 0.85rem; color: var(--simba-navy);">Klik untuk unggah atau seret file ke sini</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Format: PDF, DOCX, JPG (Maks. 10MB)</div>
                                <input type="file" name="upload_dokumen" class="d-none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="previewUploadName(this, 'tambah_file_label')">
                            </label>
                            <div id="tambah_file_label" class="mt-1 small text-primary fw-medium"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-figma">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.84rem;">Batal</button>
                    <button type="submit" class="btn-simba-primary" style="border-radius: 8px; padding: 0.65rem 1.75rem;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Surat Masuk -->
<div class="modal fade" id="modalDetailSuratMasuk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <div class="modal-header-figma">
                <div>
                    <h5 class="modal-title-figma">Detail Surat Masuk</h5>
                    <p class="modal-subtitle-figma">Informasi lengkap dokumen arsip persuratan</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailContent">
                <div class="text-center py-4">
                    <div class="spinner-border" style="color: var(--simba-navy);" role="status"></div>
                </div>
            </div>
            <div class="modal-footer-figma">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.84rem;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Surat Masuk -->
<div class="modal fade" id="modalEditSuratMasuk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form id="formEditSurat" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header-figma">
                    <div>
                        <h5 class="modal-title-figma">Edit Surat Masuk</h5>
                        <p class="modal-subtitle-figma">Perbarui informasi arsip persuratan dinas masuk</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nomor Agenda / Surat <span class="text-danger">*</span></label>
                            <input type="text" name="no_surat" id="edit_no_surat" class="form-control form-control-figma" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Klasifikasi Surat <span class="text-danger">*</span></label>
                            <select name="id_surat" id="edit_id_surat" class="form-select form-control-figma" required>
                                @foreach($klasifikasiSurat as $klas)
                                    <option value="{{ $klas->id_surat }}">{{ $klas->nama_surat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Pengirim (Asal Surat) <span class="text-danger">*</span></label>
                            <input type="text" name="asal_surat" id="edit_asal_surat" class="form-control form-control-figma" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_masuk" id="edit_tgl_masuk" class="form-control form-control-figma" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-figma">Perihal <span class="text-danger">*</span></label>
                            <textarea name="perihal" id="edit_perihal" class="form-control form-control-figma" rows="3" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label-figma">Ganti Berkas Lampiran (Opsional)</label>
                            <label class="upload-dropzone-figma d-block mb-0">
                                <i class="bi bi-cloud-arrow-up-fill fs-2" style="color: var(--simba-navy);"></i>
                                <div class="fw-bold mt-1" style="font-size: 0.85rem; color: var(--simba-navy);">Klik untuk mengganti berkas</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Biarkan kosong jika tidak ingin mengubah berkas yang ada</div>
                                <input type="file" name="upload_dokumen" class="d-none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="previewUploadName(this, 'edit_file_label')">
                            </label>
                            <div id="edit_file_label" class="mt-1 small text-primary fw-medium"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-figma">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.84rem;">Batal</button>
                    <button type="submit" class="btn-simba-primary" style="border-radius: 8px; padding: 0.65rem 1.75rem;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Surat Masuk (Sesuai Desain Figma Red Badge) -->
<div class="modal fade" id="modalDeleteSuratMasuk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content modal-content-figma text-center p-4">
            <form id="formDeleteSurat" method="POST">
                @csrf
                @method('DELETE')
                <div class="delete-icon-wrapper mx-auto">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--simba-navy); font-size: 1.15rem;">Hapus Surat?</h5>
                <p class="text-muted small mb-3">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="fw-bold fs-6 mb-4 font-monospace text-dark" id="deleteSuratNumber">-</div>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.84rem;">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 fw-semibold" style="border-radius: 8px; font-size: 0.84rem; background-color: #EF4444; border: none;">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewUploadName(input, labelId) {
        if (input.files && input.files[0]) {
            document.getElementById(labelId).innerText = "Berkas terpilih: " + input.files[0].name;
        }
    }

    function showDetail(id) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetailSuratMasuk'));
        modal.show();

        fetch(`/admin/surat-masuk/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const data = res.data;
            let downloadBtn = data.upload_dokumen 
                ? `<a href="/admin/surat-masuk/${data.id_surat_masuk}/download" class="btn-simba-primary" style="padding: 0.45rem 1.25rem; font-size: 0.78rem;"><i class="bi bi-download me-1"></i> Unduh Berkas Digital</a>`
                : `<span class="badge bg-secondary">Berkas digital belum diunggah</span>`;

            document.getElementById('detailContent').innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Nomor Surat:</div>
                        <div class="fw-bold text-dark fs-6 font-monospace">${data.no_surat}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Klasifikasi Surat:</div>
                        <span class="badge-simba badge-simba-info">${data.surat ? data.surat.nama_surat : '-'}</span>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Pengirim (Asal Surat):</div>
                        <div class="fw-semibold text-dark">${data.asal_surat}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Tanggal Masuk:</div>
                        <div class="fw-semibold text-dark">${res.formatted_date}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Perihal:</div>
                        <div class="p-3 bg-light rounded-3 mt-1 text-dark" style="font-size: 0.88rem;">${data.perihal}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Berkas Lampiran Digital:</div>
                        <div class="mt-2">${downloadBtn}</div>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            document.getElementById('detailContent').innerHTML = `<div class="alert alert-danger">Gagal memuat rincian surat.</div>`;
        });
    }

    function showEdit(id) {
        fetch(`/admin/surat-masuk/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const data = res.data;
            document.getElementById('formEditSurat').action = `/admin/surat-masuk/${data.id_surat_masuk}`;
            document.getElementById('edit_id_surat').value = data.id_surat;
            document.getElementById('edit_no_surat').value = data.no_surat;
            document.getElementById('edit_asal_surat').value = data.asal_surat;
            document.getElementById('edit_tgl_masuk').value = data.tgl_masuk.substring(0, 10);
            document.getElementById('edit_perihal').value = data.perihal;
            document.getElementById('edit_file_label').innerText = data.upload_dokumen ? "Berkas saat ini tersimpan di sistem." : "";
            
            const modal = new bootstrap.Modal(document.getElementById('modalEditSuratMasuk'));
            modal.show();
        });
    }

    function confirmDelete(id, noSurat) {
        document.getElementById('formDeleteSurat').action = `/admin/surat-masuk/${id}`;
        document.getElementById('deleteSuratNumber').innerText = noSurat;
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteSuratMasuk'));
        modal.show();
    }
</script>
@endsection
