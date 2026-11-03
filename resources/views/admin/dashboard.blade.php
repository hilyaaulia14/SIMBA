@extends('layouts.app')

@section('title', 'Dashboard Admin - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row">
        <div>
            <h3 class="page-title-main">Dashboard</h3>
            <p class="page-subtitle-main">Ringkasan informasi persuratan dan pengarsipan digital Bapas</p>
        </div>
        <div>
            <a href="{{ route('admin.surat-masuk.index') }}" class="btn-simba-primary">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Surat</span>
            </a>
        </div>
    </div>

    <!-- 3 Metric Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Surat Masuk -->
        <div class="col-12 col-md-4">
            <div class="metric-card-figma">
                <div class="metric-card-label">Total Surat Masuk</div>
                <div class="metric-card-number">{{ $totalSuratMasuk }}</div>
            </div>
        </div>

        <!-- Total Surat Keluar -->
        <div class="col-12 col-md-4">
            <div class="metric-card-figma">
                <div class="metric-card-label">Total Surat Keluar</div>
                <div class="metric-card-number">{{ $totalSuratKeluar }}</div>
            </div>
        </div>

        <!-- Total Berkas Terarsip Digital -->
        <div class="col-12 col-md-4">
            <div class="metric-card-figma">
                <div class="metric-card-label">Arsip Berkas Digital</div>
                <div class="metric-card-number">{{ $totalArsipDigital }}</div>
            </div>
        </div>
    </div>

    <!-- Table Card: Aktivitas Surat Terkini -->
    <div class="table-card-figma">
        <div class="table-filter-bar">
            <div>
                <h5 class="fw-bold mb-0" style="color: var(--simba-navy); font-size: 1.05rem;">Aktivitas Surat Terkini</h5>
                <small class="text-muted" style="font-size: 0.76rem;">Daftar rekaman surat masuk dan keluar terbaru di Bapas</small>
            </div>
            <div>
                <a href="{{ route('admin.surat-masuk.index') }}" class="btn-simba-primary" style="padding: 0.45rem 1.15rem; font-size: 0.78rem;">
                    <span>Lihat Semua</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-simba">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>No. Surat</th>
                        <th>Instansi / Pihak</th>
                        <th>Tanggal</th>
                        <th>Perihal</th>
                        <th>Klasifikasi</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse($recentSuratMasuk as $sm)
                        <tr>
                            <td class="text-center text-muted">{{ $no++ }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $sm->no_surat }}</span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1" style="font-size: 0.65rem;">Masuk</span>
                            </td>
                            <td>{{ $sm->asal_surat }}</td>
                            <td>{{ $sm->tgl_masuk->format('d/m/Y') }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 240px;" title="{{ $sm->perihal }}">
                                    {{ $sm->perihal }}
                                </div>
                            </td>
                            <td>
                                <span class="badge-simba badge-simba-info">{{ $sm->surat->nama_surat ?? 'Umum' }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.surat-masuk.index', ['search' => $sm->no_surat]) }}" class="btn-action-icon btn-action-view" title="Lihat di Surat Masuk">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                    @endforelse

                    @forelse($recentSuratKeluar as $sk)
                        <tr>
                            <td class="text-center text-muted">{{ $no++ }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $sk->no_surat }}</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 0.65rem;">Keluar</span>
                            </td>
                            <td>{{ $sk->tujuan_surat }}</td>
                            <td>{{ $sk->tgl_keluar->format('d/m/Y') }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 240px;" title="{{ $sk->perihal }}">
                                    {{ $sk->perihal }}
                                </div>
                            </td>
                            <td>
                                <span class="badge-simba badge-simba-success">{{ $sk->surat->nama_surat ?? 'Umum' }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.surat-keluar.index', ['search' => $sk->no_surat]) }}" class="btn-action-icon btn-action-view" title="Lihat di Surat Keluar">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                    @endforelse

                    @if($recentSuratMasuk->isEmpty() && $recentSuratKeluar->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada aktivitas persuratan terkini.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
