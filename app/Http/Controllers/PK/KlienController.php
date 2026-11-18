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
            $searchNormalized = trim($request->search);
            $cleanPhone = preg_replace('/[^0-9]/', '', $searchNormalized);
            $searchDash = str_replace(' ', '-', $searchNormalized);

            $query->where(function($q) use ($searchNormalized, $searchDash, $cleanPhone) {
                // 1. Nomor Registrasi
                $q->where('no_register', 'like', "%{$searchNormalized}%")
                // 2. Nama Klien
                  ->orWhere('nama_klien', 'like', "%{$searchNormalized}%")
                // 3. Asal UPT
                  ->orWhere('asal_upt', 'like', "%{$searchNormalized}%")
                // 4. Tindak Pidana
                  ->orWhere('tindak_pidana', 'like', "%{$searchNormalized}%")
                // 5. Jenis Bimbingan (PB / CB / teks lengkap)
                  ->orWhere('jenis_bimbingan', 'like', "%{$searchNormalized}%");

                if (strcasecmp($searchNormalized, 'PB') === 0 || stripos('Pembebasan Bersyarat', $searchNormalized) !== false) {
                    $q->orWhere('jenis_bimbingan', 'like', '%PB%');
                }
                if (strcasecmp($searchNormalized, 'CB') === 0 || stripos('Cuti Bersyarat', $searchNormalized) !== false) {
                    $q->orWhere('jenis_bimbingan', 'like', '%CB%');
                }

                // 6. Jenis Kelamin (mendukung "laki laki", "laki-laki", "pria", "perempuan", "wanita")
                $q->orWhere('jenis_kelamin', 'like', "%{$searchNormalized}%")
                  ->orWhere('jenis_kelamin', 'like', "%{$searchDash}%");

                $lower = strtolower($searchNormalized);
                if (in_array($lower, ['laki', 'laki laki', 'laki-laki', 'pria', 'cowok'])) {
                    $q->orWhere('jenis_kelamin', 'Laki-laki');
                } elseif (in_array($lower, ['perempuan', 'wanita', 'cewek'])) {
                    $q->orWhere('jenis_kelamin', 'Perempuan');
                }

                // 7. Alamat & Keterangan
                $q->orWhere('alamat', 'like', "%{$searchNormalized}%")
                  ->orWhere('keterangan', 'like', "%{$searchNormalized}%");

                // 8. Nomor Telepon
                if (!empty($cleanPhone) && strlen($cleanPhone) >= 4) {
                    $q->orWhere('telepon', 'like', "%{$searchNormalized}%")
                      ->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(telepon, ''), '-', ''), ' ', ''), '.', '') LIKE ?", ["%{$cleanPhone}%"]);
                }

                // 9. Pencarian Tanggal pada klien (created_at dan tgl_lahir)
                if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $searchNormalized, $m)) {
                    $dateFormatted = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
                    $q->orWhereDate('created_at', $dateFormatted)
                      ->orWhereDate('tgl_lahir', $dateFormatted);
                } elseif (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $searchNormalized, $m)) {
                    $dateFormatted = sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
                    $q->orWhereDate('created_at', $dateFormatted)
                      ->orWhereDate('tgl_lahir', $dateFormatted);
                } elseif (preg_match('/^(19|20)\d{2}$/', $searchNormalized)) {
                    $q->orWhereRaw("YEAR(created_at) = ?", [$searchNormalized])
                      ->orWhereRaw("YEAR(tgl_lahir) = ?", [$searchNormalized]);
                } else {
                    $months = [
                        'januari' => '01', 'jan' => '01', 'februari' => '02', 'feb' => '02',
                        'maret' => '03', 'mar' => '03', 'april' => '04', 'apr' => '04',
                        'mei' => '05', 'juni' => '06', 'jun' => '06', 'juli' => '07', 'jul' => '07',
                        'agustus' => '08', 'agu' => '08', 'agt' => '08', 'september' => '09', 'sep' => '09',
                        'oktober' => '10', 'okt' => '10', 'november' => '11', 'nov' => '11',
                        'desember' => '12', 'des' => '12',
                    ];
                    foreach ($months as $name => $num) {
                        if (preg_match('/^(\d{1,2})\s+' . $name . '\s+(\d{4})$/i', $lower, $m)) {
                            $f = sprintf('%04d-%02d-%02d', $m[2], $num, $m[1]);
                            $q->orWhereDate('created_at', $f)->orWhereDate('tgl_lahir', $f);
                            break;
                        } elseif ($lower === $name) {
                            $q->orWhereRaw("MONTH(created_at) = ?", [$num])->orWhereRaw("MONTH(tgl_lahir) = ?", [$num]);
                            break;
                        }
                    }
                }
            });
        }

        $klienList = $query->paginate(10)->withQueryString();

        return view('pk.klien.index', compact('klienList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_register' => 'required|string|max:50|unique:klien,no_register',
            'nama_klien' => 'required|string|max:100',
            'asal_upt' => 'required|string|max:150',
            'tindak_pidana' => 'required|string|max:100',
            'jenis_bimbingan' => 'required|in:Pembebasan Bersyarat (PB),Cuti Bersyarat (CB)',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:30',
        ], [
            'no_register.required' => 'Nomor registrasi klien wajib diisi.',
            'no_register.unique' => 'Nomor registrasi klien sudah terdaftar.',
            'nama_klien.required' => 'Nama lengkap klien wajib diisi.',
            'asal_upt.required' => 'Asal UPT wajib diisi.',
            'tindak_pidana.required' => 'Tindak pidana wajib diisi.',
            'jenis_bimbingan.required' => 'Jenis bimbingan wajib dipilih.',
            'jenis_bimbingan.in' => 'Pilihan jenis bimbingan tidak valid.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'alamat.required' => 'Alamat lengkap wajib diisi.',
        ]);

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
            'asal_upt' => 'required|string|max:150',
            'tindak_pidana' => 'required|string|max:100',
            'jenis_bimbingan' => 'required|in:Pembebasan Bersyarat (PB),Cuti Bersyarat (CB)',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:30',
        ], [
            'no_register.required' => 'Nomor registrasi klien wajib diisi.',
            'no_register.unique' => 'Nomor registrasi klien sudah terdaftar.',
            'nama_klien.required' => 'Nama lengkap klien wajib diisi.',
            'asal_upt.required' => 'Asal UPT wajib diisi.',
            'tindak_pidana.required' => 'Tindak pidana wajib diisi.',
            'jenis_bimbingan.required' => 'Jenis bimbingan wajib dipilih.',
            'jenis_bimbingan.in' => 'Pilihan jenis bimbingan tidak valid.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'alamat.required' => 'Alamat lengkap wajib diisi.',
        ]);

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
