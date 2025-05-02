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
        if (!Schema::hasTable('tbl_masterac')) {
            Schema::create('tbl_masterac', function (Blueprint $table) {
                $table->integer('IDreg')->primary();
                $table->integer('IDType')->nullable();
                $table->string('ACType', 50)->nullable();
                $table->string('ACReg', 50)->nullable();
                $table->string('Operator', 30)->nullable();
                $table->string('SerialModule', 50)->nullable();
                $table->string('VariableNumber', 50)->nullable();
                $table->integer('SerialNumber')->nullable();
                $table->dateTime('ManufYear')->nullable();
                $table->dateTime('DEliveryDate')->nullable();
                $table->string('EngineType', 50)->nullable();
                $table->string('Lessor', 50)->nullable();
                $table->integer('Active')->nullable();

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
        // Karena ini hanya untuk menghubungkan ke tabel yang sudah ada,
        // kita bisa mengosongkan method down() atau memberikan opsi untuk tidak menghapus tabel
        // Schema::dropIfExists('tbl_masterac');
    }
};