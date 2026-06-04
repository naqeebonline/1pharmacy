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
        // Create grn_returns table
        Schema::create('grn_returns', function (Blueprint $table) {
            $table->id('ReturnID');
            $table->unsignedBigInteger('SCID')->comment('Supplier ID');
            $table->date('ReturnDate');
            $table->decimal('TotalAmount', 15, 2)->default(0);
            $table->enum('Status', ['Pending', 'Approved', 'Rejected', 'Completed'])->default('Pending');
            $table->unsignedBigInteger('CreatedBy')->nullable();
            $table->unsignedBigInteger('ApprovedBy')->nullable();
            $table->timestamp('CreatedAt')->nullable();
            $table->timestamp('ApprovedAt')->nullable();
            $table->text('Remarks')->nullable();

            $table->index('SCID');
            $table->index('ReturnDate');
            $table->index('Status');
        });

        // Create grn_return_details table
        Schema::create('grn_return_details', function (Blueprint $table) {
            $table->id('ReturnDetailID');
            $table->unsignedBigInteger('ReturnID');
            $table->unsignedBigInteger('GDID')->comment('GRN Detail ID');
            $table->unsignedBigInteger('ProductID');
            $table->string('BatchNo', 100)->nullable();
            $table->date('ExpiryDate')->nullable();
            $table->decimal('ReturnQuantity', 15, 2);
            $table->decimal('UnitPrice', 15, 2);
            $table->decimal('TotalAmount', 15, 2);
            $table->timestamp('CreatedAt')->nullable();

            $table->foreign('ReturnID')->references('ReturnID')->on('grn_returns')->onDelete('cascade');
            $table->index('GDID');
            $table->index('ProductID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grn_return_details');
        Schema::dropIfExists('grn_returns');
    }
};
