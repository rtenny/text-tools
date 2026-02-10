<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectTownsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'town_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['project_id', 'town_id']); // Prevent duplicate assignments and provide index

        // Foreign key constraints with CASCADE delete
        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('town_id', 'towns', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('project_towns');
    }

    public function down()
    {
        $this->forge->dropTable('project_towns');
    }
}
