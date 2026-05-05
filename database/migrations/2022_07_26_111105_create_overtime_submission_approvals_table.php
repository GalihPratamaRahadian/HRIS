<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOvertimeSubmissionApprovalsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('overtime_submission_approvals', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_overtime_submission');
			$table->bigInteger('id_approver_position')->nullable();
			$table->bigInteger('id_user')->nullable();
			$table->string('status')->default('wait');
			$table->timestamp('approved_at')->nullable();
			$table->timestamp('rejected_at')->nullable();
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
		Schema::dropIfExists('overtime_submission_approvals');
	}
}
