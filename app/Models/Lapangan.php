<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    use HasFactory;

    protected $table = 'lapangans'; 
    protected $guarded = ['id'];
    
    // Kolom yang dapat diisi
    protected $fillable = [
        'nama',
        'lokasi',
        'deskripsi',
        'harga_per_jam',
        'gambar', 
    ];
    public function events()
{
    return $this->hasMany(Event::class);
}

}
