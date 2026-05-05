<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseExamParticipantAnswersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_exam_participant_answers', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_course_exam_participant')->nullable();
			$table->bigInteger('id_course_exam_question')->nullable();
			$table->bigInteger('id_course_exam_answer')->nullable();
			$table->string('alphabet_answer')->nullable();
			$table->string('is_correct')->nullable();
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
		Schema::dropIfExists('course_exam_participant_answers');
	}
}
