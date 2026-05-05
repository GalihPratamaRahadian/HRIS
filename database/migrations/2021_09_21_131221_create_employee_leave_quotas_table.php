<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeLeaveQuotasTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_leave_quotas', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_employee');
			$table->string('period_type');
			$table->integer('quota');
			$table->integer('quota_available')->default(0);
			$table->integer('quota_used')->default(0);
			$table->string('is_allow_accumulation', 3)->default('no');
			$table->timestamp('reset_at')->nullable();
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
		Schema::dropIfExists('employee_leave_quotas');
	}
}
