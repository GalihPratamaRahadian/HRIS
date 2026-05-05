<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseExamParticipantsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_exam_participants', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_course')->nullable();
			$table->bigInteger('id_employee')->nullable();
			$table->timestamp('started_at')->nullable();
			$table->timestamp('ended_at')->nullable();
			$table->string('status')->default('ongoing')->nullable();
			$table->string('result')->nullable();
			$table->integer('correct_answer')->default(0)->nullable();
			$table->integer('incorrect_answer')->default(0)->nullable();
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
		Schema::dropIfExists('course_exam_participants');
	}
}
