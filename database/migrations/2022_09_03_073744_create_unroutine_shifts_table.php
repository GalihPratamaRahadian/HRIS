<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnroutineShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unroutine_shifts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_employee')->nullable();
            $table->date('date')->nullable();
            $table->string('type')->nullable();
            $table->time('clock_start_limit')->nullable();
            $table->time('clock_start')->nullable();
            $table->time('clock_end')->nullable();
            $table->integer('late_tolerance')->default(0);
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
        Schema::dropIfExists('unroutine_shifts');
    }
}
