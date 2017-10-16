<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert([
            [
                'name' => 'testing user1',
                'email' => 'test1email@test.com',
                'password' => bcrypt('secret'),
            ],
            [
                'name' => 'testing user2',
                'email' => 'test2email@gmail.com',
                'password' => bcrypt('secret'),
            ],
            [
                'name' => 'testing user3',
                'email' => 'test3email@test.com',
                'password' => bcrypt('secret'),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
