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
            $table->string('describe');
            $table->string('language');
            $table->integer('page_total');
            $table->string('cover_image');
            $table->string('producer');
            $table->string('author');
            $table->longText('content');
            $table->string('mp3')->nullable();
            $table->foreignId('category_id')
                ->constranted('categories')
                ->unsigned()
                ->onDelete('cascade');
            $table->boolean('status');
            $table->string('username');
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
