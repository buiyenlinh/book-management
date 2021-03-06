<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('describe')->default('');
            $table->string('language');
            $table->string('cover_image')->default('');
            $table->string('producer')->default(''); 
            $table->timestamp('release_time');
            $table->foreignId('author_id')
                ->contranted('authors')
                ->unsigned();

            $table->string('mp3')->default('');
            $table->foreignId('category_id')
                ->constranted('categories')
                ->unsigned()
                ->onDelete('cascade');
            $table->boolean('status')->default(1);
            $table->boolean('free')->default(1);
            $table->string('username');
            $table->string('alias')->unique();
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
        Schema::dropIfExists('books');
    }
}
