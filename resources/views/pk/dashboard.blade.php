@extends('layouts.app')

@section('title', 'Dashboard PK - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row">
        <div>
            <h3 class="page-title-main">Dashboard</h3>
            <p class="page-subtitle-main">Ringkasan informasi bimbingan klien pemasyarakatan Bapas Kelas I Banjarmasin</p>
        </div>
        <div>
            <a href="{{ route('pk.jadwal.index') }}" class="btn-simba-primary">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Jadwal</span>
            </a>
        </div>
    </div>

    <!-- 3 Metric Cards Sesuai Desain Figma -->
    <div class="row g-3 mb-4">
        <!-- 1. Jadwal Hari Ini -->
        <div class="col-12 col-md-4">
            <div class="metric-card-figma">
                <div class="metric-card-label">Jadwal Hari Ini</div>
                <div class="metric-card-number">{{ $jadwalHariIni }}</div>
            </div>
        </div>

        <!-- 2. Jadwal Minggu Ini -->
        <div class="col-12 col-md-4">
            <div class="metric-card-figma">
                <div class="metric-card-label">Jadwal Minggu Ini</div>
                <div class="metric-card-number">{{ $jadwalMingguIni }}</div>
            </div>
        </div>

        <!-- 3. Total Klien -->
        <div class="col-12 col-md-4">
            <div class="metric-card-figma">
                <div class="metric-card-label">Total Klien</div>
                <div class="metric-card-number">{{ $totalKlien }}</div>
            </div>
        </div>
    </div>

    <!-- Table Card: Agenda Sesi Bimbingan Terdekat -->
    <div class="table-card-figma">
        <div class="table-filter-bar">
            <div>
                <h5 class="fw-bold mb-0" style="color: var(--simba-navy); font-size: 1.05rem;">Agenda Bimbingan Klien</h5>
                <small class="text-muted" style="font-size: 0.76rem;">Daftar jadwal bimbingan tatap muka terdekat</small>
            </div>
            <div>
                <a href="{{ route('pk.jadwal.index') }}" class="btn-simba-primary" style="padding: 0.45rem 1.15rem; font-size: 0.78rem;">
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
                        <th>Nama Klien</th>
                        <th>No. Register</th>
                        <th>Status</th>
                        <th>Tanggal & Jam Bimbingan</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agendaTerdekat as $index => $agenda)
                        @php
                            $stName = strtolower($agenda->statusBimbingan->nama_status ?? '');
                            $badgeClass = match($stName) {
                                'terlaksana' => 'badge-simba-success',
                                'dijadwalkan' => 'badge-simba-info',
                                'absen' => 'badge-simba-danger',
                                default => 'badge-simba-warning'
                            };
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $agenda->klien->nama_klien ?? 'Klien Dihapus' }}</span>
                            </td>
                            <td>
                                <span class="font-monospace text-muted small">{{ $agenda->klien->no_register ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="badge-simba {{ $badgeClass }}">
                                    {{ $agenda->statusBimbingan->nama_status ?? 'Dijadwalkan' }}
                                </span>
                            </td>
                            <td>
                                {{ $agenda->tgl_bimbingan->format('d/m/Y') }} &bull; {{ substr($agenda->jam_mulai, 0, 5) }} WITA
                            </td>
                            <td class="text-center">
                                <a href="{{ route('pk.kehadiran.index', ['search' => $agenda->klien->nama_klien ?? '']) }}" class="btn-action-icon btn-action-view" title="Catat Presensi Kehadiran">
                                    <i class="bi bi-camera-fill"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Tidak ada agenda bimbingan terdekat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
