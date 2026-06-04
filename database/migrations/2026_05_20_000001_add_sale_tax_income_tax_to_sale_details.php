<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sale_details', 'sale_tax')) {
            Schema::table('sale_details', function (Blueprint $table) {
                $table->decimal('sale_tax', 10, 2)->default(0)->after('taxAmount');
                $table->decimal('income_tax', 10, 2)->default(0)->after('sale_tax');
            });
        }

        if (!Schema::hasColumn('temp_sale_details', 'sale_tax')) {
            Schema::table('temp_sale_details', function (Blueprint $table) {
                $table->decimal('sale_tax', 10, 2)->default(0)->after('taxAmount');
                $table->decimal('income_tax', 10, 2)->default(0)->after('sale_tax');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sale_details', 'sale_tax')) {
            Schema::table('sale_details', function (Blueprint $table) {
                $table->dropColumn(['sale_tax', 'income_tax']);
            });
        }

        if (Schema::hasColumn('temp_sale_details', 'sale_tax')) {
            Schema::table('temp_sale_details', function (Blueprint $table) {
                $table->dropColumn(['sale_tax', 'income_tax']);
            });
        }
    }
};
