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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            
            // Link to the user who performed the action. 
            // If the user is deleted, their logs remain but point to NULL (System).
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
                
            $table->string('action'); // e.g., 'created', 'updated', 'deleted', 'completed'
            
            // SAFEST APPROACH: This creates subject_type & subject_id AND 
            // builds the compound index safely behind the scenes without PostgreSQL name clashing.
            $table->nullableMorphs('subject'); 
            
            $table->text('description'); // The human-readable log entry text
            
            // Audit logs are immutable records of history; they only need a creation timestamp.
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
