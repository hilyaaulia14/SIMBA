@extends('layouts.app')

@section('title', 'Jadwal Bimbingan - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row">
        <div>
            <h3 class="page-title-main">Jadwal Bimbingan</h3>
            <p class="page-subtitle-main">Kelola agenda sesi bimbingan berkala klien pemasyarakatan</p>
        </div>
        <div>
            <button class="btn-simba-primary" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Jadwal</span>
            </button>
        </div>
    </div>

    <!-- Table Card with Filter Bar -->
    <div class="table-card-figma">
        <div class="table-filter-bar">
            <form action="{{ route('pk.jadwal.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
                <div style="max-width: 320px; width: 100%;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px; border-color: var(--simba-border);">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Cari nama atau no. register..." value="{{ request('search') }}" style="border-radius: 0 8px 8px 0; border-color: var(--simba-border);">
                    </div>
                </div>

                <div style="max-width: 220px; width: 100%;">
                    <select name="id_status_bimbingan" class="form-select form-select-sm" style="border-radius: 8px; border-color: var(--simba-border);" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        @foreach($allStatus as $st)
                            <option value="{{ $st->id_status_bimbingan }}" {{ request('id_status_bimbingan') == $st->id_status_bimbingan ? 'selected' : '' }}>
                                {{ $st->nama_status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="max-width: 180px; width: 100%;">
                    <input type="date" name="tgl_bimbingan" class="form-control form-control-sm" value="{{ request('tgl_bimbingan') }}" style="border-radius: 8px; border-color: var(--simba-border);" onchange="this.form.submit()">
                </div>

                @if(request()->hasAny(['search', 'id_status_bimbingan', 'tgl_bimbingan']))
                    <a href="{{ route('pk.jadwal.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
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
                        <th>No. Register</th>
                        <th>Nama Klien</th>
                        <th>Tanggal Bimbingan</th>
                        <th>Jam Mulai</th>
                        <th>Status</th>
                        <th>Petugas PK</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalList as $index => $jadwal)
                        @php
                            $stName = strtolower($jadwal->statusBimbingan->nama_status ?? '');
                            $badgeClass = match($stName) {
                                'terlaksana' => 'badge-simba-success',
                                'dijadwalkan' => 'badge-simba-info',
                                'absen' => 'badge-simba-danger',
                                default => 'badge-simba-warning'
                            };
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $jadwalList->firstItem() + $index }}</td>
                            <td>
                                <span class="font-monospace text-dark fw-bold">{{ $jadwal->klien->no_register ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $jadwal->klien->nama_klien ?? 'Klien Dihapus' }}</span>
                            </td>
                            <td>{{ $jadwal->tgl_bimbingan->format('d/m/Y') }}</td>
                            <td>{{ substr($jadwal->jam_mulai, 0, 5) }} WITA</td>
                            <td>
                                <span class="badge-simba {{ $badgeClass }}">
                                    {{ $jadwal->statusBimbingan->nama_status ?? 'Dijadwalkan' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $jadwal->pegawai->nama_pegawai ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <a href="{{ route('pk.kehadiran.index', ['search' => $jadwal->klien->nama_klien ?? '']) }}" class="btn-action-icon btn-action-view" title="Catat Kehadiran">
                                        <i class="bi bi-camera-fill text-success"></i>
                                    </a>
                                    <button class="btn-action-icon btn-action-view" title="Detail Jadwal" onclick="showDetailJadwal({{ $jadwal->id_jadwal_bimbingan }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn-action-icon btn-action-edit" title="Edit Jadwal" onclick="showEditJadwal({{ $jadwal->id_jadwal_bimbingan }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn-action-icon btn-action-delete" title="Hapus Jadwal" onclick="confirmDeleteJadwal({{ $jadwal->id_jadwal_bimbingan }}, '{{ addslashes($jadwal->klien->nama_klien ?? '') }}', '{{ $jadwal->tgl_bimbingan->format('d/m/Y') }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Tidak ada data jadwal bimbingan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jadwalList->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center" style="font-size: 0.8rem;">
                <span class="text-muted">
                    Menampilkan {{ $jadwalList->firstItem() }} - {{ $jadwalList->lastItem() }} dari {{ $jadwalList->total() }} jadwal
                </span>
                {{ $jadwalList->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Jadwal Bimbingan -->
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form action="{{ route('pk.jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-header-figma">
                    <div>
                        <h5 class="modal-title-figma">Tambah Jadwal Bimbingan</h5>
                        <p class="modal-subtitle-figma">Atur sesi bimbingan tatap muka klien pemasyarakatan</p>
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
                            <label class="form-label-figma">Status Pelaksanaan <span class="text-danger">*</span></label>
                            <select name="id_status_bimbingan" class="form-select form-control-figma" required>
                                @foreach($allStatus as $st)
                                    <option value="{{ $st->id_status_bimbingan }}" {{ $st->id_status_bimbingan == 4 ? 'selected' : '' }}>
                                        {{ $st->nama_status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tanggal Bimbingan <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_bimbingan" class="form-control form-control-figma" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control form-control-figma" value="09:00" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Petugas Pembimbing (PK)</label>
                            <select name="id_pegawai" class="form-select form-control-figma">
                                @foreach($allPK as $petugas)
                                    <option value="{{ $petugas->id_pegawai }}" {{ $petugas->id_pegawai == Auth::id() ? 'selected' : '' }}>
                                        {{ $petugas->nama_pegawai }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Fokus / Materi Bimbingan</label>
                            <input type="text" name="keterangan" class="form-control form-control-figma" placeholder="Contoh: Konseling kepribadian dan pencegahan relaps">
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

<!-- Modal Detail Jadwal Bimbingan -->
<div class="modal fade" id="modalDetailJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <div class="modal-header-figma">
                <div>
                    <h5 class="modal-title-figma">Detail Jadwal Bimbingan</h5>
                    <p class="modal-subtitle-figma">Rincian agenda pelaksanaan dan verifikasi presensi</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailJadwalContent">
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

<!-- Modal Edit Jadwal Bimbingan -->
<div class="modal fade" id="modalEditJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form id="formEditJadwal" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header-figma">
                    <div>
                        <h5 class="modal-title-figma">Edit Jadwal Bimbingan</h5>
                        <p class="modal-subtitle-figma">Perbarui data pelaksanaan bimbingan klien</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Klien <span class="text-danger">*</span></label>
                            <select name="id_klien" id="edit_id_klien" class="form-select form-control-figma" required>
                                @foreach($allKlien as $k)
                                    <option value="{{ $k->id_klien }}">{{ $k->no_register }} - {{ $k->nama_klien }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Status Pelaksanaan <span class="text-danger">*</span></label>
                            <select name="id_status_bimbingan" id="edit_id_status" class="form-select form-control-figma" required>
                                @foreach($allStatus as $st)
                                    <option value="{{ $st->id_status_bimbingan }}">{{ $st->nama_status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tanggal Bimbingan <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_bimbingan" id="edit_tgl_bimbingan" class="form-control form-control-figma" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" id="edit_jam_mulai" class="form-control form-control-figma" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Petugas Pembimbing (PK)</label>
                            <select name="id_pegawai" id="edit_id_pegawai" class="form-select form-control-figma">
                                @foreach($allPK as $petugas)
                                    <option value="{{ $petugas->id_pegawai }}">{{ $petugas->nama_pegawai }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Fokus / Materi Bimbingan</label>
                            <input type="text" name="keterangan" id="edit_keterangan" class="form-control form-control-figma">
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

<!-- Modal Konfirmasi Hapus Jadwal Bimbingan -->
<div class="modal fade" id="modalDeleteJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content modal-content-figma text-center p-4">
            <form id="formDeleteJadwal" method="POST">
                @csrf
                @method('DELETE')
                <div class="delete-icon-wrapper mx-auto">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--simba-navy); font-size: 1.15rem;">Hapus Jadwal?</h5>
                <p class="text-muted small mb-3">Apakah Anda yakin ingin menghapus sesi bimbingan ini?</p>
                <div class="fw-bold fs-6 mb-1 text-dark" id="deleteJadwalKlien">-</div>
                <div class="text-muted small mb-4" id="deleteJadwalDate">-</div>
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
    function showDetailJadwal(id) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetailJadwal'));
        modal.show();

        fetch(`/pk/jadwal/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const jadwal = res.jadwal;
            const klien = res.klien;
            const status = res.status;
            const pegawai = res.pegawai;

            document.getElementById('detailJadwalContent').innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Nama Klien:</div>
                        <div class="fw-bold fs-6 text-dark">${klien ? klien.nama_klien : '-'}</div>
                        <small class="text-muted font-monospace">${klien ? klien.no_register : ''}</small>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Status Pelaksanaan:</div>
                        <span class="badge-simba badge-simba-info mt-1">${status ? status.nama_status : '-'}</span>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Tanggal Bimbingan:</div>
                        <div class="fw-semibold text-dark">${res.formatted_date}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Jam Mulai:</div>
                        <div class="fw-semibold text-dark">${jadwal.jam_mulai.substring(0, 5)} WITA</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Pembimbing Kemasyarakatan:</div>
                        <div class="fw-semibold text-dark">${pegawai ? pegawai.nama_pegawai : '-'}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Kasus:</div>
                        <div class="fw-semibold text-dark">${klien ? klien.tindak_pidana : '-'}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Materi / Keterangan:</div>
                        <div class="p-3 bg-light rounded-3 mt-1 text-dark">${jadwal.keterangan || 'Bimbingan rutin kepribadian dan kemandirian.'}</div>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            document.getElementById('detailJadwalContent').innerHTML = `<div class="alert alert-danger">Gagal memuat detail jadwal.</div>`;
        });
    }

    function showEditJadwal(id) {
        fetch(`/pk/jadwal/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const jadwal = res.jadwal;
            document.getElementById('formEditJadwal').action = `/pk/jadwal/${jadwal.id_jadwal_bimbingan}`;
            document.getElementById('edit_id_klien').value = jadwal.id_klien;
            document.getElementById('edit_id_status').value = jadwal.id_status_bimbingan;
            document.getElementById('edit_tgl_bimbingan').value = jadwal.tgl_bimbingan.substring(0, 10);
            document.getElementById('edit_jam_mulai').value = jadwal.jam_mulai.substring(0, 5);
            document.getElementById('edit_id_pegawai').value = jadwal.id_pegawai;
            document.getElementById('edit_keterangan').value = jadwal.keterangan || '';
            
            const modal = new bootstrap.Modal(document.getElementById('modalEditJadwal'));
            modal.show();
        });
    }

    function confirmDeleteJadwal(id, namaKlien, tanggal) {
        document.getElementById('formDeleteJadwal').action = `/pk/jadwal/${id}`;
        document.getElementById('deleteJadwalKlien').innerText = namaKlien;
        document.getElementById('deleteJadwalDate').innerText = `Tanggal: ${tanggal}`;

        const modal = new bootstrap.Modal(document.getElementById('modalDeleteJadwal'));
        modal.show();
    }
</script>
@endsection
