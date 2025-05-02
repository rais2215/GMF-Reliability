<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        if(!schema::hasTable('tbl_alertlevel')) {
            Schema::create('tbl_alertlevel', function (Blueprint $table) {
                $table->integer('id')->nullable();
                $table->string('actype', 50)->nullable();
                $table->string('ata', 10)->nullable();
                $table->string('type', 10)->nullable();
                $table->string('startmonth', 7)->nullable();
                $table->string('endmonth', 7)->nullable();
                $table->decimal('alertlevel', 12,10)->nullable();
                $table->text('NOTE')->nullable();
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
