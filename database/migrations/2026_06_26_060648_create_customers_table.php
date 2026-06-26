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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('rut');
            $table->string('razon_social');
            $table->string('giro')->nullable();
            $table->string('direccion')->nullable();
            $table->string('comuna')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'rut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
