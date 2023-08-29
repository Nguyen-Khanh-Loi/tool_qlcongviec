<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDefaultValueColumnInTableNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->integer('author_id')
                  ->default(0) // Set your desired default value here
                  ->change();
            $table->string('action')
                  ->nullable() // Set your desired default value here
                  ->change();
            $table->string('type')
                  ->nullable() // Set your desired default value here
                  ->change();
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
            $table->dropColumn('action');
            $table->dropColumn('type');
        });
    }
}
