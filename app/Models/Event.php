<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    // Menentukan primary key jika namanya bukan 'id' (tapi di migrasi Anda sudah 'id')
    protected $primaryKey = 'id';

    protected $table = 'events';

    // Kolom yang dapat diisi melalui mass assignment (sesuai migrasi)
    protected $fillable = [
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
        'biaya_pendaftaran' => 'decimal:2',
    ];

    /**
     * Relasi ke Lapangan
     */
    public function lapangan(): BelongsTo
    {
        return $this->belongsTo(Lapangan::class);
    }

    /**
     * Relasi ke Transactions (Untuk Payment Midtrans)
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'item_id')
            ->where('tipe_transaksi', 'event');
    }

    /**
     * Get successful transactions only
     */
    public function successfulTransactions(): HasMany
    {
        return $this->transactions()->where('status_pembayaran', 'success');
    }

    /**
     * Get total pendapatan dari event ini
     */
    public function getTotalPendapatanAttribute()
    {
        return $this->successfulTransactions()->sum('total_harga');
    }

    /**
     * Get jumlah peserta yang sudah bayar
     */
    public function getJumlahPesertaAttribute()
    {
        return $this->successfulTransactions()->count();
    }
}
