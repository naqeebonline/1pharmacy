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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number', 100)->nullable();
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('consultant_id');
            $table->unsignedInteger('opd_type_id');
            $table->datetime('appointment_date')->nullable();
            $table->decimal('fee', 10, 2);
            $table->decimal('hospital_share', 10, 2)->default(0.00);
            $table->decimal('consultant_share', 10, 2)->default(0.00);
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->integer('is_posted')->default(0);
            $table->datetime('posted_on')->nullable();
            $table->integer('is_active')->default(1);
            $table->integer('is_sync')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};
