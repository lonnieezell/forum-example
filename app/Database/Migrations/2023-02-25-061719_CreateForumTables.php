<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateForumTables extends Migration
{
    public function up()
    {
        $this->db->disableForeignKeyChecks();

        // Forums
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'varchar', 'constraint' => 255],
            'slug' => ['type' => 'varchar', 'constraint' => 255],
            'description' => ['type' => 'text', 'null' => true],
            'parent_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'order' => ['type' => 'tinyint', 'constraint' => 2, 'default' => 1],
            'active' => ['type' => 'tinyint', 'constraint' => 1, 'default' => 0],
            'private' => ['type' => 'tinyint', 'constraint' => 1, 'default' => 0],
            'permissions' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'thread_count' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'post_count' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'last_thread_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => false],
            'updated_at' => ['type' => 'datetime', 'null' => false],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('parent_id', 'forums', 'id', '', 'set null');
        $this->forge->addForeignKey('last_thread_id', 'threads', 'id', '', 'set null');
        $this->forge->createTable('forums', true);

        // Threads
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'forum_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'title' => ['type' => 'varchar', 'constraint' => 255],
            'slug' => ['type' => 'varchar', 'constraint' => 255],
            'body' => ['type' => 'text', 'null' => true],
            'author_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'editor_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'edited_at' => ['type' => 'datetime', 'null' => true],
            'edited_reason' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'views' => ['type' => 'int', 'constraint' => 9, 'unsigned' => true, 'default' => 0],
            'closed' => ['type' => 'tinyint', 'constraint' => 2, 'default' => 0],
            'sticky' => ['type' => 'tinyint', 'constraint' => 2, 'default' => 0],
            'visible' => ['type' => 'tinyint', 'constraint' => 2, 'default' => 0],
            'last_post_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'post_count' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'answer_post_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'markup' => ['type' => 'varchar', 'constraint' => 255, 'default' => 'markdown'],
            'created_at' => ['type' => 'datetime', 'null' => false],
            'updated_at' => ['type' => 'datetime', 'null' => false],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('forum_id', 'forums', 'id', '', 'delete');
        $this->forge->addForeignKey('author_id', 'users', 'id', '', 'delete');
        $this->forge->addForeignKey('editor_id', 'users', 'id', '', 'delete');
        $this->forge->addForeignKey('last_post_id', 'posts', 'id', '', 'delete');
        $this->forge->addForeignKey('answer_post_id', 'posts', 'id', '', 'delete');
        $this->forge->createTable('threads', true);

        // Posts
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'forum_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'thread_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'reply_to' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'author_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'editor_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'edited_at' => ['type' => 'datetime', 'null' => true],
            'edited_reason' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'body' => ['type' => 'text', 'null' => true],
            'ip_address' => ['type' => 'varchar', 'constraint' => 45, 'null' => true],
            'include_sig' => ['type' => 'tinyint', 'constraint' => 2, 'default' => 0],
            'visible' => ['type' => 'tinyint', 'constraint' => 2, 'default' => 0],
            'markup' => ['type' => 'varchar', 'constraint' => 255, 'default' => 'markdown'],
            'created_at' => ['type' => 'datetime', 'null' => false],
            'updated_at' => ['type' => 'datetime', 'null' => false],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('forum_id', 'forums', 'id', '', 'delete');
        $this->forge->addForeignKey('thread_id', 'threads', 'id', '', 'delete');
        $this->forge->addForeignKey('reply_to', 'posts', 'id', '', 'delete');
        $this->forge->addForeignKey('author_id', 'users', 'id', '', 'delete');
        $this->forge->addForeignKey('editor_id', 'users', 'id', '', 'delete');
        $this->forge->createTable('posts', true);

        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->db->disableForeignKeyChecks();

        $this->forge->dropTable('posts', true);
        $this->forge->dropTable('threads', true);
        $this->forge->dropTable('forums', true);

        $this->db->enableForeignKeyChecks();
    }
}
