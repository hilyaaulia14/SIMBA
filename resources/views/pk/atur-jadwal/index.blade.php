@extends('layouts.app')

@section('title', 'Atur Jadwal Bimbingan - SIMBA')
@section('page_title', 'Pengaturan Jadwal Rutin Bimbingan')

@section('content')
<div class="container-fluid px-0">

    <!-- Header Actions -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Pengaturan Jadwal Bimbingan</h4>
            <p class="text-muted small mb-0">Penetapan hari bimbingan berkala dan jam mulai wajib lapor untuk setiap klien pemasyarakatan.</p>
        </div>
        <div>
            <button class="btn btn-success fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAturJadwal">
                <i class="bi bi-clock-fill fs-6"></i>
                <span>Tetapkan Jadwal Klien</span>
            </button>
        </div>
    </div>

    <!-- Notice Card on Criteria -->
    <div class="card border-0 shadow-sm mb-4 bg-light border-start border-4 border-success">
        <div class="card-body p-3 d-flex align-items-center gap-3">
            <i class="bi bi-info-circle-fill text-success fs-4"></i>
            <div class="small text-muted">
                <strong>Ketentuan Penjadwalan:</strong> Pengaturan jadwal bimbingan oleh PK berfokus pada penentuan <strong>Hari bimbingan</strong> (Senin s/d Jumat) dan <strong>Jam Mulai bimbingan</strong> tanpa batasan jam selesai serta tanpa pembatasan kuota maksimal klien per hari.
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('pk.atur-jadwal.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Nama Klien atau No. Register..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select name="hari" class="form-select form-select-sm">
                        <option value="">Semua Hari Bimbingan</option>
                        @foreach($daftarHari as $h)
                            <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>Hari {{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-success flex-grow-1">Filter</button>
                    @if(request()->hasAny(['search', 'hari']))
                        <a href="{{ route('pk.atur-jadwal.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                            <th>Hari Pelaksanaan</th>
                            <th>Jam Mulai Bimbingan</th>
                            <th>Kasus / Perkara</th>
                            <th style="width: 120px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aturJadwalList as $index => $aj)
                            <tr>
                                <td class="text-center text-muted small">{{ $aturJadwalList->firstItem() + $index }}</td>
                                <td>
                                    <span class="fw-bold font-monospace text-dark">{{ $aj->klien->no_register ?? '-' }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $aj->klien->nama_klien ?? 'Klien Terhapus' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                                        <i class="bi bi-calendar-day me-1"></i> {{ $aj->hari }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">
                                        <i class="bi bi-clock me-1 text-muted"></i> {{ substr($aj->jam_mulai, 0, 5) }} WITA
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 220px;" title="{{ $aj->klien->tindak_pidana ?? '' }}">
                                        {{ $aj->klien->tindak_pidana ?? '-' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-warning" title="Edit Pengaturan" onclick="editAturJadwal({{ $aj->id_atur_jadwal }}, '{{ $aj->hari }}', '{{ substr($aj->jam_mulai, 0, 5) }}', '{{ addslashes($aj->klien->nama_klien ?? '') }}')">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Hapus Pengaturan" onclick="deleteAturJadwal({{ $aj->id_atur_jadwal }}, '{{ addslashes($aj->klien->nama_klien ?? '') }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada pengaturan jadwal bimbingan klien.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($aturJadwalList->hasPages())
                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Menampilkan {{ $aturJadwalList->firstItem() }} - {{ $aturJadwalList->lastItem() }} dari {{ $aturJadwalList->total() }} jadwal
                    </small>
                    {{ $aturJadwalList->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Modal Tambah / Tetapkan Atur Jadwal -->
<div class="modal fade" id="modalTambahAturJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('pk.atur-jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-clock-fill text-success me-2"></i> Tetapkan Jadwal Klien
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Klien Pemasyarakatan <span class="text-danger">*</span></label>
                        <select name="id_klien" class="form-select" required>
                            <option value="">Pilih Klien</option>
                            @foreach($allKlien as $k)
                                <option value="{{ $k->id_klien }}">{{ $k->no_register }} - {{ $k->nama_klien }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Hari Bimbingan <span class="text-danger">*</span></label>
                        <select name="hari" class="form-select" required>
                            <option value="">Pilih Hari</option>
                            @foreach($daftarHari as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Jam Mulai Bimbingan <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai" class="form-control" value="09:00" required>
                        <small class="text-muted">Waktu mulai pelaksanaan sesi wajib lapor klien (WITA).</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-semibold">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Atur Jadwal -->
<div class="modal fade" id="modalEditAturJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditAturJadwal" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square text-warning me-2"></i> Edit Jadwal Klien
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Klien:</label>
                        <div class="fw-bold fs-6 text-dark" id="editKlienNama">-</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Hari Bimbingan <span class="text-danger">*</span></label>
                        <select name="hari" id="edit_hari" class="form-select" required>
                            @foreach($daftarHari as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Jam Mulai Bimbingan <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai" id="edit_jam_mulai_aj" class="form-control" required>
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

<!-- Modal Konfirmasi Hapus Atur Jadwal -->
<div class="modal fade" id="modalDeleteAturJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formDeleteAturJadwal" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Hapus Jadwal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="mb-2">Apakah Anda yakin ingin menghapus pengaturan jadwal tetap untuk klien ini?</p>
                    <div class="fw-bold fs-5 text-dark" id="deleteAturJadwalKlien">-</div>
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
    function editAturJadwal(id, hari, jamMulai, namaKlien) {
        document.getElementById('formEditAturJadwal').action = `/pk/atur-jadwal/${id}`;
        document.getElementById('editKlienNama').innerText = namaKlien;
        document.getElementById('edit_hari').value = hari;
        document.getElementById('edit_jam_mulai_aj').value = jamMulai;

        const modal = new bootstrap.Modal(document.getElementById('modalEditAturJadwal'));
        modal.show();
    }

    function deleteAturJadwal(id, namaKlien) {
        document.getElementById('formDeleteAturJadwal').action = `/pk/atur-jadwal/${id}`;
        document.getElementById('deleteAturJadwalKlien').innerText = namaKlien;

        const modal = new bootstrap.Modal(document.getElementById('modalDeleteAturJadwal'));
        modal.show();
    }
</script>
@endsection
