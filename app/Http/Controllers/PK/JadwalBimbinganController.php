<?php

namespace App\Http\Controllers\PK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\JadwalBimbingan;
use App\Models\Klien;
use App\Models\StatusBimbingan;
use App\Models\Pegawai;

class JadwalBimbinganController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalBimbingan::with(['klien', 'statusBimbingan', 'pegawai', 'kehadiran'])->latest('tgl_bimbingan');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                // Cari data klien terkait
                $q->whereHas('klien', function($kq) use ($search) {
                    $kq->where('nama_klien', 'like', "%{$search}%")
                      ->orWhere('no_register', 'like', "%{$search}%")
                      ->orWhere('asal_upt', 'like', "%{$search}%")
                      ->orWhere('tindak_pidana', 'like', "%{$search}%")
                      ->orWhere('jenis_bimbingan', 'like', "%{$search}%")
                      ->orWhere('jenis_kelamin', 'like', "%{$search}%");

                    if (strcasecmp($search, 'PB') === 0 || stripos('Pembebasan Bersyarat', $search) !== false) {
                        $kq->orWhere('jenis_bimbingan', 'like', '%PB%');
                    }
                    if (strcasecmp($search, 'CB') === 0 || stripos('Cuti Bersyarat', $search) !== false) {
                        $kq->orWhere('jenis_bimbingan', 'like', '%CB%');
                    }
                    if (in_array(strtolower($search), ['pria', 'laki', 'laki-laki'])) {
                        $kq->orWhere('jenis_kelamin', 'Laki-laki');
                    } elseif (in_array(strtolower($search), ['wanita', 'perempuan'])) {
                        $kq->orWhere('jenis_kelamin', 'Perempuan');
                    }
                })
                // Cari status bimbingan (Hadir, Terjadwal, Dijadwalkan Ulang, dsb)
                ->orWhereHas('statusBimbingan', function($sq) use ($search) {
                    $sq->where('nama_status', 'like', "%{$search}%");
                })
                // Cari nama pembimbing
                ->orWhereHas('pegawai', function($pq) use ($search) {
                    $pq->where('nama_pegawai', 'like', "%{$search}%");
                })
                // Cari jam mulai dan keterangan
                ->orWhere('jam_mulai', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%");

                // Pencarian tanggal pada kolom tanggal bimbingan
                $this->applyDateSearch($q, 'tgl_bimbingan', $search);
            });
        }

        if ($request->filled('id_status_bimbingan')) {
            $query->where('id_status_bimbingan', $request->id_status_bimbingan);
        }

        $tglFilter = $this->normalizeDateFilter($request->input('tgl_bimbingan') ?: $request->input('tanggal'));
        if (!empty($tglFilter)) {
            $query->whereDate('tgl_bimbingan', $tglFilter);
        }

        $jadwalList = $query->paginate(10)->withQueryString();
        $allKlien = Klien::orderBy('nama_klien')->get();
        $allStatus = StatusBimbingan::all();
        $allPK = Pegawai::where('id_role', 2)->get();

        return view('pk.jadwal.index', compact('jadwalList', 'allKlien', 'allStatus', 'allPK'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_klien' => 'required|exists:klien,id_klien',
            'id_status_bimbingan' => 'required|exists:status_bimbingan,id_status_bimbingan',
            'tgl_bimbingan' => 'required|date',
            'jam_mulai' => 'required',
            'keterangan' => 'nullable|string|max:255',
            'id_pegawai' => 'nullable|exists:pegawai,id_pegawai',
        ], [
            'id_klien.required' => 'Klien wajib dipilih.',
            'id_status_bimbingan.required' => 'Status bimbingan wajib ditentukan.',
            'tgl_bimbingan.required' => 'Tanggal bimbingan wajib diisi.',
            'jam_mulai.required' => 'Jam mulai bimbingan wajib diisi.',
        ]);

        // Default to logged in PK if not explicitly specified
        if (empty($validated['id_pegawai'])) {
            $validated['id_pegawai'] = Auth::id();
        }

        JadwalBimbingan::create($validated);

        return redirect()->route('pk.jadwal.index')->with('success', 'Jadwal sesi bimbingan klien berhasil ditambahkan.');
    }

    public function show($id)
    {
        $jadwal = JadwalBimbingan::with(['klien', 'statusBimbingan', 'pegawai', 'kehadiran'])->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'jadwal' => $jadwal,
                'formatted_date' => $jadwal->tgl_bimbingan->translatedFormat('l, d F Y'),
                'tgl_bimbingan_ymd' => $jadwal->tgl_bimbingan->format('Y-m-d'),
                'tgl_hadir_ymd' => ($jadwal->kehadiran && $jadwal->kehadiran->tgl_hadir) ? $jadwal->kehadiran->tgl_hadir->format('Y-m-d') : null,
                'klien' => $jadwal->klien,
                'status' => $jadwal->statusBimbingan,
                'pegawai' => $jadwal->pegawai,
                'kehadiran' => $jadwal->kehadiran,
                'foto_url' => $jadwal->kehadiran ? $jadwal->kehadiran->foto_url : null,
            ]);
        }

        return view('pk.jadwal.show', compact('jadwal'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalBimbingan::findOrFail($id);

        $validated = $request->validate([
            'id_klien' => 'required|exists:klien,id_klien',
            'id_status_bimbingan' => 'required|exists:status_bimbingan,id_status_bimbingan',
            'tgl_bimbingan' => 'required|date',
            'jam_mulai' => 'required',
            'keterangan' => 'nullable|string|max:255',
            'id_pegawai' => 'nullable|exists:pegawai,id_pegawai',
        ]);

        if (empty($validated['id_pegawai'])) {
            $validated['id_pegawai'] = $jadwal->id_pegawai ?? Auth::id();
        }

        $jadwal->update($validated);

        return redirect()->route('pk.jadwal.index')->with('success', 'Perubahan jadwal bimbingan berhasil disimpan.');
    }

    public function destroy($id)
    {
        $jadwal = JadwalBimbingan::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('pk.jadwal.index')->with('success', 'Jadwal bimbingan berhasil dihapus.');
    }
}
