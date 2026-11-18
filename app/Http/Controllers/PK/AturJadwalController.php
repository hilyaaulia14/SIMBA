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
            'id_klien' => 'required',
            'hari' => 'nullable|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'tgl_bimbingan' => 'nullable|date',
            'jam_mulai' => 'required',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'id_klien.required' => 'Klien wajib dipilih atau diketik.',
            'jam_mulai.required' => 'Waktu bimbingan wajib ditentukan.',
        ]);

        $klienInput = trim($request->input('id_klien'));
        $klien = null;

        // 1. Cek jika input berupa ID numerik
        if (is_numeric($klienInput)) {
            $klien = Klien::find($klienInput);
        }

        // 2. Cek jika input teks mengandung nomor registrasi dalam kurung, misal: "Ahmad Supriyadi (REG/2026/BPS/001)"
        if (!$klien && preg_match('/\((.*?)\)/', $klienInput, $matches)) {
            $noReg = trim($matches[1]);
            $klien = Klien::where('no_register', $noReg)->first();
        }

        // 3. Cari berdasarkan nama_klien atau no_register dari data klien yang ada
        if (!$klien) {
            $klien = Klien::where('no_register', $klienInput)
                ->orWhere('nama_klien', $klienInput)
                ->orWhere('nama_klien', 'like', "%{$klienInput}%")
                ->orWhere('no_register', 'like', "%{$klienInput}%")
                ->first();
        }

        // 4. Jika klien tidak ditemukan di dalam Data Klien, tolak dan beri tahu pengguna
        if (!$klien) {
            return back()->withInput()->with('error', 'Klien "' . e($klienInput) . '" tidak ditemukan dalam Data Klien. Silakan pilih klien yang sudah terdaftar.');
        }

        $idKlien = $klien->id_klien;

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
            ['id_klien' => $idKlien],
            ['hari' => $hari, 'jam_mulai' => $validated['jam_mulai']]
        );

        // Update Klien jam_mulai
        Klien::where('id_klien', $idKlien)->update([
            'jam_mulai' => $validated['jam_mulai'],
        ]);

        // If date is provided, also create session in jadwal_bimbingan
        if (!empty($validated['tgl_bimbingan'])) {
            \App\Models\JadwalBimbingan::create([
                'id_pegawai' => \Illuminate\Support\Facades\Auth::id() ?? 2,
                'id_klien' => $idKlien,
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
