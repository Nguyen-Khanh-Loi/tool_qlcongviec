<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeignKeyColumnInTableNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')
                  ->nullable() // Set your desired default value here
                  ->change();
            $table->unsignedBigInteger('task_id')
                  ->nullable()// Set your desired default value here
                  ->change();
            $table->dropForeign(['user_id']);
            $table->dropForeign(['task_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreign('task_id')
            ->references('id')
            ->on('tasks');
            $table->foreign('user_id')
            ->references('id')
            ->on('users');
        });
    }
}
