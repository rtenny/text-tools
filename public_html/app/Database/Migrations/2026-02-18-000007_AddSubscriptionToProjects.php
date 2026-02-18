<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubscriptionToProjects extends Migration
{
    public function up()
    {
        $fields = [
            'subscription_type' => [
                'type'       => 'ENUM',
                'constraint' => ['subscription', 'lifetime'],
                'default'    => 'subscription',
                'after'      => 'is_active',
            ],
            'daily_translation_limit' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'subscription_type',
            ],
        ];

        $this->forge->addColumn('projects', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('projects', 'daily_translation_limit');
        $this->forge->dropColumn('projects', 'subscription_type');
    }
}
