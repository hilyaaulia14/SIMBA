<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KehadiranKlien extends Model
{
    use HasFactory;

    protected $table = 'kehadiran_klien';
    protected $primaryKey = 'id_kehadiran';

    protected $fillable = [
        'id_jadwal_bimbingan',
        'id_klien',
        'tgl_hadir',
        'jam_hadir',
        'foto_kehadiran',
        'status_kehadiran',
        'catatan',
    ];

    protected $casts = [
        'tgl_hadir' => 'date',
    ];

    public function jadwalBimbingan()
    {
        return $this->belongsTo(JadwalBimbingan::class, 'id_jadwal_bimbingan', 'id_jadwal_bimbingan');
    }

    public function klien()
    {
        return $this->belongsTo(Klien::class, 'id_klien', 'id_klien');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto_kehadiran && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->foto_kehadiran)) {
            return asset('storage/' . $this->foto_kehadiran);
        }
        return null;
    }
}
