@extends('layouts.app')

@section('title', 'Dashboard Pembimbing Kemasyarakatan (PK) - SIMBA')
@section('page_title', 'Dashboard Pembimbing Kemasyarakatan')

@section('content')
<div class="container-fluid px-0">

    <!-- Welcome Hero Banner -->
    <div class="card border-0 mb-4 text-white shadow-sm" style="background: linear-gradient(135deg, #072B1E 0%, #104C35 100%); border-radius: 16px;">
        <div class="card-body p-4 p-lg-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-warning text-dark mb-2 fw-semibold px-3 py-1">
                    <i class="bi bi-person-badge me-1"></i> Panel Pembimbing Kemasyarakatan (PK)
                </span>
                <h4 class="fw-bold mb-1">Selamat Datang, {{ Auth::user()->nama_pegawai }}!</h4>
                <p class="text-white-50 mb-0 small" style="max-width: 620px;">
                    Kelola data pembinaan klien pemasyarakatan, atur jadwal bimbingan berkala, dan dokumentasikan bukti kehadiran wajib lapor dengan terstruktur.
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('pk.jadwal.index') }}" class="btn btn-light text-success fw-semibold btn-sm px-3 shadow-sm">
                    <i class="bi bi-calendar-plus me-1"></i> Jadwal Bimbingan
                </a>
                <a href="{{ route('pk.kehadiran.index') }}" class="btn btn-outline-light fw-semibold btn-sm px-3">
                    <i class="bi bi-camera-fill me-1"></i> Catat Kehadiran
                </a>
            </div>
        </div>
    </div>

    <!-- 3 Metric Cards required by Acceptance Criteria -->
    <div class="row g-3 mb-4">
        <!-- 1. Jadwal Hari Ini -->
        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm h-100 bg-white">
                <div class="card-body p-3 p-md-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider">Jadwal Hari Ini</div>
                        <h2 class="fw-bolder mb-1 mt-2 text-dark">{{ $jadwalHariIni }}</h2>
                        <span class="text-success small fw-medium">
                            <i class="bi bi-calendar-event me-1"></i> Sesi bimbingan aktif hari ini
                        </span>
                    </div>
                    <div class="stat-icon-wrapper bg-success-subtle text-success">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Jadwal Minggu Ini -->
        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm h-100 bg-white">
                <div class="card-body p-3 p-md-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider">Jadwal Minggu Ini</div>
                        <h2 class="fw-bolder mb-1 mt-2 text-dark">{{ $jadwalMingguIni }}</h2>
                        <span class="text-primary small fw-medium">
                            <i class="bi bi-calendar-week me-1"></i> Agenda 7 hari berjalan
                        </span>
                    </div>
                    <div class="stat-icon-wrapper bg-primary-subtle text-primary">
                        <i class="bi bi-calendar-week-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Total Klien -->
        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm h-100 bg-white">
                <div class="card-body p-3 p-md-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase tracking-wider">Total Klien</div>
                        <h2 class="fw-bolder mb-1 mt-2 text-dark">{{ $totalKlien }}</h2>
                        <span class="text-info small fw-medium">
                            <i class="bi bi-people me-1"></i> Klien pemasyarakatan aktif
                        </span>
                    </div>
                    <div class="stat-icon-wrapper bg-info-subtle text-info">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Ringkasan Agenda Bimbingan Klien Terdekat -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-success fs-5"></i>
                <h6 class="mb-0 fw-bold text-dark">Agenda Sesi Bimbingan Terdekat</h6>
            </div>
            <a href="{{ route('pk.jadwal.index') }}" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold text-success">
                Kelola Seluruh Jadwal <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No. Register</th>
                            <th>Nama Klien</th>
                            <th>Tanggal Bimbingan</th>
                            <th>Jam Mulai</th>
                            <th>Status Pelaksanaan</th>
                            <th>Keterangan / Pokok Bimbingan</th>
                            <th class="text-center" style="width: 140px;">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agendaTerdekat as $agenda)
                            <tr>
                                <td>
                                    <span class="fw-bold font-monospace text-dark">{{ $agenda->klien->no_register ?? '-' }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $agenda->klien->nama_klien ?? '-' }}</div>
                                    <small class="text-muted">{{ $agenda->klien->tindak_pidana ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border">
                                        <i class="bi bi-calendar-event me-1"></i> {{ $agenda->tgl_bimbingan->translatedFormat('d F Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">
                                        <i class="bi bi-clock me-1 text-muted"></i> {{ substr($agenda->jam_mulai, 0, 5) }} WITA
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match(strtolower($agenda->statusBimbingan->nama_status ?? '')) {
                                            'terlaksana' => 'bg-success text-white',
                                            'menunggu' => 'bg-warning text-dark',
                                            'absen' => 'bg-danger text-white',
                                            default => 'bg-primary text-white'
                                        };
                                    @endphp
                                    <span class="badge badge-status {{ $statusClass }}">
                                        {{ $agenda->statusBimbingan->nama_status ?? 'Dijadwalkan' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 250px;" title="{{ $agenda->keterangan }}">
                                        {{ $agenda->keterangan ?: 'Bimbingan berkala wajib lapor' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('pk.kehadiran.index', ['search' => $agenda->klien->nama_klien ?? '']) }}" class="btn btn-sm btn-outline-success px-2 py-1 d-inline-flex align-items-center gap-1" title="Catat Kehadiran">
                                        <i class="bi bi-camera-fill"></i>
                                        <span>Presensi</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                    Tidak ada agenda bimbingan klien terdekat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
