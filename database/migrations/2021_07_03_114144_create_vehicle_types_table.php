<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->char('category');
            $table->unsignedBigInteger('parking_lot_id');
            $table->unsignedDecimal('daily_rate', 9, 4);
            $table->unsignedDecimal('night_rate', 9, 4);
            $table->integer('parking_space');
            $table->timestamps();
            $table->softDeletes();
            // $table->boolean('is_active')->default(true);

            $table->foreign('parking_lot_id')->references('id')->on('parking_lots')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_types');
    }
}
