<?php
/**
 * VIEW: Detail Pesanan / Halaman Sukses Order (Versi 3NF)
 * Path: app/Views/v_detail_pesanan.php
 * 
 * @var array $pesanan
 * @var array $detail_pesanan
 */
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Kuliner Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container-detail { background-color: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); padding: 30px; margin-top: 30px; margin-bottom: 30px; }
        .header-section { border-bottom: 3px solid #007bff; padding-bottom: 20px; margin-bottom: 25px; }
        .header-section h2 { color: #007bff; font-weight: 700; margin-bottom: 10px; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 14px; text-transform: uppercase; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-process { background-color: #ffc107; color: #ffffff; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        .info-section { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #007bff; }
        .info-section h5 { color: #007bff; font-weight: 700; margin-bottom: 15px; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e9ecef; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #495057; width: 40%; }
        .info-value { color: #212529; font-weight: 500; }
        .bank-account-box { background-color: #e7f3ff; border: 2px solid #007bff; border-radius: 8px; padding: 20px; margin-bottom: 25px; }
        .bank-account-box h6 { color: #007bff; font-weight: 700; margin-bottom: 15px; }
        .bank-details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        .bank-detail-item { background-color: white; padding: 12px; border-radius: 6px; }
        .bank-detail-label { font-size: 12px; color: #6c757d; font-weight: 600; text-transform: uppercase; margin-bottom: 5px; }
        .bank-detail-value { font-size: 16px; color: #212529; font-weight: 700; word-break: break-all; }
        .total-price { background-color: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 25px; }
        .total-price .label { color: #856404; font-weight: 600; font-size: 14px; }
        .total-price .amount { color: #ff6600; font-size: 32px; font-weight: 700; margin-top: 10px; }
        .form-section { background-color: #f0f7ff; padding: 25px; border-radius: 8px; border-left: 4px solid #28a745; margin-bottom: 25px; }
        .form-section h5 { color: #28a745; font-weight: 700; margin-bottom: 20px; }
        .file-input-wrapper { position: relative; overflow: hidden; display: inline-block; width: 100%; }
        .file-input-wrapper input[type="file"] { position: absolute; left: -9999px; }
        .file-input-label { display: block; padding: 15px; background-color: #e7f3ff; border: 2px dashed #007bff; border-radius: 6px; text-align: center; cursor: pointer; transition: all 0.3s ease; font-weight: 600; color: #007bff; }
        .file-input-label:hover { background-color: #cce5ff; border-color: #0056b3; }
        .file-input-label.active { background-color: #d4edda; border-color: #28a745; color: #155724; }
        .file-preview { margin-top: 15px; text-align: center; }
        .file-preview img { max-width: 100%; max-height: 300px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15); }
        .file-preview .file-info { margin-top: 10px; font-size: 14px; color: #6c757d; }
        .button-group { display: flex; gap: 10px; justify-content: center; margin-top: 20px; }
        .btn { padding: 12px 30px; font-weight: 600; border-radius: 6px; transition: all 0.3s ease; }
        .btn-submit { background-color: #28a745; color: white; border: none; }
        .btn-submit:hover { background-color: #218838; }
        .btn-secondary-custom { background-color: #6c757d; color: white; border: none; text-decoration: none; }
        .btn-secondary-custom:hover { background-color: #5a6268; color: white; text-decoration: none; }
        .alert-custom { border-radius: 6px; padding: 15px; margin-bottom: 20px; font-weight: 500; }
        .alert-info-custom { background-color: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        .alert-success-custom { background-color: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .menu-list { background-color: #f8f9fa; border-radius: 6px; padding: 15px; margin-bottom: 15px; }
        .menu-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e9ecef; }
        .menu-item:last-child { border-bottom: none; }
        .menu-name { font-weight: 600; color: #212529; }
        .menu-qty { color: #6c757d; font-size: 14px; }
        .menu-price { font-weight: 700; color: #ff6600; }
        .loading-spinner { display: none; text-align: center; margin: 20px 0; }
        .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #007bff; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
<div class="container">
    <div class="container-detail">
        <!-- HEADER -->
        <div class="header-section">
            <h2>📋 Detail Pesanan</h2>
            <div style="margin-top: 10px;">
                <span class="status-badge status-<?php 
                    $status_class = strtolower($pesanan['status_pesanan']);
                    echo $status_class == 'menunggu konfirmasi' ? 'pending' : $status_class;
                ?>">
                    <?= $pesanan['status_pesanan'] ?>
                </span>
            </div>
        </div>

        <!-- INFORMASI PEMESAN -->
        <div class="info-section">
            <h5>👤 Informasi Pesanan</h5>
            <div class="info-row">
                <span class="info-label">Kode Invoice:</span>
                <span class="info-value"><strong><?= esc($pesanan['kode_invoice']) ?></strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Nama Pemesan:</span>
                <span class="info-value"><?= esc(session()->get('nama') ?? session()->get('username') ?? 'Pelanggan') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Pesan:</span>
                <span class="info-value"><?= date('d-m-Y H:i', strtotime($pesanan['created_at'])) ?></span>
            </div>
        </div>

        <!-- DAFTAR MENU (Dari Tabel pesanan_detail) -->
        <div class="info-section">
            <h5>🍜 Daftar Menu yang Dipesan</h5>
            <div class="menu-list">
                <?php if (!empty($detail_pesanan)): ?>
                    <?php foreach ($detail_pesanan as $item): ?>
                    <div class="menu-item">
                        <div>
                            <span class="menu-name"><?= esc($item['nama_menu']) ?></span>
                            <span class="menu-qty"> × <?= $item['qty'] ?></span>
                        </div>
                        <span class="menu-price">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning">Detail menu tidak ditemukan.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- TOTAL HARGA -->
        <div class="total-price">
            <div class="label">Total Tagihan</div>
            <div class="amount">Rp <?= number_format($pesanan['total_bayar'], 0, ',', '.') ?></div>
        </div>

        <!-- METODE PEMBAYARAN & FORM UPLOAD BUKTI -->
        <?php if (strtolower($pesanan['metode_pembayaran']) === 'transfer bank'): ?>

            <!-- INFORMASI REKENING BANK -->
            <div class="bank-account-box">
                <h6>🏦 Silakan Transfer ke Rekening Berikut:</h6>
                <div class="bank-details-grid">
                    <div class="bank-detail-item">
                        <div class="bank-detail-label">Bank</div>
                        <div class="bank-detail-value">BRI</div>
                    </div>
                    <div class="bank-detail-item">
                        <div class="bank-detail-label">No. Rekening</div>
                        <div class="bank-detail-value">3406 0100 3344 503</div>
                    </div>
                    <div class="bank-detail-item">
                        <div class="bank-detail-label">Atas Nama</div>
                        <div class="bank-detail-value">Merchant Kuliner Semarang</div>
                    </div>
                    <div class="bank-detail-item">
                        <div class="bank-detail-label">Jumlah Transfer</div>
                        <div class="bank-detail-value">Rp <?= number_format($pesanan['total_bayar'], 0, ',', '.') ?></div>
                    </div>
                </div>
                <div class="alert alert-custom alert-info-custom" style="margin-top: 15px;">
                    ⚠️ <strong>Penting:</strong> Pastikan Anda mentransfer sesuai jumlah yang tertera.
                </div>
            </div>

            <!-- STATUS PEMERIKSAAN -->
            <div class="info-section">
                <h5>📊 Status Pembayaran</h5>
                <?php
                $status_lower = strtolower($pesanan['status_pesanan']);
                if (in_array($status_lower, ['pending', 'menunggu konfirmasi'])):
                ?>
                    <div class="alert alert-custom alert-info-custom">
                        Anda belum mengunggah bukti pembayaran. Silakan upload bukti transfer di bawah ini.
                    </div>
                <?php elseif (in_array($status_lower, ['process', 'menunggu verifikasi'])): ?>
                    <div class="alert alert-custom alert-info-custom">
                        Bukti pembayaran Anda sedang diverifikasi oleh merchant. Harap tunggu.
                    </div>
                <?php elseif (in_array($status_lower, ['completed', 'sudah dibayar'])): ?>
                    <div class="alert alert-custom alert-success-custom">
                        ✅ <strong>Pembayaran Telah Diverifikasi!</strong> Pesanan Anda akan segera diproses.
                    </div>
                <?php elseif (in_array($status_lower, ['cancelled', 'pembayaran ditolak'])): ?>
                    <div class="alert alert-custom alert-danger" style="background-color: #f8d7da; color: #721c24; border-left: 4px solid #f5c6cb;">
                        ❌ <strong>Pembayaran Ditolak!</strong> Silakan upload ulang bukti pembayaran yang valid.
                    </div>
                <?php endif; ?>
            </div>

            <!-- FORM UPLOAD BUKTI PEMBAYARAN -->
            <?php if (in_array($status_lower, ['pending', 'menunggu konfirmasi', 'cancelled', 'pembayaran ditolak'])): ?>
            <div class="form-section">
                <h5>📤 Upload Bukti Pembayaran Transfer</h5>
                <form id="formUploadBukti" enctype="multipart/form-data" method="POST" action="/pesanan/uploadBukti/<?= $pesanan['id'] ?>">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="buktiFile" class="form-label">Pilih Foto Bukti Transfer Bank <span style="color: red;">*</span></label>
                        <div class="file-input-wrapper">
                            <input type="file" id="buktiFile" name="bukti_pembayaran" class="form-control" required accept="image/*" onchange="handleFileSelect(this)">
                            <label for="buktiFile" class="file-input-label">
                                📎 Klik untuk memilih file atau drag & drop gambar di sini<br>
                                <small style="color: #6c757d; font-weight: 400; display: block; margin-top: 8px;">Format: PNG, JPG, JPEG | Maksimal: 2 MB</small>
                            </label>
                        </div>
                        <div class="file-preview" id="filePreview" style="display: none;">
                            <img id="previewImage" src="" alt="Preview">
                            <div class="file-info"><strong id="fileName"></strong><br>Ukuran: <span id="fileSize"></span></div>
                        </div>
                    </div>
                    <div class="loading-spinner" id="loadingSpinner"><div class="spinner"></div><p>Sedang mengunggah...</p></div>
                    <div class="button-group">
                        <button type="submit" class="btn btn-submit" id="submitBtn">✅ Upload Bukti Pembayaran</button>
                        <a href="/home" class="btn btn-secondary-custom">❌ Kembali</a>
                    </div>
                </form>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-custom alert-info-custom">
                💳 <strong>Metode Pembayaran:</strong> <?= esc($pesanan['metode_pembayaran']) ?><br>
                Pesanan Anda sedang diproses. Terima kasih sudah memesan!
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
// (Script JS tetap sama seperti aslinya, tidak ada logika database disini)
function handleFileSelect(input) {
    const fileLabel = input.parentElement.querySelector('.file-input-label');
    const filePreview = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 2097152) { alert('❌ Ukuran file terlalu besar! Maksimal 2 MB.'); input.value = ''; filePreview.style.display = 'none'; fileLabel.classList.remove('active'); return; }
        const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        if (!validTypes.includes(file.type)) { alert('❌ Format file tidak didukung!'); input.value = ''; filePreview.style.display = 'none'; fileLabel.classList.remove('active'); return; }

        const reader = new FileReader();
        reader.onload = function(e) { previewImage.src = e.target.result; fileName.textContent = file.name; fileSize.textContent = (file.size / 1024).toFixed(2) + ' KB'; filePreview.style.display = 'block'; };
        reader.readAsDataURL(file);
        fileLabel.classList.add('active');
    } else {
        filePreview.style.display = 'none'; fileLabel.classList.remove('active');
    }
}

document.getElementById('formUploadBukti')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const fileInput = document.getElementById('buktiFile');
    if (!fileInput.files[0]) { alert('❌ Silakan pilih file terlebih dahulu!'); return; }

    const formData = new FormData(this);
    const submitBtn = document.getElementById('submitBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');

    submitBtn.disabled = true; loadingSpinner.style.display = 'block';

    fetch(this.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(response => response.json())
    .then(data => {
        loadingSpinner.style.display = 'none'; submitBtn.disabled = false;
        if (data.success) { alert('✅ ' + data.message); window.location.reload(); } else { alert('❌ Error: ' + data.message); }
    })
    .catch(error => { loadingSpinner.style.display = 'none'; submitBtn.disabled = false; alert('❌ Terjadi kesalahan: ' + error); });
});
</script>
</body>
</html>