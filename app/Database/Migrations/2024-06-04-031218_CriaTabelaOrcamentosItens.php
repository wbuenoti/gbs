<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriaTabelaOrcamentosItens extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'orcamento_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
            ],
            'descricao' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'quantidade' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'valor_unitario' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'montante' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
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
        $this->forge->addForeignKey('orcamento_id', 'orcamentos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('orcamento_itens');
    }

    public function down()
    {
        $this->forge->dropTable('orcamento_itens');
    }
}
