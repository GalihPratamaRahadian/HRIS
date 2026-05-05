<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNecessityReasonsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('necessity_reasons', function (Blueprint $table) {
			$table->id();
			$table->string('reason');
			$table->integer('max_duration')->default(0);
			$table->string('is_using_max_duration')->default('no')->nullable();
			$table->string('is_counted_present')->default('no')->nullable();
			$table->string('is_required_file')->default('no');
			$table->softDeletes();
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
		Schema::dropIfExists('necessity_reasons');
	}
}
