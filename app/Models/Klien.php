<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klien extends Model
{
    use HasFactory;

    protected $table = 'klien';
    protected $primaryKey = 'id_klien';

    protected $fillable = [
        'no_register',
        'nama_klien',
        'jenis_kelamin',
        'tgl_lahir',
        'jam_mulai',
        'tindak_pidana',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    public function aturJadwal()
    {
        return $this->hasOne(AturJadwal::class, 'id_klien', 'id_klien');
    }

    public function jadwalBimbingan()
    {
        return $this->hasMany(JadwalBimbingan::class, 'id_klien', 'id_klien');
    }

    public function kehadiranKlien()
    {
        return $this->hasMany(KehadiranKlien::class, 'id_klien', 'id_klien');
    }
}
