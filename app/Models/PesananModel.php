<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananModel extends Model
{
    protected $table            = 'pesanan';
    protected $primaryKey       = 'id'; // Pastikan primary key sesuai dengan migration baru
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    
    // Kolom-kolom baru yang diizinkan untuk diisi (Mass Assignment)
    protected $allowedFields    = [
        'kode_invoice', 
        'user_id', 
        'kuliner_id', 
        'total_bayar', 
        'metode_pembayaran', 
        'status_pesanan',
        'bukti_pembayaran'
    ];

    // Mengaktifkan fitur pencatatan waktu otomatis untuk created_at & updated_at
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}