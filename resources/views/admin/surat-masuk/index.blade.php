@extends('layouts.app')

@section('title', 'Surat Masuk - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row mb-4">
        <div>
            <h2 class="page-title-main">Surat Masuk</h2>
            <p class="page-subtitle-main">Kelola dan catat surat yang diterima.</p>
        </div>
    </div>

    <!-- Standalone Filter & Action Bar (Di Luar Card Tabel Sesuai Figma) -->
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
        <form action="{{ route('admin.surat-masuk.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
            <div style="width: 250px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 50px 0 0 50px; border-color: var(--simba-border); padding-left: 1.15rem;">
                        <img src="{{ asset('assets/images/cari.svg') }}" alt="Cari" style="width: 15px; height: 15px; object-fit: contain;">
                    </span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0 filter-pill-input" placeholder="Telusuri" value="{{ request('search') }}" style="border-radius: 0 50px 50px 0 !important; padding-left: 0.35rem;">
                </div>
            </div>

            <div style="width: 170px;">
                <input type="date" name="tgl_masuk" class="form-control form-control-sm filter-pill-input" value="{{ request('tgl_masuk') }}" placeholder="DD/MM/YY" onchange="this.form.submit()">
            </div>

            <div style="width: 180px;">
                <select name="id_surat" class="form-select form-select-sm filter-pill-input" onchange="this.form.submit()">
                    <option value="">Filter</option>
                    @foreach($klasifikasiSurat as $klas)
                        <option value="{{ $klas->id_surat }}" {{ request('id_surat') == $klas->id_surat ? 'selected' : '' }}>
                            {{ $klas->nama_surat }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(request()->hasAny(['search', 'tgl_masuk', 'id_surat']))
                <a href="{{ route('admin.surat-masuk.index') }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 50px; font-weight: 500;">
                    Reset
                </a>
            @endif
        </form>

        <div>
            <button class="btn-simba-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSuratMasuk">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Surat</span>
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card-figma">
        <div class="table-responsive">
            <table class="table-simba">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nomor Surat</th>
                        <th>Tgl Surat</th>
                        <th>Tgl Diterima</th>
                        <th>Tujuan</th>
                        <th>Asal Surat</th>
                        <th>Perihal</th>
                        <th style="width: 130px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratMasukList as $index => $sm)
                        <tr>
                            <td>{{ $suratMasukList->firstItem() + $index }}</td>
                            <td class="fw-medium">{{ $sm->no_surat }}</td>
                            <td>{{ $sm->tgl_masuk->format('d/m/Y') }}</td>
                            <td>{{ $sm->tgl_diterima ? $sm->tgl_diterima->format('d/m/Y') : $sm->tgl_masuk->format('d/m/Y') }}</td>
                            <td style="white-space: normal; line-height: 1.45; min-width: 160px;">{{ $sm->tujuan ?? ($sm->surat->nama_surat ?? 'Bimbingan Konseling') }}</td>
                            <td style="white-space: normal; line-height: 1.45; min-width: 160px;">{{ $sm->asal_surat }}</td>
                            <td style="white-space: normal; line-height: 1.45; min-width: 200px;">{{ $sm->perihal }}</td>
                            <td style="text-align: center;">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <button class="btn-action-square" title="Detail Surat" onclick="showDetailSurat({{ $sm->id_surat_masuk }})">
                                        <img src="{{ asset('assets/images/detail.svg') }}" alt="Detail">
                                    </button>
                                    <button class="btn-action-square" title="Edit Surat" onclick="showEditSurat({{ $sm->id_surat_masuk }})">
                                        <img src="{{ asset('assets/images/edit.svg') }}" alt="Edit">
                                    </button>
                                    <button class="btn-action-square btn-delete" title="Hapus Surat" onclick="confirmDeleteSurat({{ $sm->id_surat_masuk }}, '{{ addslashes($sm->no_surat) }}')">
                                        <img src="{{ asset('assets/images/hapus.svg') }}" alt="Hapus">
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

        @if($suratMasukList->hasPages() || $suratMasukList->total() > 0)
            <div class="p-3 px-4 border-top d-flex justify-content-between align-items-center" style="font-size: 0.84rem; color: #64748B;">
                <span>
                    @if($suratMasukList->total() > 0 && $suratMasukList->total() <= $suratMasukList->perPage())
                        Menampilkan {{ $suratMasukList->total() }} dari {{ $suratMasukList->total() }} surat
                    @else
                        Menampilkan {{ $suratMasukList->firstItem() ?? 0 }} sampai {{ $suratMasukList->lastItem() ?? 0 }} dari {{ $suratMasukList->total() }} surat
                    @endif
                </span>
                <div>
                    {{ $suratMasukList->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Surat Masuk (Sesuai Tambah Surat Masuk.svg) -->
<div class="modal fade" id="modalTambahSuratMasuk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form action="{{ route('admin.surat-masuk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header-figma">
                    <div>
                        <h4 class="modal-title-figma">Tambah Surat Masuk</h4>
                        <p class="modal-subtitle-figma">Lengkapi data surat yang diterima.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label-figma">Asal Surat <span class="text-danger">*</span></label>
                            <input type="text" name="asal_surat" class="form-control form-control-figma" placeholder="Contoh: Kejaksaan Negeri Banjarmasin" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tanggal Masuk Surat <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_masuk" class="form-control form-control-figma" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tanggal Surat Diterima <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_diterima" class="form-control form-control-figma" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" name="no_surat" class="form-control form-control-figma" placeholder="Masukkan nomor surat" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tujuan <span class="text-danger">*</span></label>
                            <input type="text" name="tujuan" class="form-control form-control-figma" placeholder="Pilih tujuan / Bimbingan Konseling Dewasa" value="Bimbingan Konseling Dewasa">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Perihal <span class="text-danger">*</span></label>
                            <input type="text" name="perihal" class="form-control form-control-figma" placeholder="Masukkan perihal" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Lampiran</label>
                            <input type="text" name="lampiran" class="form-control form-control-figma" placeholder="1 Berkas / Salinan">
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control form-control-figma" placeholder="Catatan tambahan (opsional)">
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">File Surat</label>
                            <div class="upload-dropzone-figma" onclick="document.getElementById('upload_sm_add').click()">
                                <img src="{{ asset('assets/images/unggah.svg') }}" alt="Unggah" style="width: 44px; height: 44px; object-fit: contain; margin-bottom: 0.5rem;">
                                <span class="small text-muted d-block" id="sm_add_filename">Unggah file PDF/Word</span>
                                <input type="file" name="upload_dokumen" id="upload_sm_add" class="d-none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.svg" onchange="updateFilename(this, 'sm_add_filename')">
                            </div>
                        </div>
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

<!-- Modal Detail Surat Masuk (Sesuai Detail Surat Masuk.svg) -->
<div class="modal fade" id="modalDetailSuratMasuk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <div class="modal-header-figma">
                <div>
                    <h4 class="modal-title-figma">Detail Surat Masuk</h4>
                    <p class="modal-subtitle-figma" id="detail_no_surat_title">-</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailSuratContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer-figma justify-content-between">
                <div>
                    <button type="button" class="btn btn-outline-danger px-4" id="detail_btn_delete" style="border-radius: 8px;">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-figma-cancel" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-figma-save" id="detail_btn_edit">
                        <i class="bi bi-pencil-square me-1"></i> Edit Surat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Surat Masuk (Sesuai Edit Surat Masuk.svg) -->
<div class="modal fade" id="modalEditSuratMasuk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form id="formEditSuratMasuk" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header-figma">
                    <div>
                        <h4 class="modal-title-figma">Edit Surat Masuk</h4>
                        <p class="modal-subtitle-figma">Lengkapi data surat yang diterima.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label-figma">Asal Surat <span class="text-danger">*</span></label>
                            <input type="text" name="asal_surat" id="edit_asal_surat" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tanggal Masuk Surat <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_masuk" id="edit_tgl_masuk" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tanggal Surat Diterima <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_diterima" id="edit_tgl_diterima" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" name="no_surat" id="edit_no_surat" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tujuan <span class="text-danger">*</span></label>
                            <input type="text" name="tujuan" id="edit_tujuan" class="form-control form-control-figma">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Perihal <span class="text-danger">*</span></label>
                            <input type="text" name="perihal" id="edit_perihal" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Lampiran</label>
                            <input type="text" name="lampiran" id="edit_lampiran" class="form-control form-control-figma">
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Keterangan</label>
                            <input type="text" name="keterangan" id="edit_keterangan" class="form-control form-control-figma">
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">File Surat</label>
                            <div class="upload-dropzone-figma" onclick="document.getElementById('upload_sm_edit').click()">
                                <img src="{{ asset('assets/images/unggah.svg') }}" alt="Unggah" style="width: 44px; height: 44px; object-fit: contain; margin-bottom: 0.5rem;">
                                <span class="small text-muted d-block" id="sm_edit_filename">Unggah file PDF/Word</span>
                                <input type="file" name="upload_dokumen" id="upload_sm_edit" class="d-none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.svg" onchange="updateFilename(this, 'sm_edit_filename')">
                            </div>
                        </div>
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

<!-- Modal Hapus Surat Masuk (Sesuai Hapus Surat Masuk.svg) -->
<div class="modal fade" id="modalDeleteSuratMasuk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content modal-content-figma text-center p-4">
            <div class="modal-body p-2">
                <div class="delete-dialog-badge">
                    <img src="{{ asset('assets/images/warning.svg') }}" alt="Peringatan">
                </div>
                <h4 class="fw-bold mb-2" style="color: #102A45; font-size: 1.5rem;">Hapus Surat?</h4>
                <p class="text-dark small mb-4" style="line-height: 1.5;">
                    Surat akan dihapus permanen dari sistem.<br>Tindakan ini tidak dapat dibatalkan.
                </p>
                <form id="formDeleteSuratMasuk" method="POST" class="d-flex justify-content-center gap-3">
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

    function showDetailSurat(id) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetailSuratMasuk'));
        modal.show();

        fetch(`/admin/surat-masuk/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const data = res.data;
            document.getElementById('detail_no_surat_title').innerText = data.no_surat;
            document.getElementById('detailSuratContent').innerHTML = `
                <div class="row g-4 text-start">
                    <div class="col-12">
                        <div class="form-label-figma">Asal Surat *</div>
                        <div class="fw-medium text-dark">${data.asal_surat}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Tanggal Masuk Surat *</div>
                        <div class="fw-medium text-dark">${data.tgl_masuk ? data.tgl_masuk.substring(0, 10) : '-'}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Tanggal Surat Diterima *</div>
                        <div class="fw-medium text-dark">${data.tgl_diterima ? data.tgl_diterima.substring(0, 10) : (data.tgl_masuk ? data.tgl_masuk.substring(0, 10) : '-')}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Nomor Surat *</div>
                        <div class="fw-medium text-dark">${data.no_surat}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Tujuan *</div>
                        <div class="fw-medium text-dark">${data.tujuan || 'Bimbingan Konseling Anak'}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Perihal *</div>
                        <div class="fw-medium text-dark">${data.perihal}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Lampiran</div>
                        <div class="fw-medium text-dark">${data.lampiran || '-'}</div>
                    </div>
                </div>
            `;

            document.getElementById('detail_btn_delete').onclick = function() {
                modal.hide();
                confirmDeleteSurat(data.id_surat_masuk, data.no_surat);
            };

            document.getElementById('detail_btn_edit').onclick = function() {
                modal.hide();
                showEditSurat(data.id_surat_masuk);
            };
        });
    }

    function showEditSurat(id) {
        fetch(`/admin/surat-masuk/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const data = res.data;
            document.getElementById('formEditSuratMasuk').action = `/admin/surat-masuk/${data.id_surat_masuk}`;
            document.getElementById('edit_asal_surat').value = data.asal_surat;
            document.getElementById('edit_tgl_masuk').value = data.tgl_masuk ? data.tgl_masuk.substring(0, 10) : '';
            document.getElementById('edit_tgl_diterima').value = data.tgl_diterima ? data.tgl_diterima.substring(0, 10) : (data.tgl_masuk ? data.tgl_masuk.substring(0, 10) : '');
            document.getElementById('edit_no_surat').value = data.no_surat;
            document.getElementById('edit_tujuan').value = data.tujuan || 'Bimbingan Konseling Dewasa';
            document.getElementById('edit_perihal').value = data.perihal;
            document.getElementById('edit_lampiran').value = data.lampiran || '';
            document.getElementById('edit_keterangan').value = data.keterangan || '';

            const modal = new bootstrap.Modal(document.getElementById('modalEditSuratMasuk'));
            modal.show();
        });
    }

    function confirmDeleteSurat(id, noSurat) {
        document.getElementById('formDeleteSuratMasuk').action = `/admin/surat-masuk/${id}`;
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteSuratMasuk'));
        modal.show();
    }
</script>
@endsection
