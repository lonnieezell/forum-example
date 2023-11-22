<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddModerationTables extends Migration
{
    public function up()
    {
        // moderation_reports table
        $this->forge->addField([
            'id'            => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'resource_id'   => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'resource_type' => ['type' => 'varchar', 'constraint' => 64, 'null' => false],
            'comment'       => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'author_id'     => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'    => ['type' => 'datetime', 'null' => false],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['resource_id', 'resource_type', 'author_id'], 'moderation_reports_resource_author');
        $this->forge->addForeignKey('author_id', 'users', 'id', '', 'cascade');
        $this->forge->createTable('moderation_reports', true);

        // moderation_logs table
        $this->forge->addField([
            'id'            => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'resource_id'   => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'resource_type' => ['type' => 'varchar', 'constraint' => 64, 'null' => false],
            'author_id'     => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'status'        => ['type' => 'enum', 'constraint' => ['approved', 'denied'], 'null' => false],
            'created_at'    => ['type' => 'datetime', 'null' => false],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('author_id', 'users', 'id', '', 'cascade');
        $this->forge->createTable('moderation_logs', true);

        // moderation_ignored table
        $this->forge->addField([
            'id'                   => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'moderation_report_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'user_id'              => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'           => ['type' => 'datetime', 'null' => false],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('moderation_report_id', 'moderation_reports', 'id', '', 'cascade');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'cascade');
        $this->forge->createTable('moderation_ignored', true);
    }

    public function down()
    {
        $this->forge->dropTable('moderation_ignored', true);
        $this->forge->dropTable('moderation_logs', true);
        $this->forge->dropTable('moderation_reports', true);
    }
}
