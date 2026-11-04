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

            @if(request()->has('search'))
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
                        <th style="width: 50px;">No</th>
                        <th>Nomor Registrasi</th>
                        <th>Nama Klien</th>
                        <th>Nama Pembimbing</th>
                        <th>Telepon</th>
                        <th style="width: 130px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($klienList as $index => $klien)
                        <tr>
                            <td>{{ $klienList->firstItem() + $index }}</td>
                            <td class="fw-medium">{{ $klien->no_register }}</td>
                            <td class="fw-medium">{{ $klien->nama_klien }}</td>
                            <td>{{ Auth::user()->nama_pegawai ?? 'Budi Santoso, S.Sos.' }}</td>
                            <td>{{ $klien->telepon ?? '0812-3456-7890' }}</td>
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
                            <td colspan="6" class="text-center py-5 text-muted">
                                Tidak ada data klien yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($klienList->hasPages() || $klienList->total() > 0)
            <div class="p-3 px-4 border-top d-flex justify-content-between align-items-center" style="font-size: 0.84rem; color: #64748B;">
                <span>Menampilkan {{ $klienList->firstItem() ?? 0 }} sampai {{ $klienList->lastItem() ?? 0 }} dari {{ $klienList->total() }} klien</span>
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
                            <label class="form-label-figma">Nama Klien <span class="text-danger">*</span></label>
                            <input type="text" name="nama_klien" class="form-control form-control-figma" placeholder="Nama lengkap klien" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nomor Registrasi <span class="text-danger">*</span></label>
                            <input type="text" name="no_register" class="form-control form-control-figma" placeholder="Contoh : BJM-2026-0146" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="telepon" class="form-control form-control-figma" placeholder="08XX-XXXX-XXXX" value="0812-3456-7890">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jenis Tidak Pidana <span class="text-danger">*</span></label>
                            <input type="text" name="tindak_pidana" class="form-control form-control-figma" placeholder="Catatan" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control form-control-figma" rows="3" placeholder="Alamat tempat tinggal klien"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Keterangan</label>
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
                <button type="button" class="btn btn-figma-save" id="detail_klien_btn_edit">Simpan</button>
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
                        <p class="modal-subtitle-figma">Isi data klien sesuai informasi yang ada.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nama Klien <span class="text-danger">*</span></label>
                            <input type="text" name="nama_klien" id="edit_nama_klien" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nomor Registrasi <span class="text-danger">*</span></label>
                            <input type="text" name="no_register" id="edit_no_register" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="telepon" id="edit_telepon" class="form-control form-control-figma">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jenis Tidak Pidana <span class="text-danger">*</span></label>
                            <input type="text" name="tindak_pidana" id="edit_tindak_pidana" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea name="alamat" id="edit_alamat" class="form-control form-control-figma" rows="3"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Keterangan</label>
                            <textarea name="keterangan" id="edit_keterangan" class="form-control form-control-figma" rows="2"></textarea>
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
    function showDetailKlien(id) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetailKlien'));
        modal.show();

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
                        <div class="form-label-figma">Nama Klien *</div>
                        <div class="fw-medium text-dark">${data.nama_klien}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Nomor Registrasi *</div>
                        <div class="fw-medium text-dark">${data.no_register}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Telepon *</div>
                        <div class="fw-medium text-dark">${data.telepon || '0812-5000-1141'}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Keterangan</div>
                        <div class="fw-medium text-dark">${data.keterangan || 'Klien dewasa bimbingan rutin.'}</div>
                    </div>
                    <div class="col-12">
                        <div class="form-label-figma">Alamat Lengkap *</div>
                        <div class="fw-medium text-dark">${data.alamat || 'Jl. Kelayan B No. 12, Banjarmasin Selatan.'}</div>
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
            document.getElementById('edit_nama_klien').value = data.nama_klien;
            document.getElementById('edit_no_register').value = data.no_register;
            document.getElementById('edit_telepon').value = data.telepon || '0812-5000-1141';
            document.getElementById('edit_tindak_pidana').value = data.tindak_pidana;
            document.getElementById('edit_alamat').value = data.alamat || 'Jl. Kelayan B No. 12, Banjarmasin Selatan.';
            document.getElementById('edit_keterangan').value = data.keterangan || 'Bimbingan rutin';

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
