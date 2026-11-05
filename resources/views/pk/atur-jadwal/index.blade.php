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

    <!-- Form Card (Sesuai Buat Jadwal Figma) -->
    <div class="table-card-figma p-4 p-md-5 mb-5" style="border-radius: 16px;">
        <form action="{{ route('pk.atur-jadwal.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Pilih Klien <span class="text-danger">*</span></label>
                    <select name="id_klien" id="buat_id_klien" class="form-select form-control-figma" required onchange="syncClientTime(this)">
                        <option value="" disabled {{ old('id_klien', request('id_klien')) ? '' : 'selected' }}>Pilih Klien Binaan</option>
                        @foreach($allKlien as $k)
                            <option value="{{ $k->id_klien }}" data-jam="{{ $k->jam_mulai ? substr($k->jam_mulai, 0, 5) : '09:00' }}" {{ old('id_klien', request('id_klien')) == $k->id_klien ? 'selected' : '' }}>
                                {{ $k->nama_klien }} ({{ $k->no_register }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tgl_bimbingan" class="form-control form-control-figma" value="{{ old('tgl_bimbingan') }}" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Waktu <span class="text-danger">*</span></label>
                    <input type="time" name="jam_mulai" id="buat_jam_mulai" class="form-control form-control-figma" value="{{ old('jam_mulai', '09:00') }}" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control form-control-figma" value="{{ old('keterangan') }}">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4 pt-3">
                <a href="{{ route('pk.jadwal.index') }}" class="btn btn-figma-cancel" style="min-width: 140px; height: 42px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">Batal</a>
                <button type="submit" class="btn btn-figma-save" style="min-width: 140px; height: 42px; display: inline-flex; align-items: center; justify-content: center;">Simpan</button>
            </div>
        </form>
    </div>

</div>

<script>
    function syncClientTime(select) {
        const option = select.options[select.selectedIndex];
        if (option && option.dataset.jam) {
            document.getElementById('buat_jam_mulai').value = option.dataset.jam;
        }
    }
</script>
@endsection
