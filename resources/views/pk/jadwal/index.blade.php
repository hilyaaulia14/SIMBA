@extends('layouts.app')

@section('title', 'Jadwal Bimbingan - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row mb-4">
        <div>
            <h2 class="page-title-main">Jadwal Bimbingan</h2>
            <p class="page-subtitle-main">Pantau seluruh jadwal bimbingan klien</p>
        </div>
    </div>

    <!-- Standalone Filter Bar (Di Luar Card Tabel Sesuai Figma) -->
    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
        <form action="{{ route('pk.jadwal.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
            <div style="width: 250px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 50px 0 0 50px; border-color: var(--simba-border); padding-left: 1.15rem;">
                        <img src="{{ asset('assets/images/cari.svg') }}" alt="Cari" style="width: 15px; height: 15px; object-fit: contain;">
                    </span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0 filter-pill-input" placeholder="Telusuri" value="{{ request('search') }}" style="border-radius: 0 50px 50px 0 !important; padding-left: 0.35rem;">
                </div>
            </div>

            <div style="width: 170px;">
                <input type="date" name="tgl_bimbingan" class="form-control form-control-sm filter-pill-input" value="{{ request('tgl_bimbingan') }}" placeholder="DD/MM/YY" onchange="this.form.submit()">
            </div>

            @if(request()->hasAny(['search', 'tgl_bimbingan']))
                <a href="{{ route('pk.jadwal.index') }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 50px; font-weight: 500;">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="table-card-figma">
        <div class="table-responsive">
            <table class="table-simba">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 16%; text-align: center; padding-left: 1rem !important; padding-right: 1rem !important;">Tanggal</th>
                        <th class="text-center" style="width: 18%; text-align: center;">Jam</th>
                        <th style="width: 24%; padding-left: 2rem;">Klien</th>
                        <th class="text-center" style="width: 24%; text-align: center;">Status</th>
                        <th class="text-center" style="width: 18%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalList as $jadwal)
                        @php
                            $stName = strtolower($jadwal->statusBimbingan->nama_status ?? '');
                            $statusClass = match(true) {
                                str_contains($stName, 'hadir') && !str_contains($stName, 'tidak') => 'status-pill-hadir',
                                str_contains($stName, 'ulang') => 'status-pill-dijadwalkan-ulang',
                                str_contains($stName, 'tidak') || str_contains($stName, 'absen') => 'status-pill-tidak-hadir',
                                default => 'status-pill-terjadwal'
                            };
                            $displayStatus = match(true) {
                                str_contains($stName, 'hadir') && !str_contains($stName, 'tidak') => 'Hadir',
                                str_contains($stName, 'ulang') => 'Dijadwalkan Ulang',
                                str_contains($stName, 'tidak') || str_contains($stName, 'absen') => 'Tidak Hadir',
                                default => 'Terjadwal'
                            };
                        @endphp
                        <tr>
                            <td class="text-center" style="text-align: center; padding-left: 1rem !important; padding-right: 1rem !important;">{{ $jadwal->tgl_bimbingan->format('d/m/Y') }}</td>
                            <td class="text-center" style="text-align: center;">{{ substr($jadwal->jam_mulai, 0, 5) }}</td>
                            <td style="padding-left: 2rem;">{{ $jadwal->klien->nama_klien ?? '-' }}</td>
                            <td class="text-center" style="text-align: center;">
                                <span class="{{ $statusClass }}">
                                    ● {{ $displayStatus }}
                                </span>
                            </td>
                            <td class="text-center" style="text-align: center;">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <button class="btn-action-square" title="Detail Jadwal" onclick="showDetailJadwal({{ $jadwal->id_jadwal_bimbingan }})">
                                        <img src="{{ asset('assets/images/detail.svg') }}" alt="Detail">
                                    </button>
                                    <button class="btn-action-square" title="Edit Jadwal" onclick="showEditJadwal({{ $jadwal->id_jadwal_bimbingan }})">
                                        <img src="{{ asset('assets/images/edit.svg') }}" alt="Edit">
                                    </button>
                                    <button class="btn-action-square" title="Catatan Kehadiran" onclick="openCatatanKehadiran({{ $jadwal->id_jadwal_bimbingan }}, '{{ addslashes($jadwal->klien->nama_klien ?? '') }}', '{{ $jadwal->tgl_bimbingan->translatedFormat('d F Y') }}', '{{ substr($jadwal->jam_mulai, 0, 5) }}')">
                                        <img src="{{ asset('assets/images/kehadiran.svg') }}" alt="Catatan Kehadiran">
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                Tidak ada data jadwal bimbingan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jadwalList->hasPages() || $jadwalList->total() > 0)
            <div class="p-3 px-4 border-top d-flex justify-content-between align-items-center" style="font-size: 0.84rem; color: #64748B;">
                <span>
                    @if($jadwalList->total() > 0 && $jadwalList->total() <= $jadwalList->perPage())
                        Menampilkan {{ $jadwalList->total() }} dari {{ $jadwalList->total() }} jadwal
                    @else
                        Menampilkan {{ $jadwalList->firstItem() ?? 0 }} sampai {{ $jadwalList->lastItem() ?? 0 }} dari {{ $jadwalList->total() }} jadwal
                    @endif
                </span>
                <div>
                    {{ $jadwalList->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Modal Detail Jadwal Bimbingan (Sesuai Detail Jadwal Bimbingan.svg) -->
<div class="modal fade" id="modalDetailJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <div class="modal-header-figma">
                <div>
                    <h4 class="modal-title-figma">Detail Jadwal Bimbingan</h4>
                    <p class="modal-subtitle-figma" id="detail_jadwal_klien_title">-</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailJadwalContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer-figma justify-content-between">
                <div>
                    <button type="button" class="btn btn-outline-danger px-4" id="detail_jadwal_btn_delete" style="border-radius: 8px;">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-figma-cancel" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-figma-save" id="detail_jadwal_btn_edit">
                        <i class="bi bi-pencil-square me-1"></i> Edit Jadwal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Jadwal Bimbingan (Sesuai Edit Jadwal Bimbingan.svg) -->
<div class="modal fade" id="modalEditJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form id="formEditJadwal" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header-figma">
                    <div>
                        <h4 class="modal-title-figma">Edit Jadwal Bimbingan</h4>
                        <p class="modal-subtitle-figma">Masukkan jadwal bimbingan.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label-figma">Nama Klien <span class="text-danger">*</span></label>
                            <select name="id_klien" id="edit_jadwal_id_klien" class="form-select form-control-figma" required>
                                @foreach($allKlien as $k)
                                    <option value="{{ $k->id_klien }}">{{ $k->nama_klien }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Tanggal Bimbingan <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_bimbingan" id="edit_jadwal_tgl" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Waktu <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" id="edit_jadwal_jam" class="form-control form-control-figma" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Status Bimbingan <span class="text-danger">*</span></label>
                            <select name="id_status_bimbingan" id="edit_jadwal_id_status" class="form-select form-control-figma" required>
                                @foreach($allStatus as $st)
                                    <option value="{{ $st->id_status_bimbingan }}">{{ $st->nama_status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Keterangan</label>
                            <input type="text" name="keterangan" id="edit_jadwal_keterangan" class="form-control form-control-figma" placeholder="Keterangan (Opsional)">
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

<!-- Modal Catatan Kehadiran (Sesuai Catatan Kehadiran.svg) -->
<div class="modal fade" id="modalCatatanKehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-figma">
            <form action="{{ route('pk.kehadiran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_jadwal_bimbingan" id="kehadiran_id_jadwal">
                <input type="hidden" name="id_klien" id="kehadiran_id_klien">
                <input type="hidden" name="tgl_hadir" id="kehadiran_tgl_hadir" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="jam_hadir" id="kehadiran_jam_hadir" value="{{ date('H:i') }}">

                <div class="modal-header-figma">
                    <div>
                        <h4 class="modal-title-figma">Catatan Kehadiran</h4>
                        <p class="modal-subtitle-figma" id="kehadiran_subtitle">-</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label-figma">Status Kehadiran <span class="text-danger">*</span></label>
                            <select name="status_kehadiran" class="form-select form-control-figma" required>
                                <option value="Hadir">Hadir</option>
                                <option value="Tidak Hadir">Tidak Hadir</option>
                                <option value="Dijadwalkan Ulang">Dijadwalkan Ulang</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Bukti Dokumentasi <span class="text-danger">*</span></label>
                            <div class="upload-dropzone-figma" onclick="document.getElementById('upload_presensi_file').click()">
                                <img src="{{ asset('assets/images/unggah.svg') }}" alt="Unggah" style="width: 44px; height: 44px; object-fit: contain; margin-bottom: 0.5rem;">
                                <span class="small text-muted d-block" id="presensi_filename">Klik untuk mengunggah bukti</span>
                                <input type="file" name="foto_kehadiran" id="upload_presensi_file" class="d-none" accept="image/*,.pdf" onchange="updateFilename(this, 'presensi_filename')">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label-figma">Keterangan</label>
                            <input type="text" name="catatan" class="form-control form-control-figma" placeholder="Keterangan (Opsional)" value="Klien hadir bimbingan tatap muka sesuai jadwal." required>
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

<!-- Modal Hapus Jadwal -->
<div class="modal fade" id="modalDeleteJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content modal-content-figma text-center p-4">
            <div class="modal-body p-2">
                <div class="delete-dialog-badge">
                    <img src="{{ asset('assets/images/warning.svg') }}" alt="Peringatan">
                </div>
                <h4 class="fw-bold mb-2" style="color: #102A45; font-size: 1.5rem;">Hapus Jadwal?</h4>
                <p class="text-dark small mb-4" style="line-height: 1.5;">
                    Jadwal akan dihapus permanen dari sistem.<br>Tindakan ini tidak dapat dibatalkan.
                </p>
                <form id="formDeleteJadwal" method="POST" class="d-flex justify-content-center gap-3">
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

    function showDetailJadwal(id) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetailJadwal'));
        modal.show();

        fetch(`/pk/jadwal/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const data = res.jadwal;
            const fotoUrl = res.foto_url || (data.kehadiran ? (data.kehadiran.foto_url || (data.kehadiran.foto_kehadiran ? '/storage/' + data.kehadiran.foto_kehadiran : null)) : null);
            document.getElementById('detail_jadwal_klien_title').innerText = data.klien ? data.klien.nama_klien : '-';

            let fotoBuktiHtml = '';
            if (fotoUrl) {
                fotoBuktiHtml = `
                    <div class="col-12 mt-3 pt-3 border-top">
                        <div class="form-label-figma mb-2" style="font-weight: 600; color: #102A45; font-size: 0.88rem;">Foto Bukti Kehadiran</div>
                        <div class="p-3 rounded-3" style="background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                            <div class="d-flex flex-column flex-sm-row align-items-center gap-3">
                                <a href="${fotoUrl}" target="_blank" title="Klik untuk membuka ukuran penuh">
                                    <img src="${fotoUrl}" alt="Bukti Kehadiran" class="rounded-3 shadow-sm" style="max-height: 140px; max-width: 220px; object-fit: contain; cursor: pointer; border: 2px solid #CBD5E1;">
                                </a>
                                <div class="small" style="color: #475569;">
                                    <div class="mb-1"><strong class="text-dark">Waktu Kehadiran:</strong> ${data.kehadiran && data.kehadiran.jam_hadir ? data.kehadiran.jam_hadir.substring(0, 5) : '-'} WITA</div>
                                    <div class="mb-1"><strong class="text-dark">Tanggal Kehadiran:</strong> ${data.kehadiran && data.kehadiran.tgl_hadir ? data.kehadiran.tgl_hadir.substring(0, 10) : '-'}</div>
                                    ${data.kehadiran && data.kehadiran.catatan ? `<div class="mt-1"><strong class="text-dark">Keterangan:</strong> ${data.kehadiran.catatan}</div>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                fotoBuktiHtml = `
                    <div class="col-12 mt-3 pt-3 border-top">
                        <div class="form-label-figma mb-2" style="font-weight: 600; color: #102A45; font-size: 0.88rem;">Foto Bukti Kehadiran</div>
                        <div class="p-3 text-center rounded-3 text-muted small" style="background-color: #F8FAFC; border: 1px dashed #CBD5E1;">
                            <i class="bi bi-camera me-1"></i> Belum ada foto bukti kehadiran untuk sesi bimbingan ini.
                        </div>
                    </div>
                `;
            }

            document.getElementById('detailJadwalContent').innerHTML = `
                <div class="row g-4 text-start">
                    <div class="col-md-6">
                        <div class="form-label-figma">Nama Klien</div>
                        <div class="fw-medium text-dark">${data.klien ? data.klien.nama_klien : '-'}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Nama Pembimbing</div>
                        <div class="fw-medium text-dark">${data.pegawai ? data.pegawai.nama_pegawai : 'Andi Pratama, S.Sos.'}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Tanggal</div>
                        <div class="fw-medium text-dark">${data.tgl_bimbingan ? data.tgl_bimbingan.substring(0, 10) : '-'}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Waktu</div>
                        <div class="fw-medium text-dark">${data.jam_mulai ? data.jam_mulai.substring(0, 5) : '-'}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Keterangan</div>
                        <div class="fw-medium text-dark">${data.keterangan || 'Bimbingan rutin berjalan lancar.'}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-label-figma">Status</div>
                        <div class="fw-medium text-dark">${data.status_bimbingan ? data.status_bimbingan.nama_status : 'Terjadwal'}</div>
                    </div>
                    ${fotoBuktiHtml}
                </div>
            `;

            document.getElementById('detail_jadwal_btn_delete').onclick = function() {
                modal.hide();
                confirmDeleteJadwal(data.id_jadwal_bimbingan);
            };

            document.getElementById('detail_jadwal_btn_edit').onclick = function() {
                modal.hide();
                showEditJadwal(data.id_jadwal_bimbingan);
            };
        });
    }

    function showEditJadwal(id) {
        fetch(`/pk/jadwal/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(res => {
            const data = res.jadwal;
            document.getElementById('formEditJadwal').action = `/pk/jadwal/${data.id_jadwal_bimbingan}`;
            document.getElementById('edit_jadwal_id_klien').value = data.id_klien;
            document.getElementById('edit_jadwal_tgl').value = data.tgl_bimbingan ? data.tgl_bimbingan.substring(0, 10) : '';
            document.getElementById('edit_jadwal_jam').value = data.jam_mulai ? data.jam_mulai.substring(0, 5) : '09:00';
            document.getElementById('edit_jadwal_id_status').value = data.id_status_bimbingan || 4;
            document.getElementById('edit_jadwal_keterangan').value = data.keterangan || '';

            const modal = new bootstrap.Modal(document.getElementById('modalEditJadwal'));
            modal.show();
        });
    }

    function openCatatanKehadiran(id, namaKlien, tgl, jam) {
        document.getElementById('kehadiran_id_jadwal').value = id;
        document.getElementById('kehadiran_subtitle').innerText = `${namaKlien} - ${tgl}, ${jam}`;
        
        fetch(`/pk/jadwal/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(res => {
                document.getElementById('kehadiran_id_klien').value = res.jadwal.id_klien;
            });

        const modal = new bootstrap.Modal(document.getElementById('modalCatatanKehadiran'));
        modal.show();
    }

    function confirmDeleteJadwal(id) {
        document.getElementById('formDeleteJadwal').action = `/pk/jadwal/${id}`;
        const modal = new bootstrap.Modal(document.getElementById('modalDeleteJadwal'));
        modal.show();
    }
</script>
@endsection
