<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Relasi ke Event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Relasi ke Lapangan
     */
    public function lapangan(): BelongsTo
    {
        return $this->belongsTo(Lapangan::class, 'lapangan_id');
    }

    /**
     * Cek apakah transaksi berkaitan dengan Event
     */
    public function isEvent(): bool
    {
        return !is_null($this->event_id);
    }

    /**
     * Cek apakah transaksi berkaitan dengan Lapangan
     */
    public function isLapangan(): bool
    {
        return !is_null($this->lapangan_id);
    }

    /**
     * Scope: Upcoming berdasarkan tanggal
     */
    public function scopeUpcoming($query)
    {
        return $query->whereDate('tanggal', '>=', now()->toDateString());
    }

    /**
     * Scope: History berdasarkan tanggal
     */
    public function scopeHistory($query)
    {
        return $query->whereDate('tanggal', '<', now()->toDateString());
    }

}
