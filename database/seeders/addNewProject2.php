<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class addNewProject2 extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$data = array(
			'title'      => 'Project Demo 4',
			'user_id'    => 2,
			'slug'       => 'project-demo-4',
			'start_time' => date( 'Y-m-d H:i:s' ),
			'end_time'   => date( 'Y-m-d H:i:s' ),
			'status'     => 0,
		);
		DB::table( 'projects' )->insert( $data );
	}
}
