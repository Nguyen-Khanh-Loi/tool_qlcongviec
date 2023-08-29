<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeColumnInTableTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('project_id')
                  ->nullable() // Set your desired default value here
                  ->change();
            $table->unsignedBigInteger('card_id')
                  ->nullable()// Set your desired default value here
                  ->change();
            $table->unsignedBigInteger('department_id')
                  ->nullable() // Set your desired default value here
                  ->change();
            $table->renameColumn('status_deadline', 'status');
            $table->dropForeign(['project_id']);
            $table->dropForeign(['card_id']);
            $table->dropForeign(['department_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
            $table->foreign('project_id')
            ->references('id')
            ->on('projects');
            $table->foreign('card_id')
            ->references('id')
            ->on('cards');
            $table->foreign('department_id ')
            ->references('id')
            ->on('departments');
        });
    }
}
