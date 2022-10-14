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
            $table->unsignedBigInteger('belong_user_id')->after('message');
            $table->index('belong_user_id', 'belong_user_idx');
            $table->unsignedBigInteger('relation_user_id')->after('belong_user_id');
            $table->index('relation_user_id', 'relation_user_idx');
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
            $table->dropColumn('belong_user_id');
            $table->dropColumn('relation_user_id');
        });
    }
};
