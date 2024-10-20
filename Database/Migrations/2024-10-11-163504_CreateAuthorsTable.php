<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthorsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'    => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'  => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => false],
            'email'   => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => false],
            'password' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'phone_no' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'created_at  datetime default current_timestamp'

        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('authors');
    }

    public function down()
    {
        $this->forge->dropTable('authors');
    }
}
