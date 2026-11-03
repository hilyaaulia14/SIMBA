@extends('layouts.app')

@section('title', 'Profil Admin - SIMBA')
@section('page_title', 'Pengaturan Profil Admin')

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">

    <!-- Breadcrumb / Back Navigation -->
    <div class="mb-4">
        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
        <h4 class="fw-bold mt-2 text-dark">Profil Pengguna (Admin / Bagian Umum)</h4>
        <p class="text-muted small">Kelola informasi identitas akun staf Bagian Umum Bapas Kelas I Banjarmasin.</p>
    </div>

    <div class="row g-4">
        <!-- Profile Overview Card -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <img src="{{ $admin->foto_url }}" alt="Foto Admin" class="avatar-circle shadow" style="width: 110px; height: 110px;">
                    <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-primary p-2 border border-white">
                        <i class="bi bi-shield-check text-white"></i>
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $admin->nama_pegawai }}</h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2">
                    {{ $admin->role->nama_role ?? 'Admin / Bagian Umum' }}
                </span>
                <p class="text-muted small mb-0">{{ $admin->jabatan }}</p>
                <div class="mt-3 pt-3 border-top text-start">
                    <div class="text-muted" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">NIP (Username Login):</div>
                    <div class="fw-semibold text-dark font-monospace">{{ $admin->nip_usn }}</div>
                </div>
            </div>
        </div>

        <!-- Form Edit Profile -->
        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i> Perbarui Data Profil
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nama_pegawai" class="form-label small fw-semibold">Nama Lengkap beserta Gelar <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pegawai" id="nama_pegawai" class="form-control" value="{{ old('nama_pegawai', $admin->nama_pegawai) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="nip_usn" class="form-label small fw-semibold">NIP (Username Login) <span class="text-danger">*</span></label>
                            <input type="text" name="nip_usn" id="nip_usn" class="form-control" value="{{ old('nip_usn', $admin->nip_usn) }}" required>
                            <small class="text-muted">NIP digunakan sebagai identifikasi unik saat masuk ke sistem.</small>
                        </div>

                        <div class="mb-3">
                            <label for="jabatan" class="form-label small fw-semibold">Jabatan Kedinasan <span class="text-danger">*</span></label>
                            <input type="text" name="jabatan" id="jabatan" class="form-control" value="{{ old('jabatan', $admin->jabatan) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="foto" class="form-label small fw-semibold">Ganti Foto Profil (Opsional)</label>
                            <input type="file" name="foto" id="foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <small class="text-muted">Format gambar: JPG, JPEG, PNG, WEBP. Maksimal 2MB.</small>
                        </div>

                        <div class="alert alert-info border-0 bg-info-subtle small d-flex align-items-center gap-2 mb-4">
                            <i class="bi bi-info-circle-fill text-info fs-5"></i>
                            <div>Sesuai dengan ketentuan operasional, modul profil ini tidak menyediakan fitur penggantian kata sandi mandiri.</div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary fw-semibold px-4">
                                <i class="bi bi-check-lg me-1"></i> Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
