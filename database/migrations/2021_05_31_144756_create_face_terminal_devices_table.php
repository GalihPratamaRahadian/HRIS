<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaceTerminalDevicesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('face_terminal_devices', function (Blueprint $table) {
			$table->id();
			$table->string('device_name');
			$table->string('ip_address');
			$table->integer('port');
			$table->string('username');
			$table->string('password');
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
		Schema::dropIfExists('face_terminal_devices');
	}
}
