<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\SuratKeluar;
use App\Models\Surat;

class SuratKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratKeluar::with('surat')->latest('tgl_keluar');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat', 'like', "%{$search}%")
                  ->orWhere('tujuan_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        if ($request->filled('id_surat')) {
            $query->where('id_surat', $request->id_surat);
        }

        $suratKeluarList = $query->paginate(10)->withQueryString();
        $klasifikasiSurat = Surat::all();

        return view('admin.surat-keluar.index', compact('suratKeluarList', 'klasifikasiSurat'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_surat' => 'required|exists:surat,id_surat',
            'no_surat' => 'required|string|max:50',
            'tujuan_surat' => 'required|string|max:100',
            'tgl_keluar' => 'required|date',
            'perihal' => 'required|string|max:255',
            'upload_dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ], [
            'id_surat.required' => 'Klasifikasi surat wajib dipilih.',
            'no_surat.required' => 'Nomor agenda / surat wajib diisi.',
            'tujuan_surat.required' => 'Tujuan / penerima surat wajib diisi.',
            'tgl_keluar.required' => 'Tanggal keluar wajib diisi.',
            'perihal.required' => 'Perihal surat wajib diisi.',
            'upload_dokumen.max' => 'Ukuran berkas tidak boleh melebihi 10MB.',
            'upload_dokumen.mimes' => 'Format berkas harus PDF, DOC, DOCX, JPG, atau PNG.',
        ]);

        if ($request->hasFile('upload_dokumen')) {
            $file = $request->file('upload_dokumen');
            $filename = 'surat_keluar_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('surat', $filename, 'public');
            $validated['upload_dokumen'] = $path;
        }

        SuratKeluar::create($validated);

        return redirect()->route('admin.surat-keluar.index')->with('success', 'Data Surat Keluar berhasil ditambahkan ke dalam arsip.');
    }

    public function show($id)
    {
        $suratKeluar = SuratKeluar::with('surat')->findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $suratKeluar,
                'dokumen_url' => $suratKeluar->dokumen_url,
                'formatted_date' => $suratKeluar->tgl_keluar->translatedFormat('d F Y'),
            ]);
        }

        return view('admin.surat-keluar.show', compact('suratKeluar'));
    }

    public function update(Request $request, $id)
    {
        $suratKeluar = SuratKeluar::findOrFail($id);

        $validated = $request->validate([
            'id_surat' => 'required|exists:surat,id_surat',
            'no_surat' => 'required|string|max:50',
            'tujuan_surat' => 'required|string|max:100',
            'tgl_keluar' => 'required|date',
            'perihal' => 'required|string|max:255',
            'upload_dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ], [
            'id_surat.required' => 'Klasifikasi surat wajib dipilih.',
            'no_surat.required' => 'Nomor agenda / surat wajib diisi.',
            'tujuan_surat.required' => 'Tujuan / penerima surat wajib diisi.',
            'tgl_keluar.required' => 'Tanggal keluar wajib diisi.',
            'perihal.required' => 'Perihal surat wajib diisi.',
            'upload_dokumen.max' => 'Ukuran berkas tidak boleh melebihi 10MB.',
        ]);

        if ($request->hasFile('upload_dokumen')) {
            if ($suratKeluar->upload_dokumen && Storage::disk('public')->exists($suratKeluar->upload_dokumen)) {
                Storage::disk('public')->delete($suratKeluar->upload_dokumen);
            }

            $file = $request->file('upload_dokumen');
            $filename = 'surat_keluar_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('surat', $filename, 'public');
            $validated['upload_dokumen'] = $path;
        }

        $suratKeluar->update($validated);

        return redirect()->route('admin.surat-keluar.index')->with('success', 'Data Surat Keluar berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $suratKeluar = SuratKeluar::findOrFail($id);

        if ($suratKeluar->upload_dokumen && Storage::disk('public')->exists($suratKeluar->upload_dokumen)) {
            Storage::disk('public')->delete($suratKeluar->upload_dokumen);
        }

        $suratKeluar->delete();

        return redirect()->route('admin.surat-keluar.index')->with('success', 'Data Surat Keluar berhasil dihapus.');
    }

    public function download($id)
    {
        $suratKeluar = SuratKeluar::findOrFail($id);

        if (!$suratKeluar->upload_dokumen || !Storage::disk('public')->exists($suratKeluar->upload_dokumen)) {
            return back()->with('error', 'Berkas digital surat ini belum diunggah atau tidak ditemukan.');
        }

        return Storage::disk('public')->download($suratKeluar->upload_dokumen);
    }
}
