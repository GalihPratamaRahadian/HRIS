<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor2ndCoursesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('courses', function (Blueprint $table) {
			$table->string('pass_requirement')->default('pass_exam')->after('deadline')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('courses', function (Blueprint $table) {
			$table->dropColumn('pass_requirement');
		});
	}
}
