<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateImageTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'thread_id'  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'post_id'    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'name'       => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'size'       => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'is_used'    => ['type' => 'tinyint', 'constraint' => 2, 'default' => 0],
            'ip_address' => ['type' => 'varchar', 'constraint' => 45, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => false],
            'updated_at' => ['type' => 'datetime', 'null' => false],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'set null');
        $this->forge->addForeignKey('thread_id', 'threads', 'id', '', 'set null');
        $this->forge->addForeignKey('post_id', 'posts', 'id', '', 'set null');
        $this->forge->createTable('images', true);
    }

    public function down()
    {
        $this->forge->dropTable('images', true);
    }
}
