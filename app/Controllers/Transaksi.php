<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Transaksi extends BaseController
{
    /**
     * Menangani penyimpanan pesanan dari AJAX Fetch di View Peta Kuliner
     */
    public function simpan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Request harus menggunakan AJAX.'  
            ]);
        }
        
        $json = $this->request->getJSON();
        if (!$json) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data pesanan kosong.']);
        }

        $menuPesanan = json_decode($json->menu_pesanan, true);
        if (empty($menuPesanan)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kamu belum memilih menu apa pun.']);
        }

        // Hitung ulang total bayar (termasuk fee 2% dan pajak 10% dari client)
        $subtotal = 0;
        foreach ($menuPesanan as $item) {
            $subtotal += $item['subtotal'];
        }
        $serviceFee = round($subtotal * 0.02);
        $tax        = round($subtotal * 0.10);
        $totalBayar = $subtotal + $serviceFee + $tax;

        $db = \Config\Database::connect();
        $db->transStart();

        // Siapkan status awal berdasarkan ENUM DB ('pending', 'process')
        // Jika transfer bank masuk ke 'pending' agar form upload bukti transfer di view muncul
        $statusAwal = (strtolower($json->metode_pembayaran) === 'transfer bank') ? 'pending' : 'process';

        // Simpan ke tabel induk: `pesanan` sesuai struktur kolom database asli
        $dataPesanan = [
            'kode_invoice'      => 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)),
            'user_id'           => session()->get('user_id') ?? 1, // Fallback ke ID 1 jika session kosong
            'kuliner_id'        => $json->id_tempat, // id_tempat dari JS dipetakan ke kuliner_id
            'total_bayar'       => $totalBayar,
            'metode_pembayaran' => $json->metode_pembayaran,
            'status_pesanan'    => $statusAwal,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];
        
        $db->table('pesanan')->insert($dataPesanan);
        $pesananId = $db->insertID();

        // Simpan ke tabel anak: `pesanan_detail`
        foreach ($menuPesanan as $item) {
            // Cari menu_id secara dinamis di tabel `menus` berdasarkan nama_menu dan kuliner_id
            $menuData = $db->table('menus')
                           ->where('nama_menu', $item['name'])
                           ->where('kuliner_id', $json->id_tempat)
                           ->get()->getRowArray();

            $menuId = $menuData ? $menuData['id'] : 1; // Gunakan ID menu yang ditemukan

            $db->table('pesanan_detail')->insert([
                'pesanan_id'   => $pesananId,
                'menu_id'      => $menuId,
                'qty'          => $item['quantity'],
                'harga_satuan' => $item['price'],
                'subtotal'     => $item['subtotal']
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan ke database.'])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success'    => true,
            'message'    => 'Pesanan sukses dibuat!',
            'pesanan_id' => $pesananId
        ]);
    }

    /**
     * Menampilkan Halaman Detail Struk / Pesanan setelah checkout sukses
     */
    public function detail($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $db = \Config\Database::connect();

        // 1. Ambil data pesanan induk
        $pesanan = $db->table('pesanan')->where('id', $id)->get()->getRowArray();

        if (!$pesanan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Pesanan tidak ditemukan.");
        }

        // 2. Ambil detail menu (Join ke tabel menus untuk mengambil kolom nama_menu)
        $detail_pesanan = $db->table('pesanan_detail')
            ->select('pesanan_detail.*, menus.nama_menu')
            ->join('menus', 'menus.id = pesanan_detail.menu_id', 'left')
            ->where('pesanan_id', $id)
            ->get()
            ->getResultArray();

        $data = [
            'pesanan'        => $pesanan,
            'detail_pesanan' => $detail_pesanan
        ];

        return view('v_detail_pesanan', $data); 
    }

    /**
     * Mengunggah Bukti Pembayaran
     */
    public function uploadBukti($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $file = $this->request->getFile('bukti_pembayaran');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $namaBaru = $file->getRandomName();
            
            // Simpan gambar di folder public/uploads/bukti/
            $file->move(ROOTPATH . 'public/uploads/bukti', $namaBaru);

            $db = \Config\Database::connect();
            
            // Update nama file bukti dan naikkan status ke 'process' (Menunggu verifikasi merchant)
            $db->table('pesanan')->where('id', $id)->update([
                'bukti_pembayaran' => $namaBaru,
                'status_pesanan'   => 'process' 
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi merchant.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengunggah file bukti pembayaran.'
        ]);
    }
}