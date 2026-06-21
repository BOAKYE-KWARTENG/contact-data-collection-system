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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->enum('age_range', [
                '0-17',
                '18-24',
                '25-34',
                '35-44',
                '45-54',
                '55-64',
                '65+'
            ]);
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed']);
            $table->string('mobile_number');
            $table->foreignId('telco_id')->constrained('telcos')->cascadeOnDelete();
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
