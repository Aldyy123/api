<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyModel extends Model
{
    use HasFactory;
    protected $table = 'data_family';
    protected $primaryKey = 'id';
    protected $fillable = [
        'father', 'mother', 'address',
        'kecamatan', 'kelurahan', 'dusun',
        'phone', 'nisn_siswa'
    ];

    public function siswa()
    {
       return $this->belongsTo(SiswaModel::class, 'nisn_siswa', 'nisn');
    }
}
