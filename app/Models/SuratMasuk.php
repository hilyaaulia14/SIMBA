<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $table = 'surat_masuk';
    protected $primaryKey = 'id_surat_masuk';

    protected $fillable = [
        'id_surat',
        'no_surat',
        'asal_surat',
        'tgl_masuk',
        'perihal',
        'upload_dokumen',
    ];

    protected $casts = [
        'tgl_masuk' => 'date',
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class, 'id_surat', 'id_surat');
    }

    public function getDokumenUrlAttribute(): ?string
    {
        return $this->upload_dokumen ? asset('storage/' . $this->upload_dokumen) : null;
    }
}
