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
        Schema::create('opd_type', function (Blueprint $table) {
            // match SQL: id int NOT NULL AUTO_INCREMENT PRIMARY KEY
            $table->increments('id');

            // name varchar(100) NOT NULL
            $table->string('name', 100);

            // fees decimal(10,2) NOT NULL
            $table->decimal('fees', 10, 2);

            // is_sync tinyint(1) NOT NULL DEFAULT '0'
            $table->tinyInteger('is_sync')->default(0);

            // set the table engine/charset/collation to match SQL dump
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opd_type');
    }
};
