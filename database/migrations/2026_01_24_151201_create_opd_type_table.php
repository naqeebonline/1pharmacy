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
        Schema::create('opd_type', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('fees', 10, 2);
            $table->integer('including_medicine')->default(0);
            $table->integer('including_labs')->default(0);
            $table->tinyInteger('is_sync')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opd_type');
    }
};
