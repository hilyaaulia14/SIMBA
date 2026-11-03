@extends('layouts.app')

@section('title', 'Buat Jadwal Bimbingan - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row mb-4">
        <div>
            <h2 class="page-title-main">Buat Jadwal Bimbingan</h2>
            <p class="page-subtitle-main">Jadwalkan sesi bimbingan baru untuk klien binaan Anda.</p>
        </div>
    </div>

    <!-- Form Card (Sesuai Buat Jadwal.svg) -->
    <div class="table-card-figma p-4 p-md-5 mb-5">
        <form action="{{ route('pk.atur-jadwal.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Pilih Klien <span class="text-danger">*</span></label>
                    <select name="id_klien" id="buat_id_klien" class="form-select form-control-figma" required onchange="syncClientTime(this)">
                        <option value="">Pilih Klien Binaan</option>
                        @foreach($allKlien as $k)
                            <option value="{{ $k->id_klien }}" data-jam="{{ $k->jam_mulai ? substr($k->jam_mulai, 0, 5) : '09:00' }}" {{ request('id_klien') == $k->id_klien ? 'selected' : '' }}>
                                {{ $k->nama_klien }} ({{ $k->no_register }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tgl_bimbingan" class="form-control form-control-figma" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Waktu <span class="text-danger">*</span></label>
                    <input type="time" name="jam_mulai" id="buat_jam_mulai" class="form-control form-control-figma" value="09:00" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control form-control-figma" placeholder="Keterangan (Opsional)">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4 pt-3">
                <a href="{{ route('pk.jadwal.index') }}" class="btn btn-figma-cancel text-decoration-none">Batal</a>
                <button type="submit" class="btn btn-figma-save">Simpan</button>
            </div>
        </form>
    </div>

    <!-- Daftar Pengaturan Jadwal Rutin Klien Card -->
    <div class="table-card-figma">
        <div class="table-card-header">
            <div>
                <h5 class="table-card-title">Daftar Pengaturan Jadwal Bimbingan Rutin</h5>
                <p class="table-card-subtitle">Ketentuan jadwal wajib lapor berkala per klien binaan</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-simba">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nomor Register</th>
                        <th>Nama Klien</th>
                        <th>Hari Bimbingan</th>
                        <th>Jam Mulai</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aturJadwalList as $index => $item)
                        <tr>
                            <td>{{ $aturJadwalList->firstItem() + $index }}</td>
                            <td class="fw-medium">{{ $item->klien->no_register ?? '-' }}</td>
                            <td class="fw-medium">{{ $item->klien->nama_klien ?? '-' }}</td>
                            <td>
                                <span class="status-pill-terjadwal">
                                    {{ $item->hari }}
                                </span>
                            </td>
                            <td>{{ substr($item->jam_mulai, 0, 5) }} WITA</td>
                            <td style="text-align: center;">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <button class="btn-action-square" title="Edit Jadwal" onclick="editAturJadwal({{ $item->id_atur_jadwal }}, '{{ $item->hari }}', '{{ substr($item->jam_mulai, 0, 5) }}')">
                                        <img src="{{ asset('assets/images/edit.svg') }}" alt="Edit">
                                    </button>
                                    <button class="btn-action-square btn-delete" title="Hapus Jadwal" onclick="confirmDeleteAturJadwal({{ $item->id_atur_jadwal }})">
                                        <img src="{{ asset('assets/images/hapus.svg') }}" alt="Hapus">
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Belum ada pengaturan jadwal rutin klien yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($aturJadwalList->hasPages() || $aturJadwalList->total() > 0)
            <div class="p-3 px-4 border-top d-flex justify-content-between align-items-center" style="font-size: 0.84rem; color: #64748B;">
                <span>Menampilkan {{ $aturJadwalList->firstItem() ?? 0 }} sampai {{ $aturJadwalList->lastItem() ?? 0 }} dari {{ $aturJadwalList->total() }} jadwal</span>
                <div>
                    {{ $aturJadwalList->links('pagination::bootstrap-5') }}
                </div>
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
                        <h4 class="modal-title-figma">Edit Jadwal Rutin</h4>
                        <p class="modal-subtitle-figma">Perbarui hari dan jam mulai bimbingan</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
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
                        <input type="time" name="jam_mulai" id="edit_jam" class="form-control form-control-figma" required>
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

<!-- Modal Hapus Atur Jadwal -->
<div class="modal fade" id="modalDeleteAturJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content modal-content-figma text-center p-4">
            <div class="modal-body p-2">
                <div class="delete-dialog-badge">
                    <img src="{{ asset('assets/images/warning.svg') }}" alt="Peringatan">
                </div>
                <h4 class="fw-bold mb-2" style="color: #102A45; font-size: 1.5rem;">Hapus Jadwal?</h4>
                <p class="text-dark small mb-4" style="line-height: 1.5;">
                    Pengaturan jadwal rutin klien ini akan dihapus.<br>Tindakan ini tidak dapat dibatalkan.
                </p>
                <form id="formDeleteAturJadwal" method="POST" class="d-flex justify-content-center gap-3">
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
    function syncClientTime(select) {
        const option = select.options[select.selectedIndex];
        if (option && option.dataset.jam) {
            document.getElementById('buat_jam_mulai').value = option.dataset.jam;
        }
    }

    function editAturJadwal(id, hari, jam) {
        document.getElementById('formEditAturJadwal').action = `/pk/atur-jadwal/${id}`;
        document.getElementById('edit_hari').value = hari;
        document.getElementById('edit_jam').value = jam;
        const modal = new bootstrap.Modal(document.getElementById('modalEditAturJadwal'));
        modal.show();
    }

    function confirmDeleteAturJadwal(id) {
        document.getElementById('formDeleteAturJadwal').action = `/pk/atur-jadwal/${id}`;
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteAturJadwal'));
        modal.show();
    }
</script>
@endsection
