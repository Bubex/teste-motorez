<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->year('year');
            $table->string('version')->nullable();
            $table->string('color');
            $table->integer('mileage');
            $table->enum('fuel', ['Gasolina', 'Álcool', 'Flex', 'Diesel', 'Gás Natural', 'Híbrido', 'Elétrico', 'Não informado']);
            $table->enum('transmission', ['Manual', 'Automatizada', 'Automática', 'Semi-Automática', 'CVT', 'Não informado']);
            $table->integer('doors');
            $table->decimal('price', 10, 2);
            $table->timestamp('last_update')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
