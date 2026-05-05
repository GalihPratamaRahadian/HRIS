<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notifications', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_user');
			$table->string('type')->default('info');
			$table->string('title');
			$table->text('description')->nullable();
			$table->string('is_read')->default('no');
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
		Schema::dropIfExists('notifications');
	}
}
