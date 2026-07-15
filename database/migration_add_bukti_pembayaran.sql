-- =====================================================
-- SKEMA MODIFIKASI DATABASE
-- Fitur: Pembayaran Transfer Bank Manual dengan Verifikasi
-- Tanggal: 2026-07-07
-- =====================================================

-- ALTER TABLE: Menambahkan kolom bukti_pembayaran
-- Kolom ini menyimpan nama file acak foto bukti transfer bank
-- Ditempatkan setelah kolom metode_pembayaran
ALTER TABLE pesanan 
ADD COLUMN bukti_pembayaran VARCHAR(255) DEFAULT NULL 
AFTER metode_pembayaran;

-- CATATAN:
-- - bukti_pembayaran akan berisi nama file acak (contoh: 1719328947_abc123def456.jpg)
-- - Nama file dienkripsi menggunakan getRandomName() dari CodeIgniter 4
-- - File fisik disimpan di: public/uploads/bukti_bayar/
-- - Kolom ini nullable karena tidak semua metode pembayaran memerlukan bukti
-- - Kolom status_pesanan akan memiliki status baru:
--   * 'Menunggu Konfirmasi' → pelanggan belum membayar
--   * 'Menunggu Verifikasi' → bukti pembayaran sudah diunggah, menunggu verifikasi merchant
--   * 'Sudah Dibayar' → merchant menyetujui pembayaran
--   * 'Pembayaran Ditolak' → merchant menolak bukti pembayaran, pelanggan dapat mengunggah ulang
-- =====================================================
