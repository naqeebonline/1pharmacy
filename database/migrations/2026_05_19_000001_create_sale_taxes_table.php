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
        if (Schema::hasTable('sale_taxes')) {
            return;
        }

        Schema::create('sale_taxes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sale_id')->nullable();
            $table->unsignedInteger('tax_id')->nullable();
            $table->decimal('tax_percentage', 11, 0)->default(0);

            $table->index('sale_id');
            $table->index('tax_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_taxes');
    }
};
