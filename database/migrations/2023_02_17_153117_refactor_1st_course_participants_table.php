<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor1stCourseParticipantsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('course_participants', function (Blueprint $table) {
			$table->string('video_passed')->after('have_passed')->default('no')->nullable();
			$table->timestamp('video_passed_at')->after('video_passed')->nullable();
			$table->bigInteger('video_seconds_passed')->default(0)->after('video_passed_at')->nullable();
			$table->bigInteger('video_duration')->default(0)->after('video_seconds_passed')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('course_participants', function (Blueprint $table) {
			$table->dropColumn('video_passed');
			$table->dropColumn('video_passed_at');
			$table->dropColumn('video_minutes_passed');
		});
	}
}
