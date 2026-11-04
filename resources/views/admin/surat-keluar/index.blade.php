@extends('layouts.app')

@section('title', 'Surat Keluar - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row mb-4">
        <div>
            <h2 class="page-title-main">Surat Keluar</h2>
            <p class="page-subtitle-main">Kelola dan catat surat yang keluar.</p>
        </div>
    </div>

    <!-- Standalone Filter & Action Bar (Di Luar Card Tabel Sesuai Figma) -->
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
        <form action="{{ route('admin.surat-keluar.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
            <div style="width: 250px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 50px 0 0 50px; border-color: var(--simba-border); padding-left: 1.15rem;">
                        <img src="{{ asset('assets/images/cari.svg') }}" alt="Cari" style="width: 15px; height: 15px; object-fit: contain;">
                    </span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0 filter-pill-input" placeholder="Telusuri" value="{{ request('search') }}" style="border-radius: 0 50px 50px 0 !important; padding-left: 0.35rem;">
                </div>
            </div>

            <div style="width: 170px;">
                <input type="date" name="tgl_keluar" class="form-control form-control-sm filter-pill-input" value="{{ request('tgl_keluar') }}" placeholder="DD/MM/YY" onchange="this.form.submit()">
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

            @if(request()->hasAny(['search', 'tgl_keluar', 'id_surat']))
                <a href="{{ route('admin.surat-keluar.index') }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 50px; font-weight: 500;">
                    Reset
                </a>
            @endif
        </form>

        <div>
            <button class="btn-simba-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSuratKeluar">
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
                        <th>Tujuan</th>
                        <th>Perihal</th>
                        <th>Jenis Surat</th>
                        <th style="width: 130px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratKeluarList as $index => $sk)
                        <tr>
                            <td>{{ $suratKeluarList->firstItem() + $index }}</td>
                            <td class="fw-medium">{{ $sk->no_surat }}</td>
                            <td>{{ $sk->tgl_keluar->format('d/m/Y') }}</td>
                            <td style="white-space: normal; line-height: 1.45; min-width: 160px;">{{ $sk->tujuan_surat }}</td>
                            <td style="white-space: normal; line-height: 1.45; min-width: 200px;">{{ $sk->perihal }}</td>
                            <td>{{ $sk->surat->nama_surat ?? 'Surat Dinas' }}</td>
                            <td style="text-align: center;">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <button class="btn-action-square" title="Detail Surat" onclick="showDetailSuratKeluar({{ $sk->id_surat_keluar }})">
                                        <img src="{{ asset('assets/images/detail.svg') }}" alt="Detail">
                                    </button>
                                    <button class="btn-action-square" title="Edit Surat" onclick="showEditSuratKeluar({{ $sk->id_surat_keluar }})">
                                        <img src="{{ asset('assets/images/edit.svg') }}" alt="Edit">
                                    </button>
                                    <button class="btn-action-square btn-delete" title="Hapus Surat" onclick="confirmDeleteSuratKeluar({{ $sk->id_surat_keluar }}, '{{ addslashes($sk->no_surat) }}')">
                                        <img src="{{ asset('assets/images/hapus.svg') }}" alt="Hapus">
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Tidak ada data surat keluar yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suratKeluarList->hasPages() || $suratKeluarList->total() > 0)
            <div class="p-3 px-4 border-top d-flex justify-content-between align-items-center" style="font-size: 0.84rem; color: #64748B;">
                <span>
                    @if($suratKeluarList->total() > 0 && $suratKeluarList->total() <= $suratKeluarList->perPage())
                        Menampilkan {{ $suratKeluarList->total() }} dari {{ $suratKeluarList->total() }} surat
                    @else
                        Menampilkan {{ $suratKeluarList->firstItem() ?? 0 }} sampai {{ $suratKeluarList->lastItem() ?? 0 }} dari {{ $suratKeluarList->total() }} surat
                    @endif
                </span>
                <div>
                    {{ $suratKeluarList->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Surat Keluar (Sesuai Tambah Surat Keluar.svg) -->
<div class="modal fade" id="modalTambahSuratKeluar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form action="{{ route('admin.surat-keluar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header-figma">
                    <div>
                        <h4 class="modal-title-figma">Tambah Surat Keluar</h4>
                        <p class="modal-subtitle-figma">Lengkapi data surat yang akan dikirim.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label-figma">Jenis Surat <span class="text-danger">*</span></label>
                            <select name="id_surat" class="form-select form-control-figma" required>
                                @foreach($klasifikasiSurat as $klas)
                                    <option value="{{ $klas->id_surat }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $klas->nama_surat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_keluar" class="form-control form-control-figma" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" name="no_surat" class="form-control form-control-figma" placeholder="Contoh: 089/BPS.BJM/VIII/2026" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Perihal <span class="text-danger">*</span></label>
                            <input type="text" name="perihal" class="form-control form-control-figma" placeholder="Masukkan perihal surat" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Tujuan <span class="text-danger">*</span></label>
                            <input type="text" name="tujuan_surat" class="form-control form-control-figma" placeholder="Contoh: Kejaksaan Negeri Banjarmasin" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">File Surat</label>
                            <div class="upload-dropzone-figma" onclick="document.getElementById('upload_sk_add').click()">
                                <img src="{{ asset('assets/images/unggah.svg') }}" alt="Unggah" style="width: 44px; height: 44px; object-fit: contain; margin-bottom: 0.5rem;">
                                <span class="small text-muted d-block" id="sk_add_filename">Unggah file PDF/Word</span>
                                <input type="file" name="upload_dokumen" id="upload_sk_add" class="d-none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.svg" onchange="updateFilename(this, 'sk_add_filename')">
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

<!-- Modal Detail Surat Keluar (Sesuai Detail Surat Keluar.svg) -->
<div class="modal fade" id="modalDetailSuratKeluar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <div class="modal-header-figma">
                <div>
                    <h4 class="modal-title-figma">Detail Surat Keluar</h4>
                    <p class="modal-subtitle-figma" id="detail_sk_no_title">-</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailSuratKeluarContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer-figma justify-content-between">
                <div>
                    <button type="button" class="btn btn-outline-danger px-4" id="detail_sk_btn_delete" style="border-radius: 8px;">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-figma-cancel" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-figma-save" id="detail_sk_btn_edit">
                        <i class="bi bi-pencil-square me-1"></i> Edit Surat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Surat Keluar (Sesuai Edit Surat Keluar.svg) -->
<div class="modal fade" id="modalEditSuratKeluar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form id="formEditSuratKeluar" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header-figma">
                    <div>
                        <h4 class="modal-title-figma">Edit Surat Keluar</h4>
                        <p class="modal-subtitle-figma">Lengkapi data surat yang akan dikirim.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label-figma">Jenis Surat <span class="text-danger">*</span></label>
                            <select name="id_surat" id="edit_sk_id_surat" class="form-select form-control-figma" required>
                                @foreach($klasifikasiSurat as $klas)
                                    <option value="{{ $klas->id_surat }}">{{ $klas->nama_surat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_keluar" id="edit_sk_tgl_keluar" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" name="no_surat" id="edit_sk_no_surat" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Perihal <span class="text-danger">*</span></label>
                            <input type="text" name="perihal" id="edit_sk_perihal" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Tujuan <span class="text-danger">*</span></label>
                            <input type="text" name="tujuan_surat" id="edit_sk_tujuan" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">File Surat</label>
                            <div class="upload-dropzone-figma" onclick="document.getElementById('upload_sk_edit').click()">
                                <img src="{{ asset('assets/images/unggah.svg') }}" alt="Unggah" style="width: 44px; height: 44px; object-fit: contain; margin-bottom: 0.5rem;">
                                <span class="small text-muted d-block" id="sk_edit_filename">Unggah file PDF/Word</span>
                                <input type="file" name="upload_dokumen" id="upload_sk_edit" class="d-none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.svg" onchange="updateFilename(this, 'sk_edit_filename')">
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

<!-- Modal Hapus Surat Keluar -->
<div class="modal fade" id="modalDeleteSuratKeluar" tabindex="-1" aria-hidden="true">
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
                <form id="formDeleteSuratKeluar" method="POST" class="d-flex justify-content-center gap-3">
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

    function showDetailSuratKeluar(id) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetailSuratKeluar'));
        modal.show();

        fetch(`/admin/surat-keluar/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const data = res.data;
            document.getElementById('detail_sk_no_title').innerText = data.no_surat;
            document.getElementById('detailSuratKeluarContent').innerHTML = `
                <div class="row g-4 text-start">
                    <div class="col-12">
                        <div class="form-label-figma">Jenis Surat *</div>
                        <div class="fw-medium text-dark">${data.surat ? data.surat.nama_surat : 'Surat Dinas'}</div>
                    </div>
                    <div class="col-12">
                        <div class="form-label-figma">Tanggal Surat *</div>
                        <div class="fw-medium text-dark">${data.tgl_keluar ? data.tgl_keluar.substring(0, 10) : '-'}</div>
                    </div>
                    <div class="col-12">
                        <div class="form-label-figma">Nomor Surat *</div>
                        <div class="fw-medium text-dark">${data.no_surat}</div>
                    </div>
                    <div class="col-12">
                        <div class="form-label-figma">Perihal *</div>
                        <div class="fw-medium text-dark">${data.perihal}</div>
                    </div>
                    <div class="col-12">
                        <div class="form-label-figma">Tujuan *</div>
                        <div class="fw-medium text-dark">${data.tujuan_surat}</div>
                    </div>
                </div>
            `;

            document.getElementById('detail_sk_btn_delete').onclick = function() {
                modal.hide();
                confirmDeleteSuratKeluar(data.id_surat_keluar, data.no_surat);
            };

            document.getElementById('detail_sk_btn_edit').onclick = function() {
                modal.hide();
                showEditSuratKeluar(data.id_surat_keluar);
            };
        });
    }

    function showEditSuratKeluar(id) {
        fetch(`/admin/surat-keluar/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const data = res.data;
            document.getElementById('formEditSuratKeluar').action = `/admin/surat-keluar/${data.id_surat_keluar}`;
            document.getElementById('edit_sk_id_surat').value = data.id_surat;
            document.getElementById('edit_sk_tgl_keluar').value = data.tgl_keluar ? data.tgl_keluar.substring(0, 10) : '';
            document.getElementById('edit_sk_no_surat').value = data.no_surat;
            document.getElementById('edit_sk_perihal').value = data.perihal;
            document.getElementById('edit_sk_tujuan').value = data.tujuan_surat;

            const modal = new bootstrap.Modal(document.getElementById('modalEditSuratKeluar'));
            modal.show();
        });
    }

    function confirmDeleteSuratKeluar(id, noSurat) {
        document.getElementById('formDeleteSuratKeluar').action = `/admin/surat-keluar/${id}`;
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteSuratKeluar'));
        modal.show();
    }
</script>
@endsection
