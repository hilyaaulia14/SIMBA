<?php

namespace App\Http\Controllers\PK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\KehadiranKlien;
use App\Models\Klien;
use App\Models\JadwalBimbingan;

class KehadiranKlienController extends Controller
{
    public function index(Request $request)
    {
        $query = KehadiranKlien::with(['klien', 'jadwalBimbingan.statusBimbingan'])->latest('tgl_hadir');

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
                // Cari status kehadiran, catatan evaluasi, dan jam hadir
                ->orWhere('status_kehadiran', 'like', "%{$search}%")
                ->orWhere('catatan', 'like', "%{$search}%")
                ->orWhere('jam_hadir', 'like', "%{$search}%");

                // Pencarian tanggal pada kolom tanggal kehadiran
                $this->applyDateSearch($q, 'tgl_hadir', $search);
            });
        }

        $tglFilter = $this->normalizeDateFilter($request->input('tgl_hadir') ?: $request->input('tanggal'));
        if (!empty($tglFilter)) {
            $query->whereDate('tgl_hadir', $tglFilter);
        }

        $kehadiranList = $query->paginate(10)->withQueryString();
        $allKlien = Klien::orderBy('nama_klien')->get();
        $recentJadwal = JadwalBimbingan::with('klien')->latest('tgl_bimbingan')->take(20)->get();
        $allStatus = \App\Models\StatusBimbingan::all();

        return view('pk.kehadiran.index', compact('kehadiranList', 'allKlien', 'recentJadwal', 'allStatus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_klien' => 'required|exists:klien,id_klien',
            'id_jadwal_bimbingan' => 'nullable|exists:jadwal_bimbingan,id_jadwal_bimbingan',
            'tgl_hadir' => 'required|date',
            'jam_hadir' => 'required',
            'status_kehadiran' => 'nullable|string|max:50',
            'foto_kehadiran' => 'nullable|image|mimes:jpeg,svg,jpg,webp|max:5120',
            'catatan' => 'required|string|max:255',
        ], [
            'id_klien.required' => 'Klien wajib dipilih.',
            'tgl_hadir.required' => 'Tanggal hadir wajib diisi.',
            'jam_hadir.required' => 'Jam kedatangan wajib diisi.',
            'catatan.required' => 'Catatan keterangan / evaluasi kehadiran wajib diisi.',
            'foto_kehadiran.image' => 'Berkas bukti kehadiran harus berupa foto gambar.',
            'foto_kehadiran.max' => 'Ukuran foto maksimal 5MB.',
        ]);

        if (empty($validated['status_kehadiran'])) {
            $validated['status_kehadiran'] = 'Hadir';
        }

        if ($request->hasFile('foto_kehadiran')) {
            $file = $request->file('foto_kehadiran');
            $filename = 'kehadiran_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('kehadiran', $filename, 'public');
            $validated['foto_kehadiran'] = $path;
        }

        // Auto link schedule if not provided
        if (empty($validated['id_jadwal_bimbingan'])) {
            $matchingJadwal = JadwalBimbingan::where('id_klien', $validated['id_klien'])
                ->whereDate('tgl_bimbingan', $validated['tgl_hadir'])
                ->first();
            if ($matchingJadwal) {
                $validated['id_jadwal_bimbingan'] = $matchingJadwal->id_jadwal_bimbingan;
            }
        }

        $kehadiran = KehadiranKlien::create($validated);

        // Map and update status in jadwal_bimbingan if linked
        if (!empty($validated['id_jadwal_bimbingan'])) {
            $statusMap = [
                'Hadir' => 2,
                'Dijadwalkan Ulang' => 1,
                'Tidak Hadir' => 3,
                'Terjadwal' => 4,
            ];
            $statusId = $statusMap[$validated['status_kehadiran']] ?? 2;
            JadwalBimbingan::where('id_jadwal_bimbingan', $validated['id_jadwal_bimbingan'])
                ->update(['id_status_bimbingan' => $statusId]);
        }

        return redirect()->route('pk.kehadiran.index')->with('success', 'Bukti kehadiran wajib lapor klien berhasil dicatat.');
    }

    public function show($id)
    {
        $kehadiran = KehadiranKlien::with(['klien', 'jadwalBimbingan.statusBimbingan'])->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'kehadiran' => $kehadiran,
                'foto_url' => $kehadiran->foto_url,
                'status_kehadiran' => $kehadiran->status_kehadiran ?? ($kehadiran->jadwalBimbingan->statusBimbingan->nama_status ?? 'Hadir'),
                'formatted_tgl' => $kehadiran->tgl_hadir->translatedFormat('l, d F Y'),
                'klien' => $kehadiran->klien,
                'jadwal' => $kehadiran->jadwalBimbingan,
            ]);
        }

        return view('pk.kehadiran.show', compact('kehadiran'));
    }

    public function update(Request $request, $id)
    {
        $kehadiran = KehadiranKlien::findOrFail($id);

        $validated = $request->validate([
            'id_klien' => 'required|exists:klien,id_klien',
            'id_jadwal_bimbingan' => 'nullable|exists:jadwal_bimbingan,id_jadwal_bimbingan',
            'tgl_hadir' => 'required|date',
            'jam_hadir' => 'required',
            'status_kehadiran' => 'nullable|string|max:50',
            'foto_kehadiran' => 'nullable|image|mimes:jpeg,svg,jpg,webp|max:5120',
            'catatan' => 'required|string|max:255',
        ]);

        if (empty($validated['status_kehadiran'])) {
            $validated['status_kehadiran'] = $kehadiran->status_kehadiran ?? 'Hadir';
        }

        if ($request->hasFile('foto_kehadiran')) {
            if ($kehadiran->foto_kehadiran && Storage::disk('public')->exists($kehadiran->foto_kehadiran)) {
                Storage::disk('public')->delete($kehadiran->foto_kehadiran);
            }

            $file = $request->file('foto_kehadiran');
            $filename = 'kehadiran_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('kehadiran', $filename, 'public');
            $validated['foto_kehadiran'] = $path;
        }

        $kehadiran->update($validated);

        if (!empty($validated['id_jadwal_bimbingan'])) {
            $statusMap = [
                'Hadir' => 2,
                'Dijadwalkan Ulang' => 1,
                'Tidak Hadir' => 3,
                'Terjadwal' => 4,
            ];
            $statusId = $statusMap[$validated['status_kehadiran']] ?? 2;
            JadwalBimbingan::where('id_jadwal_bimbingan', $validated['id_jadwal_bimbingan'])
                ->update(['id_status_bimbingan' => $statusId]);
        }

        return redirect()->route('pk.kehadiran.index')->with('success', 'Data presensi kehadiran klien berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kehadiran = KehadiranKlien::findOrFail($id);

        if ($kehadiran->foto_kehadiran && Storage::disk('public')->exists($kehadiran->foto_kehadiran)) {
            Storage::disk('public')->delete($kehadiran->foto_kehadiran);
        }

        $kehadiran->delete();

        return redirect()->route('pk.kehadiran.index')->with('success', 'Catatan kehadiran berhasil dihapus.');
    }
}
