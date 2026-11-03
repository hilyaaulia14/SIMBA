@extends('layouts.app')

@section('title', 'Data Klien Pemasyarakatan - SIMBA')
@section('page_title', 'Kelola Data Klien Pemasyarakatan')

@section('content')
<div class="container-fluid px-0">

    <!-- Header Actions -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Data Klien Pemasyarakatan</h4>
            <p class="text-muted small mb-0">Manajemen identitas warga binaan pemasyarakatan yang menjalani bimbingan integrasi di Bapas Kelas I Banjarmasin.</p>
        </div>
        <div>
            <button class="btn btn-success fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKlien">
                <i class="bi bi-person-plus-fill fs-6"></i>
                <span>Tambah Klien Baru</span>
            </button>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('pk.klien.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No. Register, Nama Klien, atau Kasus Pidana..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select name="jenis_kelamin" class="form-select form-select-sm">
                        <option value="">Semua Jenis Kelamin</option>
                        <option value="Laki-laki" {{ request('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ request('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-success flex-grow-1">Filter</button>
                    @if(request()->hasAny(['search', 'jenis_kelamin']))
                        <a href="{{ route('pk.klien.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                            <th>No. Registrasi Klien</th>
                            <th>Nama Lengkap Klien</th>
                            <th>Jenis Kelamin</th>
                            <th>Tgl. Lahir</th>
                            <th>Kasus / Tindak Pidana</th>
                            <th>Jadwal Rutin Wajib Lapor</th>
                            <th style="width: 140px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($klienList as $index => $klien)
                            <tr>
                                <td class="text-center text-muted small">{{ $klienList->firstItem() + $index }}</td>
                                <td>
                                    <span class="fw-bold font-monospace text-dark">{{ $klien->no_register }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $klien->nama_klien }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $klien->jenis_kelamin === 'Laki-laki' ? 'bg-info-subtle text-info border' : 'bg-danger-subtle text-danger border' }}">
                                        {{ $klien->jenis_kelamin }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $klien->tgl_lahir ? $klien->tgl_lahir->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 220px;" title="{{ $klien->tindak_pidana }}">
                                        {{ $klien->tindak_pidana }}
                                    </div>
                                </td>
                                <td>
                                    @if($klien->aturJadwal)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-clock me-1"></i> {{ $klien->aturJadwal->hari }}, {{ substr($klien->aturJadwal->jam_mulai, 0, 5) }} WITA
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border">Belum Diatur</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-info" title="Detail Klien" onclick="showDetailKlien({{ $klien->id_klien }})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" title="Edit Klien" onclick="showEditKlien({{ $klien->id_klien }})">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Hapus Klien" onclick="confirmDeleteKlien({{ $klien->id_klien }}, '{{ addslashes($klien->nama_klien) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                                    Tidak ada data klien pemasyarakatan yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($klienList->hasPages())
                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Menampilkan {{ $klienList->firstItem() }} - {{ $klienList->lastItem() }} dari {{ $klienList->total() }} klien
                    </small>
                    {{ $klienList->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Modal Tambah Klien -->
<div class="modal fade" id="modalTambahKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('pk.klien.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-plus-fill text-success me-2"></i> Pendaftaran Klien Pemasyarakatan Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Nomor Registrasi Klien <span class="text-danger">*</span></label>
                            <input type="text" name="no_register" class="form-control" placeholder="Contoh: REG/2026/BPS/006" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Nama Lengkap Klien <span class="text-danger">*</span></label>
                            <input type="text" name="nama_klien" class="form-control" placeholder="Masukkan nama lengkap klien" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Jam Mulai Standar</label>
                            <input type="time" name="jam_mulai" class="form-control" value="09:00">
                            <small class="text-muted">Jam standar pelaksanaan bimbingan.</small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Kasus / Tindak Pidana Asal <span class="text-danger">*</span></label>
                            <input type="text" name="tindak_pidana" class="form-control" placeholder="Contoh: Pencurian (Pasal 363 KUHP)" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-semibold">Simpan Data Klien</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Klien -->
<div class="modal fade" id="modalDetailKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-vcard text-info me-2"></i> Rincian Lengkap Data Klien
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailKlienContent">
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

<!-- Modal Edit Klien -->
<div class="modal fade" id="modalEditKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditKlien" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square text-warning me-2"></i> Edit Data Klien Pemasyarakatan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Nomor Registrasi Klien <span class="text-danger">*</span></label>
                            <input type="text" name="no_register" id="edit_no_register" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Nama Lengkap Klien <span class="text-danger">*</span></label>
                            <input type="text" name="nama_klien" id="edit_nama_klien" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" id="edit_jenis_kelamin" class="form-select" required>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" id="edit_tgl_lahir" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Jam Mulai Standar</label>
                            <input type="time" name="jam_mulai" id="edit_jam_mulai" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Kasus / Tindak Pidana Asal <span class="text-danger">*</span></label>
                            <input type="text" name="tindak_pidana" id="edit_tindak_pidana" class="form-control" required>
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

<!-- Modal Konfirmasi Hapus Klien -->
<div class="modal fade" id="modalDeleteKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formDeleteKlien" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Hapus Klien
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="mb-2">Apakah Anda yakin ingin menghapus data klien ini?</p>
                    <div class="fw-bold fs-5 text-dark" id="deleteKlienName">-</div>
                    <small class="text-muted d-block mt-2">Seluruh riwayat jadwal bimbingan dan catatan kehadiran klien ini akan ikut terhapus.</small>
                </div>
                <div class="modal-footer bg-light justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 fw-semibold">Ya, Hapus Klien</button>
                </div>
            </form>
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
            const klien = res.klien;
            const aturJadwal = res.atur_jadwal;
            const riwayatBimbingan = res.riwayat_bimbingan || [];
            const riwayatKehadiran = res.riwayat_kehadiran || [];

            let bimbinganHtml = riwayatBimbingan.length > 0
                ? riwayatBimbingan.map(j => `
                    <tr>
                        <td>${j.tgl_bimbingan ? j.tgl_bimbingan.substring(0, 10) : '-'}</td>
                        <td>${j.jam_mulai ? j.jam_mulai.substring(0, 5) : '-'}</td>
                        <td><span class="badge bg-secondary">${j.status_bimbingan ? j.status_bimbingan.nama_status : '-'}</span></td>
                        <td>${j.keterangan || '-'}</td>
                    </tr>
                `).join('')
                : `<tr><td colspan="4" class="text-center text-muted">Belum ada riwayat bimbingan.</td></tr>`;

            let kehadiranHtml = riwayatKehadiran.length > 0
                ? riwayatKehadiran.map(k => `
                    <tr>
                        <td>${k.tgl_hadir ? k.tgl_hadir.substring(0, 10) : '-'}</td>
                        <td>${k.jam_hadir ? k.jam_hadir.substring(0, 5) : '-'}</td>
                        <td>${k.catatan || '-'}</td>
                    </tr>
                `).join('')
                : `<tr><td colspan="3" class="text-center text-muted">Belum ada catatan kehadiran.</td></tr>`;

            document.getElementById('detailKlienContent').innerHTML = `
                <!-- Profile Header -->
                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-4">
                    <div style="width: 54px; height: 54px; border-radius: 50%; background: #0D2A4A; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.3rem;">
                        ${klien.nama_klien.charAt(0)}
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">${klien.nama_klien}</h5>
                        <div class="small text-muted font-monospace">No. Register: <strong>${klien.no_register}</strong></div>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="text-muted small">Jenis Kelamin:</div>
                        <div class="fw-semibold text-dark">${klien.jenis_kelamin}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Tanggal Lahir:</div>
                        <div class="fw-semibold text-dark">${res.formatted_tgl_lahir}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Kasus / Tindak Pidana:</div>
                        <div class="fw-semibold text-dark">${klien.tindak_pidana}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Jadwal Tetap Wajib Lapor:</div>
                        <div class="fw-semibold text-success">${aturJadwal ? `${aturJadwal.hari}, ${aturJadwal.jam_mulai.substring(0, 5)} WITA` : '<span class="text-muted">Belum ditetapkan</span>'}</div>
                    </div>
                </div>

                <!-- Riwayat Bimbingan -->
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">Riwayat Agenda Bimbingan:</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Status</th>
                                <th>Catatan Materi</th>
                            </tr>
                        </thead>
                        <tbody>${bimbinganHtml}</tbody>
                    </table>
                </div>

                <!-- Riwayat Kehadiran -->
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">Riwayat Presensi / Kehadiran Fisik:</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal Hadir</th>
                                <th>Jam Hadir</th>
                                <th>Evaluasi Perkembangan Klien</th>
                            </tr>
                        </thead>
                        <tbody>${kehadiranHtml}</tbody>
                    </table>
                </div>
            `;
        })
        .catch(err => {
            document.getElementById('detailKlienContent').innerHTML = `<div class="alert alert-danger">Gagal memuat rincian data klien.</div>`;
        });
    }

    function showEditKlien(id) {
        fetch(`/pk/klien/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const klien = res.klien;
            document.getElementById('formEditKlien').action = `/pk/klien/${klien.id_klien}`;
            document.getElementById('edit_no_register').value = klien.no_register;
            document.getElementById('edit_nama_klien').value = klien.nama_klien;
            document.getElementById('edit_jenis_kelamin').value = klien.jenis_kelamin;
            document.getElementById('edit_tgl_lahir').value = klien.tgl_lahir ? klien.tgl_lahir.substring(0, 10) : '';
            document.getElementById('edit_jam_mulai').value = klien.jam_mulai ? klien.jam_mulai.substring(0, 5) : '';
            document.getElementById('edit_tindak_pidana').value = klien.tindak_pidana;
            
            const modal = new bootstrap.Modal(document.getElementById('modalEditKlien'));
            modal.show();
        });
    }

    function confirmDeleteKlien(id, namaKlien) {
        document.getElementById('formDeleteKlien').action = `/pk/klien/${id}`;
        document.getElementById('deleteKlienName').innerText = namaKlien;
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteKlien'));
        modal.show();
    }
</script>
@endsection
