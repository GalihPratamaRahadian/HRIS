<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveSubmissionApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_submission_approvals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_leave_submission');
            $table->bigInteger('id_department');
            $table->integer('position_level');
            $table->string('status')->default('wait');
            $table->bigInteger('id_user')->nullable();
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
        Schema::dropIfExists('leave_submission_approvals');
    }
}
