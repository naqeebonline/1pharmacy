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
        Schema::table('temp_sale_details', function (Blueprint $table) {
            // Check if column doesn't exist before adding it
            if (!Schema::hasColumn('temp_sale_details', 'ReturnQuantity')) {
                $table->decimal('ReturnQuantity', 10, 2)->default(0)->after('Quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_sale_details', function (Blueprint $table) {
            if (Schema::hasColumn('temp_sale_details', 'ReturnQuantity')) {
                $table->dropColumn('ReturnQuantity');
            }
        });
    }
};
