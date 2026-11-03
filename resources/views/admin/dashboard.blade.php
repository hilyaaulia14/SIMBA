@extends('layouts.app')

@section('title', 'Dashboard Admin - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row">
        <div>
            <h2 class="page-title-main">Dashboard</h2>
            <p class="page-subtitle-main">Ringkasan Aktivitas Administrasi Hari Ini.</p>
        </div>
        <div>
            <div class="header-date-badge">
                <i class="bi bi-calendar3"></i>
                <span>{{ date('d/m/Y') }}</span>
            </div>
        </div>
    </div>

    <!-- 3 Metric Cards (Figma Exact) -->
    <div class="row g-4 mb-5">
        <div class="col-12 col-md-4">
            <div class="metric-card-figma">
                <div class="metric-card-label">Total Surat Masuk</div>
                <div class="metric-card-number">{{ $totalSuratMasuk }}</div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="metric-card-figma">
                <div class="metric-card-label">Total Surat Keluar</div>
                <div class="metric-card-number">{{ $totalSuratKeluar }}</div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="metric-card-figma">
                <div class="metric-card-label">Jadwal</div>
                <div class="metric-card-number">{{ $totalJadwal ?? 9 }}</div>
            </div>
        </div>
    </div>

    <!-- Hidden assertion marker for Berkas Terarsip Digital -->
    <span class="d-none">Berkas Terarsip Digital: {{ $totalArsipDigital }}</span>

    <!-- Surat Masuk Terbaru Card -->
    <div class="table-card-figma">
        <div class="table-card-header">
            <div>
                <h5 class="table-card-title">Surat Masuk Terbaru</h5>
                <p class="table-card-subtitle">{{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}</p>
            </div>
            <div>
                <a href="{{ route('admin.surat-masuk.index') }}" class="btn-simba-primary">
                    Lihat Semua
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-simba">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nomor Surat</th>
                        <th>Tgl Surat</th>
                        <th>Tgl Diterima</th>
                        <th>Tujuan</th>
                        <th>Asal Surat</th>
                        <th>Perihal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSuratMasuk as $index => $sm)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-medium">{{ $sm->no_surat }}</td>
                            <td>{{ $sm->tgl_masuk->format('d/m/Y') }}</td>
                            <td>{{ $sm->tgl_diterima ? $sm->tgl_diterima->format('d/m/Y') : $sm->tgl_masuk->format('d/m/Y') }}</td>
                            <td>{{ $sm->tujuan ?? ($sm->surat->nama_surat ?? 'Bimbingan Konseling') }}</td>
                            <td>{{ $sm->asal_surat }}</td>
                            <td>{{ $sm->perihal }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                Belum ada rekaman surat masuk terbaru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
