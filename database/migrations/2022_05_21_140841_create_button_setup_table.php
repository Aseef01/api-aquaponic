<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('button_setups', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('device_inputs_id');
            $table->json('timer')->nullable();
            // $table->integer('total_auto_on')->default(0);
            $table->json('jam_berapa')->nullable();
            $table->tinyInteger('status')->default(0);

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
        Schema::dropIfExists('button_setup');
    }
};
