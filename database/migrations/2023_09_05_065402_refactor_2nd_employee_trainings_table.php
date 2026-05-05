<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor2ndEmployeeTrainingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('employee_trainings', function (Blueprint $table) {
			$table->bigInteger('id_course_participant')->after('description')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('employee_trainings', function (Blueprint $table) {
			$table->dropColumn('id_course_participant');
		});
	}
}
