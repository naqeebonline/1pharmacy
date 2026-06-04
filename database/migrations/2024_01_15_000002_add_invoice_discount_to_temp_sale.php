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
        Schema::table('temp_sale', function (Blueprint $table) {
            // Check if column doesn't exist before adding it
            if (!Schema::hasColumn('temp_sale', 'invoice_discount')) {
                $table->decimal('invoice_discount', 10, 2)->default(0)->after('Discount');
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
        Schema::table('temp_sale', function (Blueprint $table) {
            if (Schema::hasColumn('temp_sale', 'invoice_discount')) {
                $table->dropColumn('invoice_discount');
            }
        });
    }
};
