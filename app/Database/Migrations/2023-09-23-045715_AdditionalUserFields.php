<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdditionalUserFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'username',],
            'company' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'timezone',],
            'location' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'company',],
            'website' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'location',],
            'signature' => ['type' => 'TEXT', 'null' => true, 'after' => 'website',],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['name', 'company', 'location', 'website', 'signature']);
    }
}
