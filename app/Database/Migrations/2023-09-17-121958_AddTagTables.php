<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTagTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'varchar', 'constraint' => 32, 'null' => false],
            'created_at' => ['type' => 'datetime', 'null' => false],
            'updated_at' => ['type' => 'datetime', 'null' => false],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('tags', true);

        $this->forge->addField([
            'tag_id'    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'thread_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addForeignKey('tag_id', 'tags', 'id', '', 'delete');
        $this->forge->addForeignKey('thread_id', 'threads', 'id', '', 'delete');
        $this->forge->createTable('thread_tags', true);
    }

    public function down()
    {
        $this->forge->dropTable('tags', true);
        $this->forge->dropTable('thread_tags', true);
    }
}
