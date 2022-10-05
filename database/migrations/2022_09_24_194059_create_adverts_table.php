<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adverts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->unique();
            $table->string('description', 1100);
            $table->unsignedBigInteger('style_id');
            $table->index('style_id', 'style_idx');
            $table->unsignedBigInteger('edition_id')->nullable();
            $table->index('edition_id', 'edition_idx');
            $table->unsignedBigInteger('user_id');
            $table->index('user_id', 'user_idx');
            $table->integer('year')->nullable();
            $table->enum('state', [1,2]);
            $table->enum('status', [1,2,3,4])->default(2);
            $table->timestamp('up_time')->nullable();
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
        Schema::dropIfExists('adverts');
    }
};
