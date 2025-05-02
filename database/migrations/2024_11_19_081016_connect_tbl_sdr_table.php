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
        if (!Schema::hasTable('tbl_sdr')) {
            Schema::create('tbl_sdr', function (Blueprint $table) {
                $table->integer('ID')->primary()->autoIncrement();
                $table->string('ACTYPE', 50)->nullable();
                $table->string('Reg', 10)->nullable();
                $table->date('DataOccur')->nullable();
                $table->string('FlightNo', 10)->nullable();
                $table->integer('ATA', 10)->nullable();
                $table->string('Remark', 50)->nullable();
                $table->string('Problem', 50)->nullable();
                $table->string('Rectification', 50)->nullable();

                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
