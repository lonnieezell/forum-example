<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsersDeleteTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id'      => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'scheduled_at' => ['type' => 'datetime', 'null' => false],
            'created_at'   => ['type' => 'datetime', 'null' => false],
        ]);
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'cascade');
        $this->forge->addUniqueKey('user_id');
        $this->forge->createTable('users_delete', true);
    }

    public function down()
    {
        $this->forge->dropTable('users_delete', true);
    }
}
