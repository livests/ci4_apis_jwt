<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTokenBlacklistedTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
                    'id' => [
                        'type' => 'INT',
                        'unsigned' => true,
                        'auto_increment' => true,
                    ],
                    'token' => [
                        'type' => 'TEXT',
                        'null' => false
                    ],
                    'created_at datetime default current_timestamp'
                        
                    

                ]);
                $this->forge->addPrimaryKey('id');
                $this->forge->createTable('token_blacklisted');
    }

    public function down()
    {
        $this->forge->dropTable('token_blacklisted');
    }
}
