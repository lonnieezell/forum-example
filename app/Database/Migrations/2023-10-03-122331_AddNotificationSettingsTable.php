<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotificationSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id'                  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'email_thread'             => ['type' => 'tinyint', 'constraint' => 1, 'unsigned' => true, 'null' => false, 'default' => 0],
            'email_post'               => ['type' => 'tinyint', 'constraint' => 1, 'unsigned' => true, 'null' => false, 'default' => 0],
            'email_post_reply'         => ['type' => 'tinyint', 'constraint' => 1, 'unsigned' => true, 'null' => false, 'default' => 0],
            'moderation_daily_summary' => ['type' => 'tinyint', 'constraint' => 1, 'unsigned' => true, 'null' => false, 'default' => 0],
            'created_at'               => ['type' => 'datetime', 'null' => false],
            'updated_at'               => ['type' => 'datetime', 'null' => false],
        ]);
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'cascade');
        $this->forge->addUniqueKey('user_id');
        $this->forge->createTable('notification_settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('notification_settings', true);
    }
}
