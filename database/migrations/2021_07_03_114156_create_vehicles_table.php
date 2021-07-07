<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('registration_plate');
            $table->unsignedBigInteger('parking_lot_id');
            $table->unsignedBigInteger('vehicle_type_id');
            $table->unsignedBigInteger('card_id')->nullable();
            $table->dateTime('entered_at', $precision = 0);
            $table->dateTime('exited_at', $precision = 0)->nullable();
            $table->unsignedDecimal('price_of_exit', 65, 2)->nullable();;
            $table->boolean('in_parking');
            $table->timestamps();
            $table->softDeletes();
            // $table->boolean('is_active')->default(true);

            $table->foreign('parking_lot_id')->references('id')->on('parking_lots')->onDelete('cascade');
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onDelete('cascade');
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
