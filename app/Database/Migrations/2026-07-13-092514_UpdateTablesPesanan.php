<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTablesPesanan extends Migration
{
    public function up()
    {
        $this->forge->dropTable('pesanan_detail', true);
        $this->forge->dropTable('pesanan', true);

        // 2. TABEL PESANAN (MASTER)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'kode_invoice' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                // 'unsigned' => true dihapus agar match dengan tabel users lama
            ],
            'kuliner_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                // 'unsigned' => true dihapus agar match dengan tabel kuliner lama
            ],
            'total_bayar' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'default'    => 0,
            ],
            'metode_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'status_pesanan' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'process', 'completed', 'cancelled'],
                'default'    => 'pending',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kuliner_id', 'kuliner', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('pesanan');

        // 3. TABEL PESANAN_DETAIL
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'pesanan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                // Menyesuaikan dengan id milik pesanan di atas
            ],
            'menu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                // 'unsigned' => true dihapus agar match dengan tabel menus lama
            ],
            'qty' => [
                'type'       => 'INT',
                'constraint' => 5,
            ],
            'harga_satuan' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
            ],
            'subtotal' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
            ],
        ]);
        $this->forge->addKey('id', true);
        
        $this->forge->addForeignKey('pesanan_id', 'pesanan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('menu_id', 'menus', 'id', 'CASCADE', 'RESTRICT');
        
        $this->forge->createTable('pesanan_detail');
    }

    public function down()
    {
        $this->forge->dropTable('pesanan_detail', true);
        $this->forge->dropTable('pesanan', true);
    }
}