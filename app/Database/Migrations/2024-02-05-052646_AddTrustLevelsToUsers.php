<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTrustLevelsToUsers extends Migration
{
    public function up()
    {
        // Add the field 'trust_level' to the 'users' table
        $this->forge->addColumn('users', [
            'trust_level' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'timezone',
            ],
        ]);
    }

    public function down()
    {
        // Drop the field 'trust_level' from the 'users' table
        $this->forge->dropColumn('users', 'trust_level');
    }
}
