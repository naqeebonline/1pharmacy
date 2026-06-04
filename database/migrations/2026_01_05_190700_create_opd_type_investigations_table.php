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
        Schema::create('opd_type_investigations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('opd_type_id');
            $table->unsignedBigInteger('investigation_sub_category_id');
            $table->timestamps();

            $table->foreign('opd_type_id')->references('id')->on('opd_type')->onDelete('cascade');
            $table->foreign('investigation_sub_category_id')->references('id')->on('investigation_sub_category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opd_type_investigations');
    }
};
