<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseExamAnswerOptionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_exam_answer_options', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_course_exam_question');
			$table->text('answer')->nullable();
			$table->string('is_correct')->default('no')->nullable();
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
		Schema::dropIfExists('course_exam_answer_options');
	}
}
