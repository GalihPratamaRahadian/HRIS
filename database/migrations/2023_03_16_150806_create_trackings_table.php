<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trackings', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee')->nullable();
			$table->bigInteger('id_tracking_location')->nullable();
			$table->string('check_in_photo')->nullable();
			$table->string('check_out_photo')->nullable();
			$table->string('latitude')->nullable();
			$table->string('longitude')->nullable();
			$table->string('file_good_receipt')->nullable();
			$table->timestamp('check_in_at')->nullable();
			$table->timestamp('check_out_at')->nullable();
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
		Schema::dropIfExists('trackings');
	}
}
