<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnInTableLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs', function (Blueprint $table) {
            if (!Schema::hasColumn('logs', 'target_id')) {
                $table->integer('target_id')->after('type');
            }
            if (!Schema::hasColumn('logs', 'action')) {
                $table->string('action')->after('target_id');
            }
            $table->integer('user_id')->default(0)->after('type')->comment('id user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn('target_id');
            $table->dropColumn('action');
            $table->dropColumn('user_id');
        });
    }
}
