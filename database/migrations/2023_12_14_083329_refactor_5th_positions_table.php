<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Refactor5thPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->string('is_must_attend')->default('Ya')->nullable()->after('approver_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('is_must_attend');
        });
    }
}
