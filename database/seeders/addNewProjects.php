<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class addNewProjects extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // $table->unsignedBigInteger('user_id');
        // $table->string('title');
        // $table->string('slug');
        // $table->timestamp('start_time')->nullable();
        // $table->timestamp('end_time')->nullable();
        // $table->tinyInteger('status')->default(0);      
        $data = [
            'title' => 'Project Demo',
            'user_id' => 2,
            'slug' => 'project-demo',
            'start_time' => date('Y-m-d H:i:s'),
            'end_time' => date('Y-m-d H:i:s'),
            'status' => 0,
        ];
        DB::table('projects')->insert($data);
    }
}
