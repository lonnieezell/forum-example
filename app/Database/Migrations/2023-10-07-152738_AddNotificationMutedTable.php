<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotificationMutedTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id'    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'thread_id'  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'datetime', 'null' => false],
        ]);
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'cascade');
        $this->forge->addForeignKey('thread_id', 'threads', 'id', '', 'cascade');
        $this->forge->addUniqueKey(['user_id', 'thread_id']);
        $this->forge->createTable('notification_muted', true);
    }

    public function down()
    {
        $this->forge->dropTable('notification_muted', true);
    }
}
