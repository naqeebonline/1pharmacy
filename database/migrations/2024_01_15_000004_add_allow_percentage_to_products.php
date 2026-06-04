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
        Schema::table('products', function (Blueprint $table) {
            // Check if column doesn't exist before adding it
            if (!Schema::hasColumn('products', 'allow_percentage')) {
                $table->decimal('allow_percentage', 5, 2)->default(0)->after('BarCode')->comment('Allowed discount percentage for this product');
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
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'allow_percentage')) {
                $table->dropColumn('allow_percentage');
            }
        });
    }
};
