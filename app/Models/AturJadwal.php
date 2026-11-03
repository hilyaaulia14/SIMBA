<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AturJadwal extends Model
{
    use HasFactory;

    protected $table = 'atur_jadwal';
    protected $primaryKey = 'id_atur_jadwal';

    protected $fillable = [
        'id_klien',
        'hari',
        'jam_mulai',
    ];

    public function klien()
    {
        return $this->belongsTo(Klien::class, 'id_klien', 'id_klien');
    }
}
