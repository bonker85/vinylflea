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
        Schema::create('advert_dialogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advert_id');
            $table->index('advert_id', 'advert_idx');
            $table->unsignedBigInteger('from_user_id');
            $table->index('from_user_id', 'from_user_idx');
            $table->unsignedBigInteger('to_user_id');
            $table->index('to_user_id', 'to_user_idx');
            $table->unsignedSmallInteger('count_not_view_user_from');
            $table->unsignedSmallInteger('count_not_view_user_to');
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
        Schema::dropIfExists('advert_dialogs');
    }
};
