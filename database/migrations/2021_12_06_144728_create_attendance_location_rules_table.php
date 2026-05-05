<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceLocationRulesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_location_rules', function (Blueprint $table) {
			$table->id();
			$table->string('location_name');
			$table->string('latitude');
			$table->string('longitude');
			$table->string('radius_distance')->default(0);
			$table->string('radius_unit')->default('m');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('attendance_location_rules');
	}
}
