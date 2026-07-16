<?php

namespace App\Controllers;

use App\Models\KulinerModel;
use App\Models\MenuModel;
use App\Models\PesananDetailModel;
use App\Models\PesananModel;
use App\Services\OrderService;

class Pesanan extends BaseController
{
public function simpan()
    {
        // 1. Cek apakah user sudah login
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ]);
        }

        $model = new PesananModel();
        $menuModel = new MenuModel();
        $detailModel = new PesananDetailModel();

        // Mengambil data item dari POST atau JSON payload
        $isJson = $this->request->isAJAX() || strpos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false;
        
        if ($isJson) {
            $jsonData = $this->request->getJSON(true) ?? [];
            $menuPesananRaw = $jsonData['menu_pesanan'] ?? '';
            $id_tempat = $jsonData['id_tempat'] ?? '';
            $metode_pembayaran = $jsonData['metode_pembayaran'] ?? '';
        } else {
            $menuPesananRaw = $this->request->getPost('menu_pesanan');
            $id_tempat = $this->request->getPost('id_tempat');
            $metode_pembayaran = $this->request->getPost('metode_pembayaran');
        }

        $menuItems = is_string($menuPesananRaw) ? json_decode($menuPesananRaw, true) : $menuPesananRaw;

        if (!is_array($menuItems) || empty($menuItems)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Menu pesanan tidak valid. Pastikan Anda menambahkan item menu terlebih dahulu.'
            ]);
        }

        $subtotal = 0;
        $detailPesanan = [];

        // 2. Validasi & Hitung Berdasarkan Data Database (Proteksi Harga)
        foreach ($menuItems as $item) {
            $menuId = 0;
            $menu = null;

            // Cari ID Menu (Menggunakan logika bawaan kodemu sebelumnya)
            if (isset($item['id']) && is_numeric($item['id'])) {
                $menuId = (int) $item['id'];
                $menu = $menuModel->find($menuId);
            } elseif (!empty($id_tempat) && isset($item['name'])) {
                $kulinerId = (int) $id_tempat;
                $menu = $menuModel->findByNameAndKuliner($item['name'], $kulinerId);
                $menuId = $menu ? (int) $menu['id'] : 0;
            }

            if (!$menu) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Menu "' . ($item['name'] ?? 'Tidak Diketahui') . '" tidak ditemukan.'
                ]);
            }

            $qty = (int) $item['quantity'];
            if ($qty <= 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Jumlah item harus valid.'
                ]);
            }

            // Ambil harga resmi dari database, bukan dari input form HTML
            $hargaSatuan = (float) $menu['harga']; 
            $itemSubtotal = $hargaSatuan * $qty;
            $subtotal += $itemSubtotal;

            // Tampung data untuk insert ke tabel pesanan_detail nanti
            $detailPesanan[] = [
                'menu_id'      => $menuId,
                'qty'          => $qty,
                'harga_satuan' => $hargaSatuan,
                'subtotal'     => $itemSubtotal
            ];
        }

        // Logika perhitungan pajak & fee bawaan sistemmu
        $serviceFee = (int) round($subtotal * 0.02);
        $tax = (int) round($subtotal * 0.10);
        $totalBayar = $subtotal + $serviceFee + $tax;

        // Generate Kode Invoice Unik untuk struktur 3NF
        $kodeInvoice = 'INV-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));

        // 3. PROSES DATABASE TRANSACTION (Pondasi Aman 3NF)
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Data untuk Tabel Master (pesanan) - Hanya kolom yang ada di migration baru
            $dataMaster = [
                'kode_invoice'      => $kodeInvoice,
                'user_id'           => session()->get('id_user') ?? session()->get('id'),
                'kuliner_id'        => $id_tempat, // id_tempat bertindak sebagai kuliner_id
                'total_bayar'       => max(0, $totalBayar),
                'metode_pembayaran' => $metode_pembayaran,
                'status_pesanan'    => 'pending', // Status awal di set 'pending' sesuai ENUM migration
            ];

            // Insert ke tabel master pesanan
            $model->insert($dataMaster);
            $pesanan_id = $model->getInsertID();

            // Insert ke tabel detail pesanan_detail menggunakan looping
            foreach ($detailPesanan as $detail) {
                $detail['pesanan_id'] = $pesanan_id; // Suntikkan ID master yang baru lahir
                $detailModel->insert($detail);
            }

            // Cek jika ada kegagalan query di salah satu tabel
            if ($db->transStatus() === false) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses pesanan karena gangguan relasi database.'
                ]);
            }

            // Jika semua sukses, simpan permanen!
            $db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat (Struktur 3NF aktif)',
                'pesanan_id' => $pesanan_id
            ]);

        } catch (\Exception $e) {
            // Jika crash/error, batalkan semua agar database tidak korup
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan pesanan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menyelesaikan pesanan kuliner
     * @param int $id ID dari Pesanan
     */
    public function complete(int $id)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ]);
        }

        $model = new PesananModel();
        $service = new OrderService();

        $pesanan = $model->find($id);

        if (! $pesanan) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.'
            ])->setStatusCode(404);
        }

        if (strtolower($pesanan['status_pesanan']) === 'completed') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pesanan sudah selesai.'
            ]);
        }

        $statusUpdated = $model->update($id, ['status_pesanan' => 'completed']);
        $walletCredited = $service->processOrderCompletion($id);

        return $this->response->setJSON([
            'success' => (bool) $statusUpdated,
            'message' => $statusUpdated ? 'Pesanan telah diselesaikan.' : 'Gagal menyelesaikan pesanan.',
            'walletCredited' => $walletCredited,
        ]);
    }

    public function requestWithdrawal()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'merchant') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya merchant yang dapat mengajukan penarikan.'
            ])->setStatusCode(403);
        }

        $merchantId = session()->get('id') ?? session()->get('id_user');
        $jumlahTarik = (float) $this->request->getPost('jumlah_tarik');
        $bankData = [
            'bank_tujuan' => $this->request->getPost('bank_tujuan'),
            'nomor_rekening' => $this->request->getPost('nomor_rekening'),
            'nama_rekening' => $this->request->getPost('nama_rekening'),
        ];

        $service = new OrderService();
        $withdrawalId = $service->createWithdrawalRequest((int) $merchantId, $jumlahTarik, $bankData);

        if (! $withdrawalId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Saldo tidak mencukupi atau data penarikan tidak valid.'
            ])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Permintaan penarikan berhasil diajukan.',
            'withdrawal_id' => $withdrawalId,
        ]);
    }

    /**
     * Menyetujui penarikan saldo merchant
     * @param int $id ID dari Withdrawal
     */
    public function approveWithdrawal(int $id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'developer') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya developer yang bisa menyetujui penarikan.'
            ])->setStatusCode(403);
        }

        $service = new OrderService();
        $approved = $service->approveWithdrawal($id);

        return $this->response->setJSON([
            'success' => $approved,
            'message' => $approved ? 'Penarikan disetujui.' : 'Gagal menyetujui penarikan.'
        ]);
    }

    /**
     * Menolak penarikan saldo merchant
     * @param int $id ID dari Withdrawal
     */
    public function rejectWithdrawal(int $id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'developer') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak. Hanya developer yang bisa menolak penarikan.'
            ])->setStatusCode(403);
        }

        $service = new OrderService();
        $rejected = $service->rejectWithdrawal($id);

        return $this->response->setJSON([
            'success' => $rejected,
            'message' => $rejected ? 'Penarikan ditolak.' : 'Gagal menolak penarikan.'
        ]);
    }

    /**
     * Menampilkan daftar riwayat pesanan untuk pembeli
     */
    public function daftar()
    {
        // 1. Cek apakah user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu untuk melihat riwayat pesanan.');
        }

        $model = new PesananModel();
        
        // Sesuaikan nama sesi ID user dengan yang kamu gunakan saat login
        $userId = session()->get('id_user') ?? session()->get('id');

        // 2. Ambil semua pesanan milik user ini, urutkan dari yang terbaru (DESC)
        $data['pesanan'] = $model->where('user_id', $userId)
                                 ->orderBy('created_at', 'DESC')
                                 ->findAll();

        // 3. Tampilkan ke View
        return view('v_daftar_pesanan', $data);
    }
    /**
     * Menghapus data pesanan kuliner
     * @param int $id ID dari Pesanan
     */
    public function hapus(int $id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $model = new PesananModel();
        $model->delete($id);

        return redirect()->to('/pesanan/daftar')->with('success', 'Pesanan berhasil dihapus');
    }


    /**
     * =====================================================================
     * FITUR PEMBAYARAN TRANSFER BANK MANUAL - METHODS BARU
     * =====================================================================
     */

    /**
     * Detail pesanan (menampilkan halaman v_detail_pesanan)
     *
     * @param int $pesanan_id ID Pesanan
     */
    public function detail(int $pesanan_id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $model = new PesananModel();
        $detailModel = new PesananDetailModel();
        $menuModel = new MenuModel();

        $pesanan = $model->find($pesanan_id);

        if (!$pesanan) {
            return redirect()->to('/home')->with('error', 'Pesanan tidak ditemukan.');
        }

        $currentUserId = session()->get('id_user') ?? session()->get('id');
        if ((int) $pesanan['user_id'] !== (int) $currentUserId) {
            return redirect()->to('/home')->with('error', 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // Ambil detail pesanan (3NF) dan join dengan nama menu
        $detailPesanan = $detailModel->where('pesanan_id', $pesanan_id)->findAll();
        foreach ($detailPesanan as &$detail) {
            $menu = $menuModel->find($detail['menu_id']);
            $detail['nama_menu'] = $menu ? $menu['nama_menu'] : 'Menu Tidak Diketahui';
        }

        $data['pesanan'] = $pesanan;
        $data['detail_pesanan'] = $detailPesanan;

        return view('v_detail_pesanan', $data);
    }

    /**
     * DASHBOARD ADMIN/MERCHANT: Menampilkan semua pesanan masuk
     */
    public function dashboardMerchant()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $model = new PesananModel();

        // HAPUS users.nomor_hp dari select di bawah ini
        $data['pesanan'] = $model->select('pesanan.*, users.nama as nama_pemesan')
                                 ->join('users', 'users.id = pesanan.user_id', 'left')
                                 ->orderBy('pesanan.created_at', 'DESC')
                                 ->findAll();

        $data['countPendingVerification'] = $model->where('status_pesanan', 'Menunggu Verifikasi')->countAllResults();

        return view('v_dashboard_merchant_pesanan', $data);
    }

    /**
     * AJAX: Menyetujui Pembayaran
     */
    public function approvePayment($id)
    {
        $model = new PesananModel();
        
        $updated = $model->update($id, [
            'status_pesanan' => 'Sudah Dibayar'
        ]);

        if ($updated) {
            return $this->response->setJSON(['success' => true, 'message' => 'Pembayaran berhasil disetujui!']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui status.']);
    }

    /**
     * AJAX: Menolak Pembayaran
     */
    public function rejectPayment($id)
    {
        $model = new PesananModel();
        
        $updated = $model->update($id, [
            'status_pesanan' => 'Pembayaran Ditolak'
        ]);

        if ($updated) {
            return $this->response->setJSON(['success' => true, 'message' => 'Pembayaran ditolak. Pelanggan diminta upload ulang.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui status.']);
    }

    /**
     * PROSES PEMBELI: Mengunggah / mengunggah ulang bukti pembayaran
     */
    public function uploadBukti($id)
    {
        // Pastikan ini adalah request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $validationRule = [
            'bukti_pembayaran' => [
                'label' => 'Bukti Pembayaran',
                'rules' => 'uploaded[bukti_pembayaran]'
                    . '|is_image[bukti_pembayaran]'
                    . '|mime_in[bukti_pembayaran,image/jpg,image/jpeg,image/png]'
                    . '|max_size[bukti_pembayaran,2048]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Format file tidak didukung atau ukuran melebihi 2MB.']);
        }

        $fileBukti = $this->request->getFile('bukti_pembayaran');

        if ($fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $namaFileBaru = $fileBukti->getRandomName();
            $fileBukti->move(ROOTPATH . 'public/uploads/bukti_bayar', $namaFileBaru);

            $model = new \App\Models\PesananModel();
            $model->update($id, [
                'bukti_pembayaran' => $namaFileBaru,
                'status_pesanan'   => 'Menunggu Verifikasi' // Reset status
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Bukti pembayaran berhasil diunggah!']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses gambar.']);
    }

    /**
     * Generate HTML untuk PDF struk pembayaran (Versi 3NF)
     * 
     * @param array $pesanan Data master pesanan
     * @param array $detailPesanan Data item pesanan_detail
     * @return string HTML content
     */
    private function generateStukHTML($pesanan, $detailPesanan)
    {
        $subtotal = 0;
        foreach ($detailPesanan as $item) {
            $subtotal += $item['subtotal'];
        }

        $serviceFee = round($subtotal * 0.02);
        $tax = round($subtotal * 0.10);
        $total = $subtotal + $serviceFee + $tax;

        // Build HTML
        $html = '
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Struk Pembayaran</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #333; }
                .struk-container { max-width: 600px; margin: 0 auto; border: 1px solid #999; padding: 20px; background-color: #fff; }
                .struk-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px; }
                .struk-header h1 { margin: 0; font-size: 24px; color: #333; }
                .struk-status { background-color: #f0f0f0; border: 1px solid #ddd; padding: 10px; text-align: center; font-weight: bold; margin-bottom: 20px; color: #28a745; }
                .struk-section { margin-bottom: 15px; }
                .struk-section-title { font-weight: bold; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px; font-size: 12px; }
                .struk-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 12px; }
                .struk-total { background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; margin: 15px 0; border-radius: 4px; }
                .struk-total-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 12px; }
                .struk-total-row.final { font-weight: bold; font-size: 14px; border-top: 1px solid #ddd; padding-top: 8px; }
                table { width: 100%; border-collapse: collapse; font-size: 12px; margin: 10px 0; }
                table th { text-align: left; padding: 5px; border-bottom: 1px solid #ddd; font-weight: bold; }
                table td { padding: 5px; border-bottom: 1px dotted #ddd; }
                .text-right { text-align: right; }
            </style>
        </head>
        <body>
        <div class="struk-container">
            <div class="struk-header">
                <h1>STRUK PEMBAYARAN</h1>
                <p>Pesanan Anda Telah Diverifikasi</p>
            </div>

            <div class="struk-status">✓ PEMBAYARAN TELAH DISETUJUI</div>

            <div class="struk-section">
                <div class="struk-section-title">Informasi Pesanan</div>
                <div class="struk-row">
                    <span><strong>No. Invoice:</strong></span>
                    <span><strong>' . $pesanan['kode_invoice'] . '</strong></span>
                </div>
                <div class="struk-row">
                    <span>Tanggal:</span>
                    <span>' . date('d-m-Y H:i', strtotime($pesanan['created_at'] ?? date('Y-m-d H:i'))) . '</span>
                </div>
            </div>

            <div class="struk-section">
                <div class="struk-section-title">Detail Menu</div>
                <table>
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Qty</th>
                            <th class="text-right">Harga</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($detailPesanan as $item):
            $html .= '<tr>
                        <td>' . $item['nama_menu'] . '</td>
                        <td>' . $item['qty'] . '</td>
                        <td class="text-right">Rp ' . number_format($item['harga_satuan'], 0, ',', '.') . '</td>
                        <td class="text-right">Rp ' . number_format($item['subtotal'], 0, ',', '.') . '</td>
                      </tr>';
        endforeach;

        $html .= '
                    </tbody>
                </table>
            </div>

            <div class="struk-total">
                <div class="struk-total-row">
                    <span>Subtotal:</span>
                    <span>Rp ' . number_format($subtotal, 0, ',', '.') . '</span>
                </div>
                <div class="struk-total-row">
                    <span>Service Fee (2%):</span>
                    <span>Rp ' . number_format($serviceFee, 0, ',', '.') . '</span>
                </div>
                <div class="struk-total-row">
                    <span>Pajak (10%):</span>
                    <span>Rp ' . number_format($tax, 0, ',', '.') . '</span>
                </div>
                <div class="struk-total-row final">
                    <span>TOTAL PEMBAYARAN:</span>
                    <span>Rp ' . number_format($pesanan['total_bayar'], 0, ',', '.') . '</span>
                </div>
            </div>
            
            <div class="struk-section">
                <div class="struk-section-title">Metode Pembayaran</div>
                <div class="struk-row">
                    <span>Metode:</span>
                    <span><strong>' . $pesanan['metode_pembayaran'] . '</strong></span>
                </div>
            </div>
        </div>
        </body>
        </html>';

        return $html;
    }
}