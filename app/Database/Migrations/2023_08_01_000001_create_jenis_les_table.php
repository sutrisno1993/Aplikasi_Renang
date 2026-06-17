<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJenisLesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_les' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'harga' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('jenis_les');
    }

    public function down()
    {
        $this->forge->dropTable('jenis_les');
    }
}