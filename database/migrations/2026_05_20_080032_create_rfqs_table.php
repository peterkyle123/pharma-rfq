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
    Schema::create('rfqs', function (Blueprint $table) {
        $table->id();
        $table->string('rfq_number')->unique();
        $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
        $table->date('date_received');
        $table->date('deadline');
        $table->decimal('abc', 15, 2)->nullable();
        $table->enum('status', [
            'Received',
            'Reviewing',
            'Quoted',
            'Awarded',
            'Lost',
        ])->default('Received');
        $table->text('notes')->nullable();
        $table->string('philgeps_ref')->nullable();
        $table->string('attachment_path')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfqs');
    }
};
