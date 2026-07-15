<?php
/**
 * VIEW: Dashboard Merchant - Daftar Pesanan Dengan Verifikasi Pembayaran (Versi 3NF)
 * Path: app/Views/v_dashboard_merchant_pesanan.php
 * 
 * @var array $pesanan
 * @var int $countPendingVerification
 */
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Merchant - Verifikasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
        .navbar-custom .navbar-brand { font-weight: 700; font-size: 24px; color: white !important; }
        .navbar-custom .nav-link { color: rgba(255, 255, 255, 0.9) !important; font-weight: 500; margin-left: 20px; transition: color 0.3s; }
        .navbar-custom .nav-link:hover { color: white !important; }
        .container-dashboard { margin-top: 30px; margin-bottom: 30px; }
        .header-section { background: white; border-radius: 12px; padding: 25px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); border-left: 4px solid #667eea; display: flex; justify-content: space-between; align-items: center; }
        .header-section h2 { color: #667eea; font-weight: 700; margin-bottom: 5px; }
        .filter-section { background: white; border-radius: 12px; padding: 20px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); }
        .filter-section .form-label { font-weight: 600; color: #495057; margin-bottom: 8px; }
        .filter-btn { padding: 10px 20px; border-radius: 6px; font-weight: 600; border: none; cursor: pointer; transition: all 0.3s; background-color: #e9ecef; color: #495057; }
        .filter-btn.active { background-color: #667eea; color: white; }
        .filter-btn:hover:not(.active) { background-color: #dee2e6; }
        .table-container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); margin-bottom: 30px; }
        .table-custom { margin-bottom: 0; font-size: 14px; }
        .table-custom thead { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
        .table-custom thead th { font-weight: 700; color: #495057; padding: 15px; vertical-align: middle; text-transform: uppercase; font-size: 12px; }
        .table-custom tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #dee2e6; }
        .table-custom tbody tr:hover { background-color: #f8f9fa; }
        
        /* Status Badges Relasional */
        .status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-menunggu_konfirmasi { background-color: #cfe2ff; color: #084298; }
        .status-menunggu_verifikasi { background-color: #fff3cd; color: #856404; }
        .status-sudah_dibayar { background-color: #d1e7dd; color: #0a3622; }
        .status-pembayaran_ditolak { background-color: #f8d7da; color: #842029; }
        
        .btn-action { padding: 6px 12px; font-size: 12px; font-weight: 600; border-radius: 6px; cursor: pointer; transition: all 0.3s; border: none; margin: 2px; }
        .btn-view { background-color: #17a2b8; color: white; }
        .btn-approve { background-color: #28a745; color: white; }
        .btn-reject { background-color: #dc3545; color: white; }
        .empty-state { text-align: center; padding: 60px 20px; background: white; border-radius: 12px; }
        .empty-state-icon { font-size: 64px; color: #dee2e6; margin-bottom: 20px; }
        .empty-state-title { font-size: 24px; font-weight: 700; color: #495057; margin-bottom: 10px; }
        .empty-state-desc { font-size: 16px; color: #6c757d; }
        
        /* Modal Style */
        .modal-custom { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); animation: fadeIn 0.3s ease; }
        .modal-custom.show { display: block; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .modal-custom-content { background-color: white; margin: 5% auto; padding: 30px; border-radius: 12px; width: 90%; max-width: 600px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3); }
        .modal-header-custom { border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .modal-header-custom h5 { margin: 0; color: #667eea; font-weight: 700; }
        .close-btn { background: none; border: none; font-size: 24px; cursor: pointer; color: #6c757d; }
        .modal-body-custom { text-align: center; }
        .modal-image { max-width: 100%; max-height: 400px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); margin-bottom: 20px; }
        .modal-info { background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: left; }
        .modal-info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e9ecef; }
        .modal-info-row:last-child { border-bottom: none; }
        .modal-info-label { font-weight: 600; color: #495057; }
        .modal-action { display: flex; gap: 10px; justify-content: center; margin-top: 20px; }
        .badge-count { position: absolute; top: -8px; right: -8px; background-color: #dc3545; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; }
        .filter-badge-container { position: relative; display: inline-block; }
        .no-data-message { text-align: center; padding: 40px; color: #6c757d; font-size: 16px; }

        /* 🖨️ CSS KHUSUS CETAK LAPORAN */
        @media print {
            body { background-color: white; color: black; }
            .navbar-custom, .filter-section, .btn-action, .close-btn, .modal-custom, .btn-print, th:last-child, td:last-child { display: none !important; }
            .container-dashboard { margin-top: 0; padding: 0; }
            .header-section { border: none; box-shadow: none; padding: 0; margin-bottom: 20px; text-align: center; display: block; }
            .header-section h2 { color: black; font-size: 26px; text-align: center; }
            .header-section p { font-size: 14px; }
            .table-container { box-shadow: none; border: 1px solid #000; border-radius: 0; }
            .table-custom { width: 100%; border-collapse: collapse; font-size: 12px; }
            .table-custom th, .table-custom td { border: 1px solid #000 !important; padding: 8px !important; }
            .status-badge { background: none !important; color: black !important; padding: 0; font-weight: bold; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <span class="navbar-brand"><i class="fas fa-store"></i> Dashboard Admin & Merchant</span>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/home"><i class="fas fa-home"></i> Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container-dashboard">
    <div class="container">
        <!-- HEADER -->
        <div class="header-section">
            <div>
                <h2><i class="fas fa-list-check"></i> Verifikasi Pembayaran & Laporan</h2>
                <p class="text-muted" style="margin: 0;">Kelola, verifikasi bukti transfer bank, dan cetak rekap laporan pesanan masuk.</p>
            </div>
            <!-- TOMBOL CETAK LAPORAN -->
            <button class="btn btn-dark btn-print fw-bold px-4 py-2" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>

        <!-- FILTER SECTION -->
        <div class="filter-section">
            <label class="form-label">Filter Berdasarkan Status:</label>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="filter-btn active" onclick="filterTable('all', event)">📋 Semua</button>
                <button class="filter-btn filter-badge-container" onclick="filterTable('menunggu_verifikasi', event)">
                    ⏳ Menunggu Verifikasi
                    <?php if (isset($countPendingVerification) && $countPendingVerification > 0): ?>
                        <span class="badge-count"><?= $countPendingVerification ?></span>
                    <?php endif; ?>
                </button>
                <button class="filter-btn" onclick="filterTable('sudah_dibayar', event)">✅ Sudah Dibayar</button>
                <button class="filter-btn" onclick="filterTable('pembayaran_ditolak', event)">❌ Pembayaran Ditolak</button>
            </div>
        </div>

        <!-- TABLE PESANAN -->
        <div class="table-container">
            <?php if (!empty($pesanan)): ?>
            <table class="table table-custom" id="tableOrders">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Nama Pemesan</th>
                        <th>Metode</th>
                        <th>Total Tagihan</th>
                        <th>Status</th>
                        <th>Tanggal Masuk</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pesanan as $item): ?>
                    <?php 
                        $statusSlug = strtolower(str_replace(' ', '_', $item['status_pesanan']));
                    ?>
                    <tr class="order-row" data-status="<?= $statusSlug ?>">
                        <td><strong><?= esc($item['kode_invoice']) ?></strong></td>
                        <td>
                            <strong><?= esc($item['nama_pemesan'] ?? 'User ID: '.$item['user_id']) ?></strong><br>
                            <small class="text-muted"><?= esc($item['nomor_hp'] ?? '-') ?></small>
                        </td>
                        <td><span class="badge bg-secondary"><?= esc($item['metode_pembayaran']) ?></span></td>
                        <td><strong class="text-success">Rp <?= number_format($item['total_bayar'], 0, ',', '.') ?></strong></td>
                        <td><span class="status-badge status-<?= $statusSlug ?>"><?= esc($item['status_pesanan']) ?></span></td>
                        <td><small><?= date('d-m-Y H:i', strtotime($item['created_at'])) ?></small></td>
                        <td class="text-center">
                            <?php if ($statusSlug === 'menunggu_verifikasi' && !empty($item['bukti_pembayaran'])): ?>
                                <button class="btn-action btn-view" onclick="viewBukti('<?= $item['id'] ?>', '<?= esc($item['bukti_pembayaran']) ?>', '<?= esc($item['nama_pemesan'] ?? 'User') ?>', '<?= number_format($item['total_bayar'], 0, ',', '.') ?>')">
                                    <i class="fas fa-eye"></i> Lihat Bukti
                                </button>
                                <button class="btn-action btn-approve" onclick="approvePayment(<?= $item['id'] ?>)">
                                    <i class="fas fa-check"></i> Setuju
                                </button>
                                <button class="btn-action btn-reject" onclick="rejectPayment(<?= $item['id'] ?>)">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            <?php elseif ($statusSlug === 'menunggu_konfirmasi'): ?>
                                <span class="text-muted small">⏳ Menunggu transfer</span>
                            <?php elseif ($statusSlug === 'sudah_dibayar'): ?>
                                <span class="text-success small"><i class="fas fa-check-double"></i> Terverifikasi</span>
                            <?php elseif ($statusSlug === 'pembayaran_ditolak'): ?>
                                <span class="text-danger small"><i class="fas fa-ban"></i> Ditolak</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-folder-open text-muted"></i></div>
                    <div class="empty-state-title">Tidak Ada Pesanan</div>
                    <div class="empty-state-desc">Saat ini belum ada data riwayat transaksi masuk.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL: LIHAT BUKTI PEMBAYARAN -->
<div class="modal-custom" id="modalBukti">
    <div class="modal-custom-content">
        <div class="modal-header-custom">
            <h5>📸 Bukti Pembayaran Transfer Bank</h5>
            <button class="close-btn" onclick="closeBuktiModal()">×</button>
        </div>
        <div class="modal-body-custom">
            <img id="buktiImage" class="modal-image" src="" alt="Bukti Pembayaran">
            <div class="modal-info">
                <div class="modal-info-row"><span class="modal-info-label">Nama Pemesan:</span><span id="buktiNama">-</span></div>
                <div class="modal-info-row"><span class="modal-info-label">Total Tagihan:</span><span id="buktiTotal" class="text-success fw-bold">-</span></div>
            </div>
            <div class="modal-action">
                <button class="btn-approve px-3 py-2" onclick="approvePaymentFromModal()"><i class="fas fa-check"></i> Setujui</button>
                <button class="btn-reject px-3 py-2" onclick="rejectPaymentFromModal()"><i class="fas fa-times"></i> Tolak</button>
                <button class="btn btn-secondary px-3 py-2" onclick="closeBuktiModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPesananId = null;

function filterTable(status, e) {
    const rows = document.querySelectorAll('.order-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        if (status === 'all' || rowStatus === status) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    e.target.classList.add('active');

    const exMsg = document.getElementById('noDataMsg');
    if(exMsg) exMsg.remove();

    if (visibleCount === 0) {
        const tbody = document.querySelector('#tableOrders tbody');
        if (tbody) {
            const noDataRow = document.createElement('tr');
            noDataRow.id = 'noDataMsg';
            noDataRow.innerHTML = '<td colspan="7" class="no-data-message">Tidak ada data pesanan untuk kategori filter ini.</td>';
            tbody.appendChild(noDataRow);
        }
    }
}

function viewBukti(pesananId, buktiFile, namaPemesan, totalTagihan) {
    currentPesananId = pesananId;
    document.getElementById('buktiImage').src = '/uploads/bukti_bayar/' + buktiFile;
    document.getElementById('buktiNama').textContent = namaPemesan;
    document.getElementById('buktiTotal').textContent = 'Rp ' + totalTagihan;
    document.getElementById('modalBukti').classList.add('show');
}

function closeBuktiModal() {
    document.getElementById('modalBukti').classList.remove('show');
    currentPesananId = null;
}

function approvePayment(id) {
    if (!confirm('Setujui pembayaran invoice ini?')) return;
    fetch('/pesanan/approvePayment/' + id, { method: 'POST' })
    .then(res => res.json()).then(data => { if(data.success){ location.reload(); }else{ alert(data.message); } });
}

function rejectPayment(id) {
    if (!confirm('Tolak bukti pembayaran ini?')) return;
    fetch('/pesanan/rejectPayment/' + id, { method: 'POST' })
    .then(res => res.json()).then(data => { if(data.success){ location.reload(); }else{ alert(data.message); } });
}

function approvePaymentFromModal() { if(currentPesananId) approvePayment(currentPesananId); }
function rejectPaymentFromModal() { if(currentPesananId) rejectPayment(currentPesananId); }

window.onclick = function(event) {
    const modal = document.getElementById('modalBukti');
    if (event.target === modal) closeBuktiModal();
}
</script>
</body>
</html>