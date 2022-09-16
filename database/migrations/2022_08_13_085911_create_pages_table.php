<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->unique();
            $table->text('content');
            $table->string('header');
            $table->string('title');
            $table->string('description');
            $table->string('keywords');
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->index('parent_id', 'page_parent_idx');
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
