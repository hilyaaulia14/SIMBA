<?php

namespace App\Http\Controllers\PK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Klien;

class KlienController extends Controller
{
    public function index(Request $request)
    {
        $query = Klien::with(['aturJadwal', 'jadwalBimbingan.pegawai'])->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $cleanPhone = preg_replace('/[^0-9]/', '', $search);
            $currentUser = auth()->user();
            $matchesCurrentPK = $currentUser && !empty($currentUser->nama_pegawai) && (stripos($currentUser->nama_pegawai, $search) !== false);

            $query->where(function($q) use ($search, $cleanPhone, $matchesCurrentPK) {
                // 1. Nomor Registrasi
                $q->where('no_register', 'like', "%{$search}%")
                // 2. Nama Klien
                  ->orWhere('nama_klien', 'like', "%{$search}%")
                // 3. Nomor Telepon (format teks biasa maupun angka saja)
                  ->orWhere('telepon', 'like', "%{$search}%")
                // 4. Tindak Pidana
                  ->orWhere('tindak_pidana', 'like', "%{$search}%");

                if (!empty($cleanPhone)) {
                    $q->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(telepon, ''), '-', ''), ' ', ''), '.', '') LIKE ?", ["%{$cleanPhone}%"]);
                }

                // 5. Nama Pembimbing (dari relasi jadwal bimbingan)
                $q->orWhereHas('jadwalBimbingan.pegawai', function($pq) use ($search) {
                    $pq->where('nama_pegawai', 'like', "%{$search}%");
                });

                // Jika nama pembimbing yang sedang login cocok dengan kata kunci pencarian
                if ($matchesCurrentPK) {
                    $q->orWhereRaw('1 = 1');
                }
            });
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $klienList = $query->paginate(10)->withQueryString();

        return view('pk.klien.index', compact('klienList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_register' => 'required|string|max:50|unique:klien,no_register',
            'nama_klien' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:30',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'tgl_lahir' => 'nullable|date',
            'jam_mulai' => 'nullable',
            'tindak_pidana' => 'required|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'no_register.required' => 'Nomor registrasi klien wajib diisi.',
            'no_register.unique' => 'Nomor registrasi klien sudah terdaftar.',
            'nama_klien.required' => 'Nama lengkap klien wajib diisi.',
            'tindak_pidana.required' => 'Perkara / tindak pidana wajib diisi.',
        ]);

        if (empty($validated['jenis_kelamin'])) {
            $validated['jenis_kelamin'] = 'Laki-laki';
        }

        Klien::create($validated);

        return redirect()->route('pk.klien.index')->with('success', 'Data Klien Pemasyarakatan berhasil didaftarkan.');
    }

    public function show($id)
    {
        $klien = Klien::with([
            'aturJadwal',
            'jadwalBimbingan.statusBimbingan',
            'jadwalBimbingan.pegawai',
            'kehadiranKlien'
        ])->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'klien' => $klien,
                'formatted_tgl_lahir' => $klien->tgl_lahir ? $klien->tgl_lahir->translatedFormat('d F Y') : '-',
                'atur_jadwal' => $klien->aturJadwal,
                'riwayat_bimbingan' => $klien->jadwalBimbingan,
                'riwayat_kehadiran' => $klien->kehadiranKlien,
            ]);
        }

        return view('pk.klien.show', compact('klien'));
    }

    public function update(Request $request, $id)
    {
        $klien = Klien::findOrFail($id);

        $validated = $request->validate([
            'no_register' => 'required|string|max:50|unique:klien,no_register,' . $klien->id_klien . ',id_klien',
            'nama_klien' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:30',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'tgl_lahir' => 'nullable|date',
            'jam_mulai' => 'nullable',
            'tindak_pidana' => 'required|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'no_register.required' => 'Nomor registrasi klien wajib diisi.',
            'no_register.unique' => 'Nomor registrasi klien sudah terdaftar.',
            'nama_klien.required' => 'Nama lengkap klien wajib diisi.',
            'tindak_pidana.required' => 'Perkara / tindak pidana wajib diisi.',
        ]);

        if (empty($validated['jenis_kelamin'])) {
            $validated['jenis_kelamin'] = $klien->jenis_kelamin ?? 'Laki-laki';
        }

        $klien->update($validated);

        return redirect()->route('pk.klien.index')->with('success', 'Data Klien Pemasyarakatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $klien = Klien::findOrFail($id);
        $klien->delete();

        return redirect()->route('pk.klien.index')->with('success', 'Data Klien berhasil dihapus dari sistem.');
    }
}
