 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SettingsTable extends Migration
{
    
    public function up()
    {
        Schema::create('settings', function(Blueprint $table){
            $table->increments('id');
            $table->string('setting_name');
            $table->text('setting_value')->nullable();
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
