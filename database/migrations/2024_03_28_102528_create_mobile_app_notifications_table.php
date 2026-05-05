<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileAppNotificationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mobile_app_notifications', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_user')->nullable();
			$table->string('title')->nullable();
			$table->string('message')->nullable();
			$table->string('type')->nullable();
			$table->bigInteger('id_reference')->nullable();
			$table->timestamp('notify_at')->nullable();
			$table->string('delivered')->default('No')->nullable();
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
		Schema::dropIfExists('mobile_app_notifications');
	}
}
