<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataColumnInTableNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            //
            $table->integer('project_id')->default(0)->after('id')->comment('project id');
            $table->string('action')->after('author_id');
            $table->string('type')->after('action');
            $table->text('description')->after('action');
            $table->dropColumn('status_add_user');
            $table->dropColumn('date_add_user');
            $table->dropColumn('date_remove_user');
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
            //
            $table->dropColumn('project_id');
            $table->dropColumn('action');
            $table->dropColumn('type');
            $table->dropColumn('description');
            $table->tinyInteger('status_add_user');
            $table->timestamp('date_add_user');
            $table->timestamp('date_remove_user');
        });
    }
}
