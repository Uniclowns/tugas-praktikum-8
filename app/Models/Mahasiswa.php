<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $primaryKey = 'NIM';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'NIM',
        'nama',
        'jurusan',
        'prodi',
        'alamat',
        'ttl',
        'no_hp',
    ];
}
