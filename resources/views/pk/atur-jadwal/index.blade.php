@extends('layouts.app')

@section('title', 'Atur Jadwal Bimbingan - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row mb-4">
        <div>
            <h3 class="page-title-main">Atur Jadwal Bimbingan</h3>
            <p class="page-subtitle-main">Formulir pengaturan jadwal bimbingan klien oleh Pembimbing Kemasyarakatan</p>
        </div>
    </div>

    <!-- Form Card Sesuai Desain Figma Atur Jadwal -->
    <div class="table-card-figma p-4 mb-4">
        <form action="{{ route('pk.atur-jadwal.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Pilih Klien <span class="text-danger">*</span></label>
                    <select name="id_klien" class="form-select form-control-figma" required>
                        <option value="">Pilih Klien Pemasyarakatan</option>
                        @foreach($allKlien as $k)
                            <option value="{{ $k->id_klien }}">{{ $k->no_register }} - {{ $k->nama_klien }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Hari Bimbingan <span class="text-danger">*</span></label>
                    <select name="hari" class="form-select form-control-figma" required>
                        <option value="">Pilih Hari Bimbingan</option>
                        @foreach($daftarHari as $h)
                            <option value="{{ $h }}">Hari {{ $h }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Jam Mulai <span class="text-danger">*</span></label>
                    <input type="time" name="jam_mulai" class="form-control form-control-figma" value="09:00" required>
                    <small class="text-muted" style="font-size: 0.72rem;">Waktu standar kehadiran wajib lapor (WITA).</small>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="catatan_atur" class="form-control form-control-figma" placeholder="Contoh: Sesi tatap muka berkala dua minggu sekali">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button type="reset" class="btn btn-outline-secondary px-4" style="border-radius: 8px; font-size: 0.84rem;">Batal</button>
                <button type="submit" class="btn-simba-primary" style="border-radius: 8px; padding: 0.65rem 1.75rem;">Simpan</button>
            </div>
        </form>
    </div>

    <!-- Data Table Card: Daftar Pengaturan Jadwal Klien -->
    <div class="table-card-figma">
        <div class="table-filter-bar">
            <div>
                <h5 class="fw-bold mb-0" style="color: var(--simba-navy); font-size: 1.05rem;">Daftar Jadwal Rutin Klien</h5>
                <small class="text-muted" style="font-size: 0.76rem;">Klien pemasyarakatan yang telah memiliki penetapan hari dan waktu bimbingan</small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-simba">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>No. Register</th>
                        <th>Nama Lengkap Klien</th>
                        <th>Hari Bimbingan</th>
                        <th>Jam Mulai</th>
                        <th>Kasus / Perkara</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aturJadwalList as $index => $aj)
                        <tr>
                            <td class="text-center text-muted">{{ $aturJadwalList->firstItem() + $index }}</td>
                            <td>
                                <span class="font-monospace text-dark fw-bold">{{ $aj->klien->no_register ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $aj->klien->nama_klien ?? 'Klien Dihapus' }}</span>
                            </td>
                            <td>
                                <span class="badge-simba badge-simba-success">
                                    {{ $aj->hari }}
                                </span>
                            </td>
                            <td>{{ substr($aj->jam_mulai, 0, 5) }} WITA</td>
                            <td>{{ $aj->klien->tindak_pidana ?? '-' }}</td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <button class="btn-action-icon btn-action-edit" title="Edit Jadwal" onclick="editAturJadwal({{ $aj->id_atur_jadwal }}, '{{ $aj->hari }}', '{{ substr($aj->jam_mulai, 0, 5) }}', '{{ addslashes($aj->klien->nama_klien ?? '') }}')">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn-action-icon btn-action-delete" title="Hapus Jadwal" onclick="deleteAturJadwal({{ $aj->id_atur_jadwal }}, '{{ addslashes($aj->klien->nama_klien ?? '') }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Belum ada pengaturan jadwal bimbingan yang ditetapkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($aturJadwalList->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center" style="font-size: 0.8rem;">
                <span class="text-muted">
                    Menampilkan {{ $aturJadwalList->firstItem() }} - {{ $aturJadwalList->lastItem() }} dari {{ $aturJadwalList->total() }} jadwal
                </span>
                {{ $aturJadwalList->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Edit Atur Jadwal -->
<div class="modal fade" id="modalEditAturJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form id="formEditAturJadwal" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header-figma">
                    <div>
                        <h5 class="modal-title-figma">Edit Jadwal Klien</h5>
                        <p class="modal-subtitle-figma">Perbarui penetapan hari dan jam bimbingan</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label-figma">Klien:</label>
                        <div class="fw-bold fs-6 text-dark" id="editKlienNama">-</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-figma">Hari Bimbingan <span class="text-danger">*</span></label>
                        <select name="hari" id="edit_hari" class="form-select form-control-figma" required>
                            @foreach($daftarHari as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-figma">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai" id="edit_jam_mulai_aj" class="form-control form-control-figma" required>
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

<!-- Modal Konfirmasi Hapus Atur Jadwal -->
<div class="modal fade" id="modalDeleteAturJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content modal-content-figma text-center p-4">
            <form id="formDeleteAturJadwal" method="POST">
                @csrf
                @method('DELETE')
                <div class="delete-icon-wrapper mx-auto">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--simba-navy); font-size: 1.15rem;">Hapus Jadwal?</h5>
                <p class="text-muted small mb-3">Apakah Anda yakin ingin menghapus pengaturan jadwal tetap untuk klien ini?</p>
                <div class="fw-bold fs-6 mb-4 text-dark" id="deleteAturJadwalKlien">-</div>
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
