<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor1stTrackingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('trackings', function (Blueprint $table) {
			$table->string('check_day_photo')->nullable()->after('check_in_photo');
			$table->timestamp('check_day_at')->nullable()->after('check_in_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('trackings', function (Blueprint $table) {
			$table->dropColumn('check_day_photo');
			$table->dropColumn('check_day_at');
		});
	}
}
