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
        if (!Schema::hasTable('patient_investigations_payments')) {
            Schema::create('patient_investigations_payments', function (Blueprint $table) {
                // Set engine / charset
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

                $table->increments('id');
                $table->integer('patient_id');
                $table->integer('admission_id')->nullable();
                $table->bigInteger('invoice_no')->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('remarks', 255)->default('investigation_payment');
                $table->integer('created_by');
                // created_at required
                $table->dateTime('created_at');
                $table->integer('is_posted')->default(0);
                $table->dateTime('posted_on')->nullable();
                $table->bigInteger('updated_by')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->tinyInteger('is_sync')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_investigations_payments');
    }
};
