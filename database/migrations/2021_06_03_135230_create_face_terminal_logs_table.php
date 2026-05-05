<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaceTerminalLogsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('face_terminal_logs', function (Blueprint $table) {
			$table->id();
			$table->dateTime('date');
			$table->string('device_name');
			$table->bigInteger('auth_id')->nullable();
			$table->string('name');
			$table->string('from')->nullable();
			$table->string('temperature')->nullable();
			$table->tinyInteger('mask')->nullable();
			$table->string('photo')->nullable();
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
		Schema::dropIfExists('face_terminal_logs');
	}
}
