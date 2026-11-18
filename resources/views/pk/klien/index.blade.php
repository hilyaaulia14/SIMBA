@extends('layouts.app')

@section('title', 'Data Klien - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row mb-4">
        <div>
            <h2 class="page-title-main">Data Klien</h2>
            <p class="page-subtitle-main">Input data klien sesuai informasi yang ada.</p>
        </div>
    </div>

    <!-- Standalone Filter & Action Bar (Di Luar Card Tabel Sesuai Figma) -->
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
        <form action="{{ route('pk.klien.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-grow-1">
            <div style="max-width: 280px; width: 100%;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 50px 0 0 50px; border-color: var(--simba-border); padding-left: 1.15rem;">
                        <img src="{{ asset('assets/images/cari.svg') }}" alt="Cari" style="width: 15px; height: 15px; object-fit: contain;">
                    </span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0 filter-pill-input" placeholder="Telusuri" value="{{ request('search') }}" style="border-radius: 0 50px 50px 0 !important; padding-left: 0.35rem;">
                </div>
            </div>

            @if(request()->filled('search'))
                <a href="{{ route('pk.klien.index') }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 50px; font-weight: 500;">
                    Reset
                </a>
            @endif
        </form>

        <div>
            <button class="btn-simba-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKlien">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Klien</span>
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card-figma">
        <div class="table-responsive">
            <table class="table-simba">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 17%;">Nomor Registrasi</th>
                        <th style="width: 18%;">Nama Klien</th>
                        <th style="width: 18%;">Asal UPT</th>
                        <th style="width: 15%;">Tindak Pidana</th>
                        <th style="width: 13%; text-align: center;">Jenis Bimbingan</th>
                        <th style="width: 8%; text-align: center;">Jenis Kelamin</th>
                        <th style="width: 6%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($klienList as $index => $klien)
                        <tr>
                            <td style="text-align: center;">{{ $klienList->firstItem() + $index }}</td>
                            <td class="fw-semibold text-dark">{{ $klien->no_register }}</td>
                            <td>{{ $klien->nama_klien }}</td>
                            <td>{{ $klien->asal_upt ?? '-' }}</td>
                            <td>{{ $klien->tindak_pidana ?? '-' }}</td>
                            <td style="text-align: center;">
                                @if($klien->jenis_bimbingan == 'Pembebasan Bersyarat (PB)')
                                    <span class="badge" style="background-color: #E0F2FE; color: #0369A1; font-weight: 500; font-size: 0.78rem; border-radius: 50px; padding: 0.35rem 0.65rem;">PB</span>
                                @elseif($klien->jenis_bimbingan == 'Cuti Bersyarat (CB)')
                                    <span class="badge" style="background-color: #FEF3C7; color: #92400E; font-weight: 500; font-size: 0.78rem; border-radius: 50px; padding: 0.35rem 0.65rem;">CB</span>
                                @else
                                    <span class="text-muted small">{{ $klien->jenis_bimbingan ?? '-' }}</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="small text-muted">{{ $klien->jenis_kelamin ?? '-' }}</span>
                            </td>
                            <td style="text-align: center;">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <button class="btn-action-square" title="Detail Klien" onclick="showDetailKlien({{ $klien->id_klien }})">
                                        <img src="{{ asset('assets/images/detail.svg') }}" alt="Detail">
                                    </button>
                                    <button class="btn-action-square" title="Edit Klien" onclick="showEditKlien({{ $klien->id_klien }})">
                                        <img src="{{ asset('assets/images/edit.svg') }}" alt="Edit">
                                    </button>
                                    <button class="btn-action-square btn-delete" title="Hapus Klien" onclick="confirmDeleteKlien({{ $klien->id_klien }}, '{{ addslashes($klien->nama_klien) }}')">
                                        <img src="{{ asset('assets/images/hapus.svg') }}" alt="Hapus">
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Tidak ada data klien yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($klienList->hasPages() || $klienList->total() > 0)
            <div class="p-3 px-4 border-top d-flex justify-content-between align-items-center" style="font-size: 0.84rem; color: #64748B;">
                <span>
                    @if($klienList->total() > 0 && $klienList->total() <= $klienList->perPage())
                        Menampilkan {{ $klienList->total() }} dari {{ $klienList->total() }} klien
                    @else
                        Menampilkan {{ $klienList->firstItem() ?? 0 }} sampai {{ $klienList->lastItem() ?? 0 }} dari {{ $klienList->total() }} klien
                    @endif
                </span>
                <div>
                    {{ $klienList->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Klien (Sesuai Tambah Klien.svg) -->
<div class="modal fade" id="modalTambahKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form action="{{ route('pk.klien.store') }}" method="POST">
                @csrf
                <div class="modal-header-figma">
                    <div>
                        <h4 class="modal-title-figma">Tambah Klien</h4>
                        <p class="modal-subtitle-figma">Isi data klien sesuai informasi yang ada.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nomor Registrasi <span class="text-danger">*</span></label>
                            <input type="text" name="no_register" class="form-control form-control-figma" placeholder="Contoh : 1969/DW/XI/2026" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nama Klien <span class="text-danger">*</span></label>
                            <input type="text" name="nama_klien" class="form-control form-control-figma" placeholder="Nama lengkap klien" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Asal UPT <span class="text-danger">*</span></label>
                            <input type="text" name="asal_upt" class="form-control form-control-figma" placeholder="Contoh : Lapas Kelas IIA Banjarmasin" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tindak Pidana <span class="text-danger">*</span></label>
                            <input type="text" name="tindak_pidana" class="form-control form-control-figma" placeholder="Contoh : Narkotika / Pencurian" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jenis Bimbingan <span class="text-danger">*</span></label>
                            <select name="jenis_bimbingan" class="form-select form-control-figma" required>
                                <option value="" disabled selected>Pilih Jenis Bimbingan</option>
                                <option value="Pembebasan Bersyarat (PB)">Pembebasan Bersyarat (PB)</option>
                                <option value="Cuti Bersyarat (CB)">Cuti Bersyarat (CB)</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select form-control-figma" required>
                                <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control form-control-figma" rows="3" placeholder="Alamat tempat tinggal klien" required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Keterangan <span class="text-muted fw-normal" style="font-size: 0.85rem;">(Opsional)</span></label>
                            <textarea name="keterangan" class="form-control form-control-figma" rows="2" placeholder="Catatan opsional"></textarea>
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

<!-- Modal Detail Klien (Sesuai Detail Klien.svg) -->
<div class="modal fade" id="modalDetailKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <div class="modal-header-figma">
                <div>
                    <h4 class="modal-title-figma">Detail Klien</h4>
                    <p class="modal-subtitle-figma" id="detail_klien_nama_title">-</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailKlienContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer-figma">
                <button type="button" class="btn btn-figma-cancel" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-figma-save" id="detail_klien_btn_edit">Edit Klien</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Data Klien (Sesuai Edit Data Klien.svg) -->
<div class="modal fade" id="modalEditKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form id="formEditKlien" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header-figma">
                    <div>
                        <h4 class="modal-title-figma">Edit Data Klien</h4>
                        <p class="modal-subtitle-figma">Perbarui data klien sesuai informasi yang ada.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nomor Registrasi <span class="text-danger">*</span></label>
                            <input type="text" name="no_register" id="edit_no_register" class="form-control form-control-figma" placeholder="Contoh : 1969/DW/XI/2026" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nama Klien <span class="text-danger">*</span></label>
                            <input type="text" name="nama_klien" id="edit_nama_klien" class="form-control form-control-figma" placeholder="Nama lengkap klien" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Asal UPT <span class="text-danger">*</span></label>
                            <input type="text" name="asal_upt" id="edit_asal_upt" class="form-control form-control-figma" placeholder="Contoh : Lapas Kelas IIA Banjarmasin" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tindak Pidana <span class="text-danger">*</span></label>
                            <input type="text" name="tindak_pidana" id="edit_tindak_pidana" class="form-control form-control-figma" placeholder="Contoh : Narkotika / Pencurian" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jenis Bimbingan <span class="text-danger">*</span></label>
                            <select name="jenis_bimbingan" id="edit_jenis_bimbingan" class="form-select form-control-figma" required>
                                <option value="" disabled>Pilih Jenis Bimbingan</option>
                                <option value="Pembebasan Bersyarat (PB)">Pembebasan Bersyarat (PB)</option>
                                <option value="Cuti Bersyarat (CB)">Cuti Bersyarat (CB)</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" id="edit_jenis_kelamin" class="form-select form-control-figma" required>
                                <option value="" disabled>Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" id="edit_alamat" class="form-control form-control-figma" rows="3" placeholder="Alamat tempat tinggal klien" required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Keterangan <span class="text-muted fw-normal" style="font-size: 0.85rem;">(Opsional)</span></label>
                            <textarea name="keterangan" id="edit_keterangan" class="form-control form-control-figma" rows="2" placeholder="Catatan opsional"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-figma">
                    <button type="button" class="btn btn-figma-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-figma-save">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Klien (Sesuai Hapus Klien.svg) -->
<div class="modal fade" id="modalDeleteKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content modal-content-figma text-center p-4">
            <div class="modal-body p-2">
                <div class="delete-dialog-badge">
                    <img src="{{ asset('assets/images/warning.svg') }}" alt="Peringatan">
                </div>
                <h4 class="fw-bold mb-2" style="color: #102A45; font-size: 1.5rem;">Hapus Klien?</h4>
                <p class="text-dark small mb-4" style="line-height: 1.5;">
                    Klien akan dihapus permanen dari sistem.<br>Tindakan ini tidak dapat dibatalkan.
                </p>
                <form id="formDeleteKlien" method="POST" class="d-flex justify-content-center gap-3">
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
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function showDetailKlien(id) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetailKlien'));
        modal.show();

        document.getElementById('detail_klien_nama_title').innerText = 'Memuat data...';
        document.getElementById('detailKlienContent').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        `;

        fetch(`/pk/klien/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const data = res.klien;
            document.getElementById('detail_klien_nama_title').innerText = data.nama_klien;
            document.getElementById('detailKlienContent').innerHTML = `
                <div class="row g-4 text-start">
                    <div class="col-md-6">
                        <div class="form-label-figma text-muted small">Nomor Registrasi</div>
                        <div class="fw-semibold text-dark">${escapeHtml(data.no_register)}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma text-muted small">Nama Klien</div>
                        <div class="fw-semibold text-dark">${escapeHtml(data.nama_klien)}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma text-muted small">Asal UPT</div>
                        <div class="fw-medium text-dark">${escapeHtml(data.asal_upt || '-')}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma text-muted small">Tindak Pidana</div>
                        <div class="fw-medium text-dark">${escapeHtml(data.tindak_pidana || '-')}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma text-muted small">Jenis Bimbingan</div>
                        <div class="fw-medium text-dark">
                            <span class="badge ${data.jenis_bimbingan === 'Pembebasan Bersyarat (PB)' ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-dark'} px-2 py-1" style="border-radius: 50px;">
                                ${escapeHtml(data.jenis_bimbingan || '-')}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma text-muted small">Jenis Kelamin</div>
                        <div class="fw-medium text-dark">${escapeHtml(data.jenis_kelamin || '-')}</div>
                    </div>
                    <div class="col-12">
                        <div class="form-label-figma text-muted small">Alamat</div>
                        <div class="fw-medium text-dark">${escapeHtml(data.alamat || '-')}</div>
                    </div>
                    <div class="col-12">
                        <div class="form-label-figma text-muted small">Keterangan</div>
                        <div class="fw-medium text-dark">${escapeHtml(data.keterangan || '-')}</div>
                    </div>
                </div>
            `;

            document.getElementById('detail_klien_btn_edit').onclick = function() {
                modal.hide();
                showEditKlien(data.id_klien);
            };
        });
    }

    function showEditKlien(id) {
        fetch(`/pk/klien/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const data = res.klien;
            document.getElementById('formEditKlien').action = `/pk/klien/${data.id_klien}`;
            document.getElementById('edit_no_register').value = data.no_register || '';
            document.getElementById('edit_nama_klien').value = data.nama_klien || '';
            document.getElementById('edit_asal_upt').value = data.asal_upt || '';
            document.getElementById('edit_tindak_pidana').value = data.tindak_pidana || '';
            document.getElementById('edit_jenis_bimbingan').value = data.jenis_bimbingan || '';
            document.getElementById('edit_jenis_kelamin').value = data.jenis_kelamin || '';
            document.getElementById('edit_alamat').value = data.alamat || '';
            document.getElementById('edit_keterangan').value = data.keterangan || '';

            const modal = new bootstrap.Modal(document.getElementById('modalEditKlien'));
            modal.show();
        });
    }

    function confirmDeleteKlien(id, namaKlien) {
        document.getElementById('formDeleteKlien').action = `/pk/klien/${id}`;
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteKlien'));
        modal.show();
    }
</script>
@endsection
