<?php
/**
 * VIEW: Daftar Riwayat Pesanan Pembeli (Versi 3NF)
 * Path: app/Views/v_daftar_pesanan.php
 * 
 * @var array $pesanan
 */
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Kuliner Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container-riwayat { background-color: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); padding: 30px; margin-top: 40px; margin-bottom: 40px; }
        .header-section { border-bottom: 3px solid #007bff; padding-bottom: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
        .header-section h2 { color: #007bff; font-weight: 700; margin: 0; }
        .badge-status { padding: 8px 12px; border-radius: 20px; font-weight: 600; font-size: 12px; text-transform: uppercase; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-process { background-color: #cce5ff; color: #004085; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        .table-hover tbody tr:hover { background-color: #f1f7ff; transition: 0.3s; }
        .btn-detail { padding: 6px 15px; font-size: 14px; border-radius: 6px; }
        .empty-state { text-align: center; padding: 50px 20px; }
        .empty-state i { font-size: 60px; color: #dee2e6; margin-bottom: 15px; }
        .empty-state h5 { color: #6c757d; font-weight: 600; }
    </style>
</head>
<body>

<!-- NAVBAR BISA DI-INCLUDE DI SINI JIKA ADA -->

<div class="container">
    <div class="container-riwayat">
        <div class="header-section">
            <h2><i class="fas fa-history"></i> Riwayat Pesanan Saya</h2>
            <a href="/home" class="btn btn-outline-primary"><i class="fas fa-home"></i> Kembali ke Menu</a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($pesanan)): ?>
            <!-- JIKA BELUM ADA PESANAN -->
            <div class="empty-state">
                <i class="fas fa-shopping-basket"></i>
                <h5>Anda belum memiliki riwayat pesanan.</h5>
                <p class="text-muted">Yuk, mulai pesan makanan favoritmu sekarang!</p>
                <a href="/home" class="btn btn-primary mt-3">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <!-- TABEL DAFTAR PESANAN -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">No. Invoice</th>
                            <th scope="col">Tanggal Pesan</th>
                            <th scope="col">Metode Pembayaran</th>
                            <th scope="col">Total Pembayaran</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pesanan as $row): ?>
                            <?php 
                                // Mapping warna badge status
                                $statusRaw = strtolower($row['status_pesanan']);
                                $badgeClass = 'secondary';
                                
                                if (in_array($statusRaw, ['pending', 'menunggu konfirmasi'])) {
                                    $badgeClass = 'pending';
                                } elseif (in_array($statusRaw, ['process', 'menunggu verifikasi'])) {
                                    $badgeClass = 'process';
                                } elseif (in_array($statusRaw, ['completed', 'sudah dibayar'])) {
                                    $badgeClass = 'completed';
                                } elseif (in_array($statusRaw, ['cancelled', 'pembayaran ditolak', 'batal'])) {
                                    $badgeClass = 'cancelled';
                                }
                            ?>
                            <tr>
                                <td><strong><?= esc($row['kode_invoice']) ?></strong></td>
                                <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                                <td><?= esc($row['metode_pembayaran']) ?></td>
                                <td class="text-success fw-bold">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge-status status-<?= $badgeClass ?>">
                                        <?= esc($row['status_pesanan']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="/pesanan/detail/<?= $row['id'] ?>" class="btn btn-primary btn-sm btn-detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>