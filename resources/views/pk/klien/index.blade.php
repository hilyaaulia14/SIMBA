@extends('layouts.app')

@section('title', 'Data Klien - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row">
        <div>
            <h3 class="page-title-main">Data Klien</h3>
            <p class="page-subtitle-main">Kelola profil warga binaan dan klien pemasyarakatan Bapas Kelas I Banjarmasin</p>
        </div>
        <div>
            <button class="btn-simba-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKlien">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Klien</span>
            </button>
        </div>
    </div>

    <!-- Table Card with Filter Bar -->
    <div class="table-card-figma">
        <div class="table-filter-bar">
            <form action="{{ route('pk.klien.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
                <div style="max-width: 340px; width: 100%;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px; border-color: var(--simba-border);">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Cari nama, nomor register, perkara..." value="{{ request('search') }}" style="border-radius: 0 8px 8px 0; border-color: var(--simba-border);">
                    </div>
                </div>

                <div style="max-width: 200px; width: 100%;">
                    <select name="jenis_kelamin" class="form-select form-select-sm" style="border-radius: 8px; border-color: var(--simba-border);" onchange="this.form.submit()">
                        <option value="">Semua Jenis Kelamin</option>
                        <option value="Laki-laki" {{ request('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ request('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                @if(request()->hasAny(['search', 'jenis_kelamin']))
                    <a href="{{ route('pk.klien.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
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
                        <th>Nama Lengkap</th>
                        <th>Jenis Kelamin</th>
                        <th>Tanggal Lahir</th>
                        <th>Perkara Pidana</th>
                        <th>Jadwal Rutin</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($klienList as $index => $klien)
                        <tr>
                            <td class="text-center text-muted">{{ $klienList->firstItem() + $index }}</td>
                            <td>
                                <span class="font-monospace fw-bold text-dark">{{ $klien->no_register }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $klien->nama_klien }}</span>
                            </td>
                            <td>
                                <span class="badge-simba {{ $klien->jenis_kelamin === 'Laki-laki' ? 'badge-simba-info' : 'badge-simba-warning' }}">
                                    {{ $klien->jenis_kelamin }}
                                </span>
                            </td>
                            <td>{{ $klien->tgl_lahir ? $klien->tgl_lahir->format('d/m/Y') : '-' }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 220px;" title="{{ $klien->tindak_pidana }}">
                                    {{ $klien->tindak_pidana }}
                                </div>
                            </td>
                            <td>
                                @if($klien->aturJadwal)
                                    <span class="badge-simba badge-simba-success">
                                        {{ $klien->aturJadwal->hari }}, {{ substr($klien->aturJadwal->jam_mulai, 0, 5) }} WITA
                                    </span>
                                @else
                                    <span class="badge-simba" style="background-color: #E2E8F0; color: #64748B;">Belum Diatur</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <button class="btn-action-icon btn-action-view" title="Detail Klien" onclick="showDetailKlien({{ $klien->id_klien }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn-action-icon btn-action-edit" title="Edit Klien" onclick="showEditKlien({{ $klien->id_klien }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn-action-icon btn-action-delete" title="Hapus Klien" onclick="confirmDeleteKlien({{ $klien->id_klien }}, '{{ addslashes($klien->nama_klien) }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Tidak ada data klien pemasyarakatan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($klienList->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center" style="font-size: 0.8rem;">
                <span class="text-muted">
                    Menampilkan {{ $klienList->firstItem() }} - {{ $klienList->lastItem() }} dari {{ $klienList->total() }} klien
                </span>
                {{ $klienList->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Klien -->
<div class="modal fade" id="modalTambahKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form action="{{ route('pk.klien.store') }}" method="POST">
                @csrf
                <div class="modal-header-figma">
                    <div>
                        <h5 class="modal-title-figma">Tambah Klien</h5>
                        <p class="modal-subtitle-figma">Isi data klien dibawah ini untuk disimpan</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nomor Register / Klien <span class="text-danger">*</span></label>
                            <input type="text" name="no_register" class="form-control form-control-figma" placeholder="Contoh: REG/2026/001" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nama Lengkap Klien <span class="text-danger">*</span></label>
                            <input type="text" name="nama_klien" class="form-control form-control-figma" placeholder="Nama Klien (ex. Jhon)" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select form-control-figma" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control form-control-figma">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Perkara / Tindak Pidana <span class="text-danger">*</span></label>
                            <input type="text" name="tindak_pidana" class="form-control form-control-figma" placeholder="Contoh: Pasal 363 KUHP (Pencurian)" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jam Mulai Sesi Rutin</label>
                            <input type="time" name="jam_mulai" class="form-control form-control-figma" value="09:00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer-figma">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.84rem;">Batal</button>
                    <button type="submit" class="btn-simba-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Klien -->
<div class="modal fade" id="modalDetailKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <div class="modal-header-figma">
                <div>
                    <h5 class="modal-title-figma">Detail Klien</h5>
                    <p class="modal-subtitle-figma">Informasi lengkap profil dan riwayat warga binaan</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailKlienContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer-figma">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.84rem;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Data Klien -->
<div class="modal fade" id="modalEditKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form id="formEditKlien" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header-figma">
                    <div>
                        <h5 class="modal-title-figma">Edit Data Klien</h5>
                        <p class="modal-subtitle-figma">Ubah data klien dibawah ini lalu simpan</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nomor Register / Klien <span class="text-danger">*</span></label>
                            <input type="text" name="no_register" id="edit_no_register" class="form-control form-control-figma" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nama Lengkap Klien <span class="text-danger">*</span></label>
                            <input type="text" name="nama_klien" id="edit_nama_klien" class="form-control form-control-figma" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" id="edit_jenis_kelamin" class="form-select form-control-figma" required>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" id="edit_tgl_lahir" class="form-control form-control-figma">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Perkara / Tindak Pidana <span class="text-danger">*</span></label>
                            <input type="text" name="tindak_pidana" id="edit_tindak_pidana" class="form-control form-control-figma" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jam Mulai Sesi Rutin</label>
                            <input type="time" name="jam_mulai" id="edit_jam_mulai" class="form-control form-control-figma">
                        </div>
                    </div>
                </div>
                <div class="modal-footer-figma">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.84rem;">Batal</button>
                    <button type="submit" class="btn-simba-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Klien -->
<div class="modal fade" id="modalDeleteKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content modal-content-figma text-center p-3">
            <div class="modal-body p-3">
                <div class="delete-icon-wrapper">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color: #1E293B;">Hapus Klien?</h5>
                <p class="text-muted small mb-4">
                    Apakah Anda yakin ingin menghapus data klien <strong id="deleteKlienName" class="text-dark"></strong>? Tindakan ini tidak dapat dibatalkan.
                </p>
                <form id="formDeleteKlien" method="POST" class="d-flex justify-content-center gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.84rem;">Batal</button>
                    <button type="submit" class="btn btn-danger px-4" style="border-radius: 8px; font-size: 0.84rem; background-color: #EF4444; border-color: #EF4444;">Hapus</button>
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
            const klien = res.klien;
            const aturJadwal = res.atur_jadwal;
            const riwayatBimbingan = res.riwayat_bimbingan || [];
            const riwayatKehadiran = res.riwayat_kehadiran || [];

            let bimbinganRows = riwayatBimbingan.length > 0
                ? riwayatBimbingan.map(j => `
                    <tr>
                        <td>${j.tgl_bimbingan ? j.tgl_bimbingan.substring(0, 10) : '-'}</td>
                        <td>${j.jam_mulai ? j.jam_mulai.substring(0, 5) : '-'} WITA</td>
                        <td><span class="badge-simba badge-simba-info">${j.status_bimbingan ? j.status_bimbingan.nama_status : '-'}</span></td>
                        <td>${j.keterangan || '-'}</td>
                    </tr>
                `).join('')
                : `<tr><td colspan="4" class="text-center text-muted">Belum ada agenda bimbingan.</td></tr>`;

            let kehadiranRows = riwayatKehadiran.length > 0
                ? riwayatKehadiran.map(k => `
                    <tr>
                        <td>${k.tgl_hadir ? k.tgl_hadir.substring(0, 10) : '-'}</td>
                        <td>${k.jam_hadir ? k.jam_hadir.substring(0, 5) : '-'} WITA</td>
                        <td>${k.catatan || '-'}</td>
                    </tr>
                `).join('')
                : `<tr><td colspan="3" class="text-center text-muted">Belum ada rekaman kehadiran.</td></tr>`;

            document.getElementById('detailKlienContent').innerHTML = `
                <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-4" style="background-color: var(--simba-bg);">
                    <div style="width: 52px; height: 52px; border-radius: 50%; background: var(--simba-navy); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.25rem;">
                        ${klien.nama_klien.charAt(0)}
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--simba-navy); font-size: 1.05rem;">${klien.nama_klien}</h6>
                        <div class="small text-muted font-monospace">No. Register: <strong>${klien.no_register}</strong></div>
                    </div>
                </div>

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
                        <div class="text-muted small">Perkara / Tindak Pidana:</div>
                        <div class="fw-semibold text-dark">${klien.tindak_pidana}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Jadwal Sesi Rutin:</div>
                        <div class="fw-semibold text-success">${aturJadwal ? `${aturJadwal.hari}, ${aturJadwal.jam_mulai.substring(0, 5)} WITA` : '<span class="text-muted">Belum ditetapkan</span>'}</div>
                    </div>
                </div>

                <h6 class="fw-bold mb-2" style="color: var(--simba-navy); font-size: 0.88rem;">Riwayat Agenda Bimbingan:</h6>
                <div class="table-responsive mb-4">
                    <table class="table-simba">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Status</th>
                                <th>Materi Bimbingan</th>
                            </tr>
                        </thead>
                        <tbody>${bimbinganRows}</tbody>
                    </table>
                </div>

                <h6 class="fw-bold mb-2" style="color: var(--simba-navy); font-size: 0.88rem;">Riwayat Presensi Kehadiran Fisik:</h6>
                <div class="table-responsive">
                    <table class="table-simba">
                        <thead>
                            <tr>
                                <th>Tanggal Hadir</th>
                                <th>Jam Hadir</th>
                                <th>Evaluasi Perkembangan Klien</th>
                            </tr>
                        </thead>
                        <tbody>${kehadiranRows}</tbody>
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
