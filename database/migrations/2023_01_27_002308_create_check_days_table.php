<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_days', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_employee')->nullable();
            $table->timestamp('check_day_at')->nullable();
            $table->string('photo')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
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
        Schema::dropIfExists('check_days');
    }
}
