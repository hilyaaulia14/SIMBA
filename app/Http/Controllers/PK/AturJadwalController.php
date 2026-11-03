<?php

namespace App\Http\Controllers\PK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AturJadwal;
use App\Models\Klien;

class AturJadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = AturJadwal::with('klien')->latest();

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('klien', function($q) use ($search) {
                $q->where('nama_klien', 'like', "%{$search}%")
                  ->orWhere('no_register', 'like', "%{$search}%");
            });
        }

        $aturJadwalList = $query->paginate(10)->withQueryString();
        
        // Clients without a schedule rule yet or for selection
        $allKlien = Klien::orderBy('nama_klien')->get();
        $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        return view('pk.atur-jadwal.index', compact('aturJadwalList', 'allKlien', 'daftarHari'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_klien' => 'required|exists:klien,id_klien',
            'hari' => 'nullable|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'tgl_bimbingan' => 'nullable|date',
            'jam_mulai' => 'required',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'id_klien.required' => 'Klien wajib dipilih.',
            'jam_mulai.required' => 'Waktu bimbingan wajib ditentukan.',
        ]);

        $hari = $validated['hari'] ?? null;
        if (!$hari && !empty($validated['tgl_bimbingan'])) {
            $dayName = \Carbon\Carbon::parse($validated['tgl_bimbingan'])->locale('id')->isoFormat('dddd');
            $hari = in_array($dayName, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']) ? $dayName : 'Senin';
        }

        if (!$hari) {
            $hari = 'Senin';
        }

        // Set rule in atur_jadwal
        AturJadwal::updateOrCreate(
            ['id_klien' => $validated['id_klien']],
            ['hari' => $hari, 'jam_mulai' => $validated['jam_mulai']]
        );

        // Update Klien jam_mulai
        Klien::where('id_klien', $validated['id_klien'])->update([
            'jam_mulai' => $validated['jam_mulai'],
        ]);

        // If date is provided, also create session in jadwal_bimbingan
        if (!empty($validated['tgl_bimbingan'])) {
            \App\Models\JadwalBimbingan::create([
                'id_pegawai' => \Illuminate\Support\Facades\Auth::id() ?? 2,
                'id_klien' => $validated['id_klien'],
                'id_status_bimbingan' => 4, // Terjadwal
                'tgl_bimbingan' => $validated['tgl_bimbingan'],
                'jam_mulai' => $validated['jam_mulai'],
                'keterangan' => $validated['keterangan'] ?? 'Bimbingan rutin klien pemasyarakatan',
            ]);
        }

        return redirect()->route('pk.atur-jadwal.index')->with('success', 'Jadwal sesi bimbingan klien berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $aturJadwal = AturJadwal::findOrFail($id);

        $validated = $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_mulai' => 'required',
        ], [
            'hari.required' => 'Hari bimbingan wajib dipilih.',
            'jam_mulai.required' => 'Jam mulai bimbingan wajib ditentukan.',
        ]);

        $aturJadwal->update($validated);

        if ($aturJadwal->id_klien) {
            Klien::where('id_klien', $aturJadwal->id_klien)->update([
                'jam_mulai' => $validated['jam_mulai'],
            ]);
        }

        return redirect()->route('pk.atur-jadwal.index')->with('success', 'Pengaturan jadwal bimbingan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $aturJadwal = AturJadwal::findOrFail($id);
        $aturJadwal->delete();

        return redirect()->route('pk.atur-jadwal.index')->with('success', 'Pengaturan jadwal bimbingan klien berhasil dihapus.');
    }
}
