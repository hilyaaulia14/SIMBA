@extends('layouts.app')

@section('title', 'Profil PK - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row mb-4">
        <div>
            <h3 class="page-title-main">Profil</h3>
            <p class="page-subtitle-main">Kelola informasi identitas fungsional Pembimbing Kemasyarakatan (PK) Bapas Banjarmasin</p>
        </div>
    </div>

    <form action="{{ route('pk.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Card: Avatar -->
            <div class="col-12 col-md-4">
                <div class="table-card-figma p-4 text-center">
                    <div class="position-relative d-inline-block mx-auto mb-3">
                        <img src="{{ $pk->foto_url }}" alt="Foto PK" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #FFFFFF;">
                    </div>
                    <h5 class="fw-bold mb-1" style="color: var(--simba-navy); font-size: 1.05rem;">{{ $pk->nama_pegawai }}</h5>
                    <div class="text-muted small mb-3">NIP: {{ $pk->nip_usn }}</div>
                    
                    <label class="btn btn-outline-secondary btn-sm px-3" style="border-radius: 50px; font-size: 0.78rem; cursor: pointer;">
                        <i class="bi bi-camera me-1"></i> Ganti Foto Profil
                        <input type="file" name="foto" class="d-none" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewAvatar(this)">
                    </label>
                    <div id="avatar_feedback" class="small text-primary mt-2"></div>
                </div>
            </div>

            <!-- Right Card: Data Profil -->
            <div class="col-12 col-md-8">
                <div class="table-card-figma p-4">
                    <h6 class="fw-bold mb-4" style="color: var(--simba-navy); text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.95rem;">
                        Data Profil
                    </h6>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pegawai" class="form-control form-control-figma" value="{{ old('nama_pegawai', $pk->nama_pegawai) }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">NIP (Username Login) <span class="text-danger">*</span></label>
                            <input type="text" name="nip_usn" class="form-control form-control-figma" value="{{ old('nip_usn', $pk->nip_usn) }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jabatan Fungsional PK <span class="text-danger">*</span></label>
                            <input type="text" name="jabatan" class="form-control form-control-figma" value="{{ old('jabatan', $pk->jabatan) }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Peran Hak Akses</label>
                            <input type="text" class="form-control form-control-figma bg-light" value="{{ $pk->role->nama_role ?? 'Pembimbing Kemasyarakatan' }}" disabled>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <button type="submit" class="btn-simba-primary" style="padding: 0.65rem 2rem;">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            document.getElementById('avatar_feedback').innerText = "Foto dipilih: " + input.files[0].name;
        }
    }
</script>
@endsection
