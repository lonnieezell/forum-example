<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCountFieldsToUsers extends Migration
{
    public function up()
    {
        // Users extra fields
        $fields = [
            'thread_count' => [
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'after'      => 'handed',
            ],
            'post_count' => [
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'after'      => 'thread_count',
            ],
            'avatar' => [
                'type'       => 'varchar',
                'constraint' => 64,
                'null'       => true,
                'default'    => null,
                'after'      => 'post_count',
            ],
            'country' => [
                'type'       => 'varchar',
                'constraint' => 32,
                'null'       => true,
                'default'    => null,
                'after'      => 'avatar',
            ],
            'timezone' => [
                'type'       => 'varchar',
                'constraint' => 32,
                'null'       => false,
                'default'    => 'UTC',
                'after'      => 'country',
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'thread_count');
        $this->forge->dropColumn('users', 'post_count');
        $this->forge->dropColumn('users', 'avatar');
        $this->forge->dropColumn('users', 'country');
        $this->forge->dropColumn('users', 'timezone');
    }
}
