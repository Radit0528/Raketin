<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    // Menentukan primary key jika namanya bukan 'id' (tapi di migrasi Anda sudah 'id')
    protected $primaryKey = 'id'; 
    protected $table = 'events';

    // Kolom yang dapat diisi melalui mass assignment (sesuai migrasi)
    protected $fillable = [
        // 'court_id',
        // 'organizer_id',
        'nama_event',
        'lokasi',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'biaya_pendaftaran',
        'status',
        'gambar',
    ];

    // Kolom yang harus diubah menjadi tipe data tertentu (misalnya DateTime)
    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];
    public function lapangan()
{
    return $this->belongsTo(Lapangan::class);
}


    // --- RELATIONS ---

    /**
     * Relasi ke Lapangan (Court)
     */
    // public function court(): BelongsTo
    // {
    //     return $this->belongsTo(Lapangan::class, 'court_id');
    // }

    // /**
    //  * Relasi ke User (Organizer)
    //  */
    // public function organizer(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'organizer_id');
    // }
}
