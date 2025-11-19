<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'event_id',
        'lapangan_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'durasi',
        'nama',
        'email',
        'phone',
        'amount',
        'status_pembayaran',
    ];

    // Relasi ke Event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Relasi ke Lapangan
    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class);
    }

    // Check apakah transaksi ini event
    public function isEvent()
    {
        return !is_null($this->event_id);
    }

    // Check apakah transaksi ini lapangan
    public function isLapangan()
    {
        return !is_null($this->lapangan_id);
    }
}
