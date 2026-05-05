<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancePermissionSubmissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_permission_submissions', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee');
			$table->string('type');
			$table->date('date');
			$table->time('time')->nullable();
			$table->string('reason')->nullable();
			$table->string('status')->default('wait');
			$table->timestamp('approved_at')->nullable();
			$table->timestamp('rejected_at')->nullable();
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
		Schema::dropIfExists('attendance_permission_submissions');
	}
}
