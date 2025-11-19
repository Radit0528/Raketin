<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // Cast untuk tipe data
    protected $casts = [
        'harga_per_jam' => 'decimal:2',
    ];

    /**
     * Relasi ke Events
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Relasi ke Transactions (Untuk Payment Midtrans)
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'item_id')
                    ->where('tipe_transaksi', 'lapangan');
    }

    /**
     * Get successful transactions only
     */
    public function successfulTransactions(): HasMany
    {
        return $this->transactions()->where('status_pembayaran', 'success');
    }

    /**
     * Get total pendapatan dari lapangan ini
     */
    public function getTotalPendapatanAttribute()
    {
        return $this->successfulTransactions()->sum('total_harga');
    }

    /**
     * Get jumlah booking yang sudah dibayar
     */
    public function getJumlahBookingAttribute()
    {
        return $this->successfulTransactions()->count();
    }

    /**
     * Check apakah lapangan tersedia pada waktu tertentu
     * 
     * @param string $tanggal
     * @param string $jamMulai
     * @param string $jamSelesai
     * @return bool
     */
    public function isAvailable($tanggal, $jamMulai, $jamSelesai)
    {
        $existingBookings = $this->successfulTransactions()
            ->whereJsonContains('metadata->tanggal_booking', $tanggal)
            ->get();

        foreach ($existingBookings as $booking) {
            $bookedStart = $booking->metadata['jam_mulai'] ?? null;
            $bookedEnd = $booking->metadata['jam_selesai'] ?? null;

            if ($bookedStart && $bookedEnd) {
                // Check if time ranges overlap
                if (
                    ($jamMulai >= $bookedStart && $jamMulai < $bookedEnd) ||
                    ($jamSelesai > $bookedStart && $jamSelesai <= $bookedEnd) ||
                    ($jamMulai <= $bookedStart && $jamSelesai >= $bookedEnd)
                ) {
                    return false; // Tidak tersedia (bentrok)
                }
            }
        }

        return true; // Tersedia
    }
}
