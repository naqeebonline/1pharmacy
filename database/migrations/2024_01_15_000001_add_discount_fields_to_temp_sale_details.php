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
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('temp_sale_details', 'discount_percentage')) {
                $table->decimal('discount_percentage', 8, 2)->default(0)->after('taxAmount');
            }
            if (!Schema::hasColumn('temp_sale_details', 'discount_percentage_amount')) {
                $table->decimal('discount_percentage_amount', 10, 2)->default(0)->after('discount_percentage');
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
            if (Schema::hasColumn('temp_sale_details', 'discount_percentage')) {
                $table->dropColumn('discount_percentage');
            }
            if (Schema::hasColumn('temp_sale_details', 'discount_percentage_amount')) {
                $table->dropColumn('discount_percentage_amount');
            }
        });
    }
};
