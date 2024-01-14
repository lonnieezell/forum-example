<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostsLikesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true,],
            'post_id' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'null' => true],
            'thread_id' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'null' => true],
            'reactor_id' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true],
            'reaction' => ['type' => 'TINYINT'],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
            'updated_at' => ['type' => 'DATETIME', 'null' => false],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('post_id', 'posts', 'id', false, 'SET NULL');
        $this->forge->addForeignKey('thread_id', 'threads', 'id', false, 'SET NULL');
        $this->forge->addForeignKey('reactor_id', 'users', 'id', false, 'CASCADE');
        $this->forge->createTable('reactions', true);

        // Update the 'posts' and 'threads' table to include a reaction count, de-normalized for performance.
        $this->forge->addColumn('posts', [
            'reaction_count' => ['type' => 'INT', 'constraint' => 9, 'unsigned' => true, 'default' => 0],
        ]);
        $this->forge->addColumn('threads', [
            'reaction_count' => ['type' => 'INT', 'constraint' => 9, 'unsigned' => true, 'default' => 0],
        ]);
        $this->forge->addColumn('users', [
            'reaction_count' => ['type' => 'INT', 'constraint' => 9, 'unsigned' => true, 'default' => 0],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('reactions', true);
        $this->forge->dropColumn('posts', 'reaction_count');
        $this->forge->dropColumn('threads', 'reaction_count');
        $this->forge->dropColumn('users', 'reaction_count');
    }
}
