<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('patient_investigations')) {
            Schema::create('patient_investigations', function (Blueprint $table) {
                // Table engine / charset
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';

                $table->bigIncrements('id');
                $table->string('invoice_no', 100)->nullable();
                $table->integer('patient_id')->nullable();
                $table->bigInteger('appointment_id')->nullable();
                $table->integer('admission_id')->nullable();
                $table->integer('investigation_sub_category_id')->nullable();
                $table->integer('consultant_id')->default(0);
                $table->integer('consultant_share_percentage')->default(0);
                $table->integer('consultant_share_amount')->default(0);
                $table->decimal('inv_amount', 10, 2)->nullable();
                $table->decimal('sale_price', 10, 2)->default('0.00');
                $table->integer('frequency')->default(1);
                $table->decimal('discount_percentage', 10, 2)->default('0.00');
                $table->decimal('discount_amount', 10, 2)->default('0.00');
                $table->dateTime('inv_date')->nullable();
                $table->dateTime('inv_out_date')->nullable();
                $table->text('inv_comment')->nullable();
                $table->integer('created_by')->nullable();
                // created_at with current timestamp to match SQL
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->bigInteger('updated_by')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->string('patient_type', 100)->default('sehat_card');
                $table->integer('is_posted')->default(0);
                $table->dateTime('posted_on')->nullable();
                $table->integer('status')->default(0);
                $table->integer('is_sync')->default(0);

                // Indexes from SQL dump
                $table->index('admission_id');
                $table->index('patient_id');
                $table->index('investigation_sub_category_id', 'investigation_id');
                $table->index('consultant_id');
                $table->index('invoice_no');
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
        Schema::dropIfExists('patient_investigations');
    }
};
