@extends('layouts.app')

@section('title', 'Profil Pembimbing Kemasyarakatan - SIMBA')
@section('page_title', 'Profil Pembimbing Kemasyarakatan (PK)')

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">

    <!-- Breadcrumb / Back Navigation -->
    <div class="mb-4">
        <a href="{{ route('pk.dashboard') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
        <h4 class="fw-bold mt-2 text-dark">Profil Pembimbing Kemasyarakatan (PK)</h4>
        <p class="text-muted small">Kelola informasi data diri fungsional Pembimbing Kemasyarakatan Bapas Kelas I Banjarmasin.</p>
    </div>

    <div class="row g-4">
        <!-- Profile Overview Card -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <img src="{{ $pk->foto_url }}" alt="Foto PK" class="avatar-circle shadow" style="width: 110px; height: 110px;">
                    <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success p-2 border border-white">
                        <i class="bi bi-person-check-fill text-white"></i>
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $pk->nama_pegawai }}</h5>
                <span class="badge bg-success-subtle text-success border border-success-subtle mb-2">
                    {{ $pk->role->nama_role ?? 'Pembimbing Kemasyarakatan' }}
                </span>
                <p class="text-muted small mb-0">{{ $pk->jabatan }}</p>
                <div class="mt-3 pt-3 border-top text-start">
                    <div class="text-muted" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">NIP (Username Login):</div>
                    <div class="fw-semibold text-dark font-monospace">{{ $pk->nip_usn }}</div>
                </div>
            </div>
        </div>

        <!-- Form Edit Profile -->
        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-person-lines-fill text-success me-2"></i> Perbarui Data Fungsional PK
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('pk.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nama_pegawai" class="form-label small fw-semibold">Nama Lengkap beserta Gelar <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pegawai" id="nama_pegawai" class="form-control" value="{{ old('nama_pegawai', $pk->nama_pegawai) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="nip_usn" class="form-label small fw-semibold">NIP (Username Login) <span class="text-danger">*</span></label>
                            <input type="text" name="nip_usn" id="nip_usn" class="form-control" value="{{ old('nip_usn', $pk->nip_usn) }}" required>
                            <small class="text-muted">NIP dipergunakan sebagai kredensial unik saat masuk sistem SIMBA.</small>
                        </div>

                        <div class="mb-3">
                            <label for="jabatan" class="form-label small fw-semibold">Jabatan Fungsional PK <span class="text-danger">*</span></label>
                            <input type="text" name="jabatan" id="jabatan" class="form-control" value="{{ old('jabatan', $pk->jabatan) }}" placeholder="Contoh: Pembimbing Kemasyarakatan Ahli Pertama" required>
                        </div>

                        <div class="mb-4">
                            <label for="foto" class="form-label small fw-semibold">Ganti Foto Profil (Opsional)</label>
                            <input type="file" name="foto" id="foto" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <small class="text-muted">Format gambar: JPG, JPEG, PNG, WEBP. Maksimal 2MB.</small>
                        </div>

                        <div class="alert alert-secondary border-0 bg-light small d-flex align-items-center gap-2 mb-4 text-muted">
                            <i class="bi bi-info-circle text-secondary fs-5"></i>
                            <div>Berdasarkan standar operasional Bapas, modul profil Pembimbing Kemasyarakatan (PK) tidak menyertakan fitur ganti kata sandi mandiri dan tanpa informasi kontak.</div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success fw-semibold px-4">
                                <i class="bi bi-check-lg me-1"></i> Simpan Profil PK
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
