@extends('layouts.app')

@section('title', 'Dashboard Pembimbing - SIMBA')

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row">
        <div>
            <h2 class="page-title-main">Dashboard</h2>
            <p class="page-subtitle-main">Ringkasan Jadwal Bimbingan Anda Hari Ini.</p>
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
                <div class="metric-card-label">Jadwal Hari Ini</div>
                <div class="metric-card-number">{{ $jadwalHariIni }}</div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="metric-card-figma">
                <div class="metric-card-label">Jadwal Minggu Ini</div>
                <div class="metric-card-number">{{ $jadwalMingguIni }}</div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="metric-card-figma">
                <div class="metric-card-label">Total Klien</div>
                <div class="metric-card-number">{{ $totalKlien }}</div>
            </div>
        </div>
    </div>

    <!-- Jadwal Bimbingan Hari Ini Card -->
    <div class="table-card-figma">
        <div class="table-card-header">
            <div>
                <h5 class="table-card-title">Jadwal Bimbingan Hari Ini</h5>
                <p class="table-card-subtitle">{{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}</p>
            </div>
            <div>
                <a href="{{ route('pk.atur-jadwal.index') }}" class="btn-simba-primary">
                    <i class="bi bi-plus-lg"></i>
                    <span>Buat Jadwal</span>
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-simba">
                <thead>
                    <tr>
                        <th style="width: 18%;">Jam</th>
                        <th style="width: 32%;">Nama Klien</th>
                        <th style="width: 22%;">Status</th>
                        <th style="width: 28%;">Nama Pembimbing</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agendaTerdekat as $agenda)
                        @php
                            $stName = strtolower($agenda->statusBimbingan->nama_status ?? '');
                            $statusClass = match(true) {
                                str_contains($stName, 'hadir') && !str_contains($stName, 'tidak') => 'status-pill-hadir',
                                str_contains($stName, 'ulang') => 'status-pill-dijadwalkan-ulang',
                                str_contains($stName, 'tidak') || str_contains($stName, 'absen') => 'status-pill-tidak-hadir',
                                default => 'status-pill-terjadwal'
                            };
                            $displayStatus = match(true) {
                                str_contains($stName, 'hadir') && !str_contains($stName, 'tidak') => 'Hadir',
                                str_contains($stName, 'ulang') => 'Dijadwalkan Ulang',
                                str_contains($stName, 'tidak') || str_contains($stName, 'absen') => 'Tidak Hadir',
                                default => 'Terjadwal'
                            };
                        @endphp
                        <tr>
                            <td>{{ substr($agenda->jam_mulai, 0, 5) }}</td>
                            <td>{{ $agenda->klien->nama_klien ?? '-' }}</td>
                            <td>
                                <span class="{{ $statusClass }}">
                                    ● {{ $displayStatus }}
                                </span>
                            </td>
                            <td>{{ $agenda->pegawai->nama_pegawai ?? 'Ahmad Fauzan, S.H.' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                Tidak ada agenda jadwal bimbingan untuk hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
