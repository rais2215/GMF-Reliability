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
        if (!Schema::hasTable('tbl_master_ata')) {
            Schema::create('tbl_master_ata', function (Blueprint $table) {
                $table->integer('ATA')->primary();
                $table->string('ATA_DESC', 100)->nullable();
            });
        };
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
