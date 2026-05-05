<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add3rdColumnsFaceTerminalDevicesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('face_terminal_devices', function (Blueprint $table) {
			$table->tinyInteger('status')->default(1)->after('meta');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('face_terminal_devices', function (Blueprint $table) {
			$table->dropColumn('status');
		});
	}
}
