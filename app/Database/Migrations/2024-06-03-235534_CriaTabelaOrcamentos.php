<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriaTabelaOrcamentos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'numero' => [
                'type'       => 'INT',
                'constraint' => '10',
            ],
            'cliente_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
            ],
            'data' => [
                'type' => 'DATE',
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => null,
            ],
            'criado_em' => [
                'type'       => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'atualizado_em' => [
                'type'       => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'deletado_em' => [
                'type'       => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('orcamentos');
    }

    public function down()
    {
        $this->forge->dropTable('orcamentos');
    }
}
