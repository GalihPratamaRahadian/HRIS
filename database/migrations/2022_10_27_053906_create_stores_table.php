<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stores', function (Blueprint $table) {
			$table->id();
			$table->string('store_name')->nullable();
			$table->string('phone_number')->nullable();
			$table->string('address')->nullable();
			$table->string('latitude')->nullable();
			$table->string('longitude')->nullable();
			$table->bigInteger('registered_by')->nullable();
			$table->bigInteger('handled_by')->nullable();
			$table->timestamp('last_visited_at')->nullable();
			$table->string('partner_status')->nullable()->default('active');
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
		Schema::dropIfExists('stores');
	}
}
