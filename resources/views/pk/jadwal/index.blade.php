@extends('layouts.app')

@section('title', 'Jadwal Bimbingan - SIMBA')
@section('page_title', 'Kelola Jadwal Sesi Bimbingan')

@section('content')
<div class="container-fluid px-0">

    <!-- Header Actions -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Daftar Jadwal Bimbingan Klien</h4>
            <p class="text-muted small mb-0">Pengelolaan agenda sesi bimbingan tatap muka, pemantauan status kehadiran, dan materi evaluasi kemandirian.</p>
        </div>
        <div>
            <button class="btn btn-success fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                <i class="bi bi-calendar-plus-fill fs-6"></i>
                <span>Tambah Jadwal Bimbingan</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('pk.jadwal.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No. Register atau Nama Klien..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select name="id_status_bimbingan" class="form-select form-select-sm">
                        <option value="">Semua Status Pelaksanaan</option>
                        @foreach($allStatus as $st)
                            <option value="{{ $st->id_status_bimbingan }}" {{ request('id_status_bimbingan') == $st->id_status_bimbingan ? 'selected' : '' }}>
                                {{ $st->nama_status }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <input type="date" name="tgl_bimbingan" class="form-control form-control-sm" value="{{ request('tgl_bimbingan') }}" title="Filter Tanggal">
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-success flex-grow-1">Filter</button>
                    @if(request()->hasAny(['search', 'id_status_bimbingan', 'tgl_bimbingan']))
                        <a href="{{ route('pk.jadwal.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                            <th>No. Register Klien</th>
                            <th>Nama Lengkap Klien</th>
                            <th>Tanggal Bimbingan</th>
                            <th>Jam Mulai</th>
                            <th>Status Pelaksanaan</th>
                            <th>Petugas PK</th>
                            <th style="width: 150px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwalList as $index => $jadwal)
                            <tr>
                                <td class="text-center text-muted small">{{ $jadwalList->firstItem() + $index }}</td>
                                <td>
                                    <span class="fw-bold font-monospace text-dark">{{ $jadwal->klien->no_register ?? '-' }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $jadwal->klien->nama_klien ?? 'Klien Dihapus' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border">
                                        <i class="bi bi-calendar-event me-1"></i> {{ $jadwal->tgl_bimbingan->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">
                                        <i class="bi bi-clock me-1 text-muted"></i> {{ substr($jadwal->jam_mulai, 0, 5) }} WITA
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match(strtolower($jadwal->statusBimbingan->nama_status ?? '')) {
                                            'terlaksana' => 'bg-success text-white',
                                            'menunggu' => 'bg-warning text-dark',
                                            'absen' => 'bg-danger text-white',
                                            default => 'bg-primary text-white'
                                        };
                                    @endphp
                                    <span class="badge badge-status {{ $statusClass }}">
                                        {{ $jadwal->statusBimbingan->nama_status ?? 'Dijadwalkan' }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $jadwal->pegawai->nama_pegawai ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('pk.kehadiran.index', ['search' => $jadwal->klien->nama_klien ?? '']) }}" class="btn btn-outline-success" title="Catat Presensi Kehadiran">
                                            <i class="bi bi-camera-fill"></i>
                                        </a>
                                        <button class="btn btn-outline-info" title="Detail Jadwal" onclick="showDetailJadwal({{ $jadwal->id_jadwal_bimbingan }})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" title="Edit Jadwal" onclick="showEditJadwal({{ $jadwal->id_jadwal_bimbingan }})">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Hapus Jadwal" onclick="confirmDeleteJadwal({{ $jadwal->id_jadwal_bimbingan }}, '{{ addslashes($jadwal->klien->nama_klien ?? '') }}', '{{ $jadwal->tgl_bimbingan->format('d/m/Y') }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                    Tidak ada data jadwal bimbingan yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($jadwalList->hasPages())
                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Menampilkan {{ $jadwalList->firstItem() }} - {{ $jadwalList->lastItem() }} dari {{ $jadwalList->total() }} jadwal
                    </small>
                    {{ $jadwalList->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Modal Tambah Jadwal Bimbingan -->
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('pk.jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-calendar-plus-fill text-success me-2"></i> Tambah Jadwal Bimbingan Baru
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
                            <label class="form-label small fw-semibold">Status Pelaksanaan <span class="text-danger">*</span></label>
                            <select name="id_status_bimbingan" class="form-select" required>
                                @foreach($allStatus as $st)
                                    <option value="{{ $st->id_status_bimbingan }}" {{ $st->id_status_bimbingan == 4 ? 'selected' : '' }}>
                                        {{ $st->nama_status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Bimbingan <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_bimbingan" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Jam Mulai Bimbingan <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control" value="09:00" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Petugas Pembimbing (PK)</label>
                            <select name="id_pegawai" class="form-select">
                                @foreach($allPK as $petugas)
                                    <option value="{{ $petugas->id_pegawai }}" {{ $petugas->id_pegawai == Auth::id() ? 'selected' : '' }}>
                                        {{ $petugas->nama_pegawai }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Fokus / Materi Bimbingan</label>
                            <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Konseling kepribadian dan evaluasi kemandirian">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-semibold">Simpan Jadwal Bimbingan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Jadwal Bimbingan -->
<div class="modal fade" id="modalDetailJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-info-circle-fill text-info me-2"></i> Rincian Jadwal Bimbingan Klien
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailJadwalContent">
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

<!-- Modal Edit Jadwal Bimbingan -->
<div class="modal fade" id="modalEditJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditJadwal" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square text-warning me-2"></i> Edit Jadwal Bimbingan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Klien Pemasyarakatan <span class="text-danger">*</span></label>
                            <select name="id_klien" id="edit_id_klien" class="form-select" required>
                                @foreach($allKlien as $k)
                                    <option value="{{ $k->id_klien }}">{{ $k->no_register }} - {{ $k->nama_klien }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Status Pelaksanaan <span class="text-danger">*</span></label>
                            <select name="id_status_bimbingan" id="edit_id_status" class="form-select" required>
                                @foreach($allStatus as $st)
                                    <option value="{{ $st->id_status_bimbingan }}">{{ $st->nama_status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Bimbingan <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_bimbingan" id="edit_tgl_bimbingan" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Jam Mulai Bimbingan <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" id="edit_jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Petugas Pembimbing (PK)</label>
                            <select name="id_pegawai" id="edit_id_pegawai" class="form-select">
                                @foreach($allPK as $petugas)
                                    <option value="{{ $petugas->id_pegawai }}">{{ $petugas->nama_pegawai }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Fokus / Materi Bimbingan</label>
                            <input type="text" name="keterangan" id="edit_keterangan" class="form-control">
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

<!-- Modal Konfirmasi Hapus Jadwal Bimbingan -->
<div class="modal fade" id="modalDeleteJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formDeleteJadwal" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Hapus Jadwal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="mb-2">Apakah Anda yakin ingin menghapus sesi jadwal bimbingan ini?</p>
                    <div class="fw-bold fs-5 text-dark" id="deleteJadwalKlien">-</div>
                    <small class="text-muted d-block mt-2" id="deleteJadwalDate">-</small>
                </div>
                <div class="modal-footer bg-light justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 fw-semibold">Ya, Hapus</button>
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
            const kehadiran = res.kehadiran;

            let kehadiranInfo = kehadiran
                ? `<div class="alert alert-success border-0 small mt-2">
                        <strong>Presensi Terverifikasi:</strong> Klien hadir pada ${kehadiran.tgl_hadir} pukul ${kehadiran.jam_hadir}.<br>
                        <em>Catatan PK:</em> ${kehadiran.catatan}
                   </div>`
                : `<div class="alert alert-warning border-0 small mt-2">
                        Belum ada rekaman bukti kehadiran untuk jadwal ini.
                        <a href="/pk/kehadiran?search=${encodeURIComponent(klien ? klien.nama_klien : '')}" class="btn btn-sm btn-outline-dark mt-2 d-inline-block">Catat Presensi Sekarang</a>
                   </div>`;

            document.getElementById('detailJadwalContent').innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Nama Klien:</div>
                        <div class="fw-bold fs-6 text-dark">${klien ? klien.nama_klien : '-'}</div>
                        <small class="text-muted font-monospace">${klien ? klien.no_register : ''}</small>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Status Pelaksanaan:</div>
                        <span class="badge bg-primary fs-6 mt-1">${status ? status.nama_status : '-'}</span>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Hari & Tanggal:</div>
                        <div class="fw-semibold text-dark">${res.formatted_date}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Jam Mulai Bimbingan:</div>
                        <div class="fw-semibold text-dark">${jadwal.jam_mulai.substring(0, 5)} WITA</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Pembimbing Kemasyarakatan (PK):</div>
                        <div class="fw-semibold text-dark">${pegawai ? pegawai.nama_pegawai : '-'}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Kasus / Perkara:</div>
                        <div class="fw-semibold text-dark">${klien ? klien.tindak_pidana : '-'}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Materi / Catatan Bimbingan:</div>
                        <div class="p-3 bg-light rounded-3 mt-1 text-dark">${jadwal.keterangan || 'Bimbingan rutin kepribadian dan kemandirian.'}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Status Presensi Kehadiran:</div>
                        ${kehadiranInfo}
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
        document.getElementById('deleteJadwalDate').innerText = `Tanggal Sesi: ${tanggal}`;

        const modal = new bootstrap.Modal(document.getElementById('modalDeleteJadwal'));
        modal.show();
    }
</script>
@endsection
