<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignkeyColumTableChecklists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->dropForeign(['task_id']);          
            $table->renameColumn('task_id', 'work_todo_id');
            $table->foreign('work_todo_id')
                ->references('id')
                ->on('work_todo')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign(['task_id']);          
        $table->renameColumn('task_id', 'work_todo_id');;
        $table->foreign('work_todo_id')
            ->references('id')
            ->on('work_todo')
            ->onDelete('cascade');
    }
}
