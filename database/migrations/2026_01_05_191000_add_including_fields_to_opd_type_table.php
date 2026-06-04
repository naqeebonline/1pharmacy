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
        Schema::table('opd_type', function (Blueprint $table) {
            if (!Schema::hasColumn('opd_type', 'including_medicine')) {
                $table->tinyInteger('including_medicine')->default(0)->after('fees');
            }
            if (!Schema::hasColumn('opd_type', 'including_labs')) {
                $table->tinyInteger('including_labs')->default(0)->after('including_medicine');
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
        Schema::table('opd_type', function (Blueprint $table) {
            if (Schema::hasColumn('opd_type', 'including_medicine')) {
                $table->dropColumn('including_medicine');
            }
            if (Schema::hasColumn('opd_type', 'including_labs')) {
                $table->dropColumn('including_labs');
            }
        });
    }
};
