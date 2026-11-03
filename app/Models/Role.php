<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'role';
    protected $primaryKey = 'id_role';

    protected $fillable = [
        'nama_role',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id_role', 'id_role');
    }
}
