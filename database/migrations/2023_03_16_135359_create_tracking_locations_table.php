<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingLocationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tracking_locations', function (Blueprint $table) {
			$table->id();
			$table->string('location_name');
			$table->string('latitude')->nullable();
			$table->string('longitude')->nullable();
			$table->string('photo')->nullable();
			$table->text('description')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('tracking_locations');
	}
}
