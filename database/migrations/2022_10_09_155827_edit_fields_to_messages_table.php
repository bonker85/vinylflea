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
        Schema::table('messages', function (Blueprint $table) {
          //  $table->dropColumn('belong_user_id');
        //    $table->dropColumn('relation_user_id');
            $table->unsignedBigInteger('advert_dialog_id')->after('id');
            $table->index('advert_dialog_id', 'advert_dialog_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            //
        });
    }
};
