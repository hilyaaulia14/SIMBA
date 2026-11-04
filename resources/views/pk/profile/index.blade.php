@extends('layouts.app')

@section('title', 'Profil Saya - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header -->
    <div class="mb-4">
        <h3 class="fw-bold mb-1" style="color: var(--simba-navy); font-size: 1.75rem;">Profil Saya</h3>
        <p class="mb-0" style="font-size: 0.95rem; font-weight: normal; color: #0F2A4A !important;">Kelola informasi akun Anda</p>
    </div>

    <form action="{{ route('pk.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Card: Avatar -->
            <div class="col-12 col-md-4 col-lg-4">
                <div class="card border-0 shadow-sm p-4 text-center d-flex flex-column align-items-center justify-content-center" style="border-radius: 16px; min-height: 260px;">
                    <div class="position-relative d-inline-block mx-auto mb-3">
                        @if($pk->foto)
                            <img id="avatar-preview-img" src="{{ $pk->foto_url }}" alt="{{ $pk->nama_pegawai }}" 
                            class="rounded-circle shadow-sm" 
                            style="width: 88px; height: 88px; object-fit: cover;">
                        @else
                            <div id="avatar-preview-circle" class="d-flex align-items-center justify-content-center rounded-circle mx-auto text-white fw-bold shadow-sm" style="width: 88px; height: 88px; background-color: var(--simba-navy); font-size: 1.85rem; letter-spacing: 1px;">
                                {{ $pk->initials }}
                            </div>
                            <img id="avatar-preview-img" src="" alt="{{ $pk->nama_pegawai }}" class="rounded-circle shadow-sm d-none" style="width: 88px; height: 88px; object-fit: cover;">
                        @endif
                        <label for="foto-upload" class="position-absolute bottom-0 end-0 bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; cursor: pointer; border: 1px solid #E2E8F0;" title="Ganti Foto">
                            <i class="bi bi-camera text-secondary" style="font-size: 0.8rem;"></i>
                        </label>
                        <input type="file" id="foto-upload" name="foto" class="d-none" accept="image/jpeg,image/svg,image/jpg,image/webp" onchange="previewAvatar(this)">
                    </div>
                    <h5 class="fw-bold mb-1" style="color: var(--simba-navy); font-size: 1.15rem;">{{ $pk->nama_pegawai }}</h5>
                    <div class="text-muted" style="font-size: 0.9rem;">@pk</div>
                    <div id="avatar_feedback" class="small text-success mt-2"></div>
                </div>
            </div>

            <!-- Right Card: Informasi Akun -->
            <div class="col-12 col-md-8 col-lg-8">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 16px;">
                    <div class="border-bottom pb-3 mb-4">
                        <h5 class="fw-bold mb-1" style="color: var(--simba-navy); font-size: 1.25rem;">Informasi Akun</h5>
                        <p class="small mb-0" style="font-size: 0.88rem; font-weight: normal; color: #0F2A4A !important;">Perbarui data profil Anda</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Nama Lengkap</label>
                            <input type="text" name="nama_pegawai" class="form-control form-control-figma" value="{{ old('nama_pegawai', $pk->nama_pegawai) }}" placeholder="Nama lengkap" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Username</label>
                            <input type="text" name="nip_usn" class="form-control form-control-figma" value="{{ old('nip_usn', $pk->nip_usn) }}" placeholder="Username" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control form-control-figma" value="{{ old('jabatan', $pk->jabatan) }}" placeholder="Jabatan" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-figma">Peran Hak Akses</label>
                            <input type="text" class="form-control form-control-figma bg-light" value="{{ $pk->role->nama_role ?? 'Pembimbing Kemasyarakatan' }}" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label-figma">Alamat</label>
                            <textarea name="alamat" rows="2" class="form-control form-control-figma" placeholder="Alamat lengkap" style="resize: none;">{{ old('alamat', $pk->alamat) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-2">
                        <button type="submit" class="btn text-white fw-semibold px-4 py-2" style="background-color: var(--simba-navy); border-radius: 8px; font-size: 0.9rem;">
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
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('avatar-preview-img');
                const circle = document.getElementById('avatar-preview-circle');
                if (img) {
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                }
                if (circle) {
                    circle.classList.add('d-none');
                }
                document.getElementById('avatar_feedback').innerText = "Foto dipilih: " + input.files[0].name;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
