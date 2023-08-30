<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHandedToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'handed' => [
                'type' => 'ENUM',
                'constraint' => ['left', 'right'],
                'default' => 'right',
                'null' => false,
                'after' => 'last_active'
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'handed');
    }
}
