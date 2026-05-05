<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_metas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_attendance');
            $table->string('clock_in_photo')->nullable();
            $table->string('clock_out_photo')->nullable();
            $table->text('clock_in_location')->nullable();
            $table->text('clock_out_location')->nullable();
            $table->bigInteger('id_clock_in_face_terminal_log')->nullable();
            $table->bigInteger('id_clock_out_face_terminal_log')->nullable();
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
        Schema::dropIfExists('attendance_metas');
    }
}
