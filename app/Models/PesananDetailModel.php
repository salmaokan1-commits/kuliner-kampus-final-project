<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananDetailModel extends Model
{
    protected $table            = 'pesanan_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Kolom detail transaksi
    protected $allowedFields    = [
        'pesanan_id', 
        'menu_id', 
        'qty', 
        'harga_satuan', 
        'subtotal'
    ];

    // Tabel detail biasanya tidak memerlukan timestamps mandiri karena ikut tabel induknya
    protected $useTimestamps = false; 
}