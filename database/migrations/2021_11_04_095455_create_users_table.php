<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('fullname');
            $table->string('username')->unique();
            $table->string('email')->default('');
            $table->string('password');
            $table->boolean('active');
            $table->enum('gender', ['N', 'F', 'M'])->default('N');
            $table->timestamp('birthday');
            $table->string('address')->default('');
            $table->string('avatar')->default('');
            $table->foreignId('role_id')->constrained('roles')->unsigned ();
            $table->string('token')->default('');
            $table->string('oauth2')->default('');
            $table->timestamps();
        });
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
