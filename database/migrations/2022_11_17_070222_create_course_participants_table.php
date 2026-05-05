<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseParticipantsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_participants', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_course')->nullable();
			$table->bigInteger('id_employee')->nullable();
			$table->string('have_passed')->default('no')->nullable();
			$table->timestamp('passed_at')->nullable();
			$table->timestamp('started_at')->nullable();
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
		Schema::dropIfExists('course_participants');
	}
}
