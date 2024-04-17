<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserVisitsTable extends Migration
{
    public function up()
    {
        // Create a table for tracking the days a user visits.
        $this->forge->addField([
            'id'         => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'visited_on' => ['type' => 'date'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', false, 'CASCADE');
        $this->forge->createTable('user_visits');
    }

    public function down()
    {
        $this->forge->dropTable('user_visits');
    }
}
