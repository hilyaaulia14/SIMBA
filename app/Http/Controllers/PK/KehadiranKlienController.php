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
            $search = $request->search;
            $query->whereHas('klien', function($q) use ($search) {
                $q->where('nama_klien', 'like', "%{$search}%")
                  ->orWhere('no_register', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tgl_hadir')) {
            $query->whereDate('tgl_hadir', $request->tgl_hadir);
        }

        $kehadiranList = $query->paginate(10)->withQueryString();
        $allKlien = Klien::orderBy('nama_klien')->get();
        $recentJadwal = JadwalBimbingan::with('klien')->latest('tgl_bimbingan')->take(20)->get();

        return view('pk.kehadiran.index', compact('kehadiranList', 'allKlien', 'recentJadwal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_klien' => 'required|exists:klien,id_klien',
            'id_jadwal_bimbingan' => 'nullable|exists:jadwal_bimbingan,id_jadwal_bimbingan',
            'tgl_hadir' => 'required|date',
            'jam_hadir' => 'required',
            'foto_kehadiran' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'catatan' => 'required|string|max:255',
        ], [
            'id_klien.required' => 'Klien wajib dipilih.',
            'tgl_hadir.required' => 'Tanggal hadir wajib diisi.',
            'jam_hadir.required' => 'Jam kedatangan wajib diisi.',
            'catatan.required' => 'Catatan perkembangan kepribadian/kemandirian wajib diisi.',
            'foto_kehadiran.image' => 'Berkas bukti kehadiran harus berupa foto gambar.',
            'foto_kehadiran.max' => 'Ukuran foto maksimal 5MB.',
        ]);

        if ($request->hasFile('foto_kehadiran')) {
            $file = $request->file('foto_kehadiran');
            $filename = 'kehadiran_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('kehadiran', $filename, 'public');
            $validated['foto_kehadiran'] = $path;
        }

        $kehadiran = KehadiranKlien::create($validated);

        // If linked to a scheduled session, mark status as 'Terlaksana' (id = 2)
        if (!empty($validated['id_jadwal_bimbingan'])) {
            JadwalBimbingan::where('id_jadwal_bimbingan', $validated['id_jadwal_bimbingan'])
                ->update(['id_status_bimbingan' => 2]);
        }

        return redirect()->route('pk.kehadiran.index')->with('success', 'Bukti kehadiran wajib lapor klien berhasil dicatat.');
    }

    public function show($id)
    {
        $kehadiran = KehadiranKlien::with(['klien', 'jadwalBimbingan'])->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'kehadiran' => $kehadiran,
                'foto_url' => $kehadiran->foto_url,
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
            'foto_kehadiran' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'catatan' => 'required|string|max:255',
        ]);

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
