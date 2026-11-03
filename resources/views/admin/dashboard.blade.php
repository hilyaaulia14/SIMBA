@extends('layouts.app')

@section('title', 'Dashboard Admin / Bagian Umum')
@section('page_title', 'Dashboard Administrasi Persuratan & Pengarsipan')

@section('content')
<div class="container-fluid px-0">

    <!-- Welcome Hero Banner -->
    <div class="card border-0 mb-4 text-white shadow-sm" style="background: linear-gradient(135deg, #0D2A4A 0%, #163B65 100%); border-radius: 16px;">
        <div class="card-body p-4 p-lg-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-warning text-dark mb-2 fw-semibold px-3 py-1">
                    <i class="bi bi-shield-check me-1"></i> Panel Admin Bagian Umum
                </span>
                <h4 class="fw-bold mb-1">Selamat Datang, {{ Auth::user()->nama_pegawai }}!</h4>
                <p class="text-white-50 mb-0 small" style="max-width: 620px;">
                    Kelola tata kelola persuratan dinas masuk dan keluar serta pengarsipan berkas digital Bapas Kelas I Banjarmasin dengan tertib, transparan, dan terintegrasi.
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.surat-masuk.index') }}" class="btn btn-light text-primary fw-semibold btn-sm px-3 shadow-sm">
                    <i class="bi bi-inbox-fill me-1"></i> Kelola Surat Masuk
                </a>
                <a href="{{ route('admin.surat-keluar.index') }}" class="btn btn-outline-light fw-semibold btn-sm px-3">
                    <i class="bi bi-send-fill me-1"></i> Kelola Surat Keluar
                </a>
            </div>
        </div>
    </div>

    <!-- 3 Metric Cards for Admin -->
    <div class="row g-3 mb-4">
        <!-- Total Surat Masuk -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card shadow-sm h-100 bg-white">
                <div class="card-body p-3 p-md-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider">Total Surat Masuk</div>
                        <h2 class="fw-bolder mb-1 mt-2 text-dark">{{ $totalSuratMasuk }}</h2>
                        <span class="text-primary small fw-medium">
                            <i class="bi bi-arrow-down-left-circle me-1"></i> Terarsip di sistem
                        </span>
                    </div>
                    <div class="stat-icon-wrapper bg-primary-subtle text-primary">
                        <i class="bi bi-inbox-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Surat Keluar -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card shadow-sm h-100 bg-white">
                <div class="card-body p-3 p-md-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider">Total Surat Keluar</div>
                        <h2 class="fw-bolder mb-1 mt-2 text-dark">{{ $totalSuratKeluar }}</h2>
                        <span class="text-success small fw-medium">
                            <i class="bi bi-arrow-up-right-circle me-1"></i> Diterbitkan resmi
                        </span>
                    </div>
                    <div class="stat-icon-wrapper bg-success-subtle text-success">
                        <i class="bi bi-send-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Berkas Terarsip Digital -->
        <div class="col-12 col-sm-12 col-xl-4">
            <div class="card stat-card shadow-sm h-100 bg-white">
                <div class="card-body p-3 p-md-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider">Berkas Terarsip Digital</div>
                        <h2 class="fw-bolder mb-1 mt-2 text-dark">{{ $totalArsipDigital }}</h2>
                        <span class="text-info small fw-medium">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Lampiran file scan
                        </span>
                    </div>
                    <div class="stat-icon-wrapper bg-info-subtle text-info">
                        <i class="bi bi-archive-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Aktivitas Persuratan Terkini -->
    <div class="row g-4">
        <!-- 5 Surat Masuk Terkini -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-inbox-fill text-primary"></i>
                        <h6 class="mb-0 fw-bold">Surat Masuk Terkini</h6>
                    </div>
                    <a href="{{ route('admin.surat-masuk.index') }}" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No. Surat & Asal</th>
                                    <th>Tanggal</th>
                                    <th>Perihal</th>
                                    <th class="text-center">Berkas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSuratMasuk as $sm)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $sm->no_surat }}</div>
                                            <small class="text-muted">{{ $sm->asal_surat }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border">
                                                {{ $sm->tgl_masuk->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 180px;" title="{{ $sm->perihal }}">
                                                {{ $sm->perihal }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($sm->upload_dokumen)
                                                <a href="{{ route('admin.surat-masuk.download', $sm->id_surat_masuk) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Unduh Berkas">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            @else
                                                <span class="badge bg-secondary-subtle text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada data surat masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5 Surat Keluar Terkini -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-send-fill text-success"></i>
                        <h6 class="mb-0 fw-bold">Surat Keluar Terkini</h6>
                    </div>
                    <a href="{{ route('admin.surat-keluar.index') }}" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold text-success">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No. Surat & Tujuan</th>
                                    <th>Tanggal</th>
                                    <th>Perihal</th>
                                    <th class="text-center">Berkas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSuratKeluar as $sk)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $sk->no_surat }}</div>
                                            <small class="text-muted">{{ $sk->tujuan_surat }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border">
                                                {{ $sk->tgl_keluar->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 180px;" title="{{ $sk->perihal }}">
                                                {{ $sk->perihal }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($sk->upload_dokumen)
                                                <a href="{{ route('admin.surat-keluar.download', $sk->id_surat_keluar) }}" class="btn btn-sm btn-outline-success py-0 px-2" title="Unduh Berkas">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            @else
                                                <span class="badge bg-secondary-subtle text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada data surat keluar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
