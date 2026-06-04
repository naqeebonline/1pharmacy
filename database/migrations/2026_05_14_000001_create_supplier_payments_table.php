<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->increments('id');

            // Similar to sale_payments, but for suppliers instead of patients
            $table->unsignedInteger('SCID');
            $table->unsignedBigInteger('admission_id')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();

            $table->decimal('amount', 10, 2);
            $table->string('remarks', 255)->nullable();

            $table->unsignedInteger('created_by');
            $table->dateTime('created_at');

            $table->integer('is_posted')->default(0);
            $table->dateTime('posted_on')->nullable();

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->integer('is_active')->default(1);
            $table->boolean('is_sync')->default(false);

            $table->index('SCID');

            // Keep behavior consistent with legacy schema style (no cascading by default)
            $table->foreign('SCID')->references('SCID')->on('sup_cus_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
