<?php
/**
 * VIEW: Struk Pembayaran - Halaman Struk Lengkap (Versi 3NF)
 * Path: app/Views/v_struk_pembayaran.php
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
    <title>Struk Pembayaran - <?= $pesanan['kode_invoice'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container-struk { margin-top: 40px; margin-bottom: 40px; }
        .struk-container { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); overflow: hidden; max-width: 600px; margin: 0 auto; }
        .struk-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .struk-header h1 { font-size: 32px; font-weight: 700; margin-bottom: 10px; }
        .struk-header p { font-size: 14px; opacity: 0.9; margin: 0; }
        .struk-status { background-color: #d4edda; color: #155724; padding: 15px; text-align: center; font-weight: 600; border-bottom: 2px solid #155724; }
        .struk-body { padding: 30px; }
        .struk-section { margin-bottom: 25px; }
        .struk-section-title { font-weight: 700; color: #667eea; font-size: 14px; text-transform: uppercase; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0; }
        .struk-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .struk-row:last-child { border-bottom: none; }
        .struk-row.bold { font-weight: 600; color: #212529; }
        .struk-row.highlight { background-color: #f9f9f9; padding: 12px; margin: 5px 0; border-radius: 6px; border: 1px solid #f0f0f0; }
        .struk-row label { color: #6c757d; font-weight: 500; }
        .struk-row value { color: #212529; font-weight: 600; }
        .menu-items { margin: 15px 0; }
        .menu-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; background-color: #f9f9f9; margin-bottom: 8px; border-radius: 6px; font-size: 13px; }
        .menu-item-name { font-weight: 600; color: #212529; flex: 1; }
        .menu-item-qty { color: #6c757d; margin: 0 10px; min-width: 40px; text-align: center; }
        .menu-item-price { color: #667eea; font-weight: 600; min-width: 100px; text-align: right; }
        .struk-total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-top: 20px; margin-bottom: 20px; }
        .struk-total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
        .struk-total-row.final { font-size: 18px; font-weight: 700; border-top: 1px solid rgba(255, 255, 255, 0.3); padding-top: 12px; margin-top: 12px; }
        .struk-footer { text-align: center; padding: 20px; background-color: #f9f9f9; border-top: 1px solid #f0f0f0; font-size: 12px; color: #6c757d; }
        .button-group { display: flex; gap: 10px; justify-content: center; margin-top: 25px; flex-wrap: wrap; }
        .btn-action { padding: 12px 25px; font-weight: 600; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; text-decoration: none; transition: all 0.3s; }
        .btn-download-pdf { background-color: #667eea; color: white; }
        .btn-download-pdf:hover { background-color: #5568d3; color: white; text-decoration: none; }
        .btn-print { background-color: #6c757d; color: white; }
        .btn-back { background-color: #e9ecef; color: #495057; }
        .separator-line { height: 1px; background-color: #f0f0f0; margin: 15px 0; }
        @media print { body { background-color: white; } .container-struk { margin-top: 0; margin-bottom: 0; } .struk-container { box-shadow: none; margin: 0; } .button-group { display: none; } }
    </style>
</head>
<body>
<div class="container-struk">
    <div class="struk-container">
        <!-- HEADER -->
        <div class="struk-header">
            <h1>🧾 STRUK PEMBAYARAN</h1>
            <p>Pesanan Anda Telah Diverifikasi</p>
        </div>

        <div class="struk-status">✅ PEMBAYARAN TELAH DISETUJUI</div>

        <div class="struk-body">
            <!-- NOMOR INVOICE -->
            <div class="struk-section">
                <div class="struk-section-title">Informasi Pesanan</div>
                <div class="struk-row bold">
                    <label>No. Invoice:</label>
                    <value><?= $pesanan['kode_invoice'] ?></value>
                </div>
                <div class="struk-row">
                    <label>Tanggal Pesan:</label>
                    <value><?= date('d-m-Y H:i', strtotime($pesanan['created_at'])) ?></value>
                </div>
                <div class="struk-row">
                    <label>Status:</label>
                    <value style="color: #28a745; font-weight: 700;">Sudah Dibayar ✅</value>
                </div>
            </div>

            <div class="separator-line"></div>

            <!-- INFORMASI PEMESAN -->
            <div class="struk-section">
                <div class="struk-section-title">Data Pemesan</div>
                <div class="struk-row">
                    <label>Nama:</label>
                    <value><?= esc(session()->get('nama') ?? session()->get('username') ?? 'Pelanggan') ?></value>
                </div>
            </div>

            <div class="separator-line"></div>

            <!-- DETAIL MENU (Dari pesanan_detail) -->
            <div class="struk-section">
                <div class="struk-section-title">Detail Menu</div>
                <div class="menu-items">
                    <?php 
                    $subtotal = 0;
                    $totalItem = 0;
                    foreach ($detail_pesanan as $item): 
                        $subtotal += $item['subtotal'];
                        $totalItem += $item['qty'];
                    ?>
                    <div class="menu-item">
                        <span class="menu-item-name"><?= esc($item['nama_menu']) ?></span>
                        <span class="menu-item-qty">× <?= $item['qty'] ?></span>
                        <span class="menu-item-price">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="separator-line"></div>

            <!-- RINGKASAN PEMBAYARAN -->
            <div class="struk-section">
                <div class="struk-section-title">Ringkasan Pembayaran</div>
                <?php
                $serviceFee = round($subtotal * 0.02);
                $tax = round($subtotal * 0.10);
                ?>
                <div class="struk-row">
                    <label>Subtotal:</label>
                    <value>Rp <?= number_format($subtotal, 0, ',', '.') ?></value>
                </div>
                <div class="struk-row">
                    <label>Service Fee (2%):</label>
                    <value>Rp <?= number_format($serviceFee, 0, ',', '.') ?></value>
                </div>
                <div class="struk-row">
                    <label>Pajak (10%):</label>
                    <value>Rp <?= number_format($tax, 0, ',', '.') ?></value>
                </div>
            </div>

            <!-- TOTAL PEMBAYARAN -->
            <div class="struk-total">
                <div class="struk-total-row">
                    <span>Total Item:</span>
                    <span><?= $totalItem ?> Item</span>
                </div>
                <div class="struk-total-row final">
                    <span>TOTAL PEMBAYARAN:</span>
                    <span>Rp <?= number_format($pesanan['total_bayar'], 0, ',', '.') ?></span>
                </div>
            </div>

            <!-- METODE PEMBAYARAN -->
            <div class="struk-section">
                <div class="struk-section-title">Metode Pembayaran</div>
                <div class="struk-row highlight bold">
                    <label>Metode:</label>
                    <value><?= esc($pesanan['metode_pembayaran']) ?></value>
                </div>
                <?php if (strtolower($pesanan['metode_pembayaran']) === 'transfer bank'): ?>
                <div class="struk-row highlight" style="background-color: #fff3cd; border-color: #ffe69c;">
                    <label style="color: #856404;">Bukti Transfer:</label>
                    <value style="color: #856404; font-weight: 700;">✅ Terverifikasi</value>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="struk-footer">
            <p>Terima kasih telah memesan di Kuliner Kampus</p>
            <p>Pesanan Anda telah diverifikasi dan siap dikerjakan</p>
            <p style="margin-top: 10px; font-size: 11px; color: #999;">Struk ini dicetak pada <?= date('d-m-Y H:i:s') ?></p>
        </div>
    </div>

    <!-- BUTTON ACTIONS -->
    <div class="button-group">
        <a href="/pesanan/strukPDF/<?= $pesanan['id'] ?>" class="btn-action btn-download-pdf" target="_blank">
            <i class="fas fa-download"></i> Download PDF
        </a>
        <button class="btn-action btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print
        </button>
        <a href="/pesanan/detail/<?= $pesanan['id'] ?>" class="btn-action btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>