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
    Schema::create('rfq_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('rfq_id')->constrained()->cascadeOnDelete();
        $table->string('item_description');
        $table->string('unit');
        $table->integer('quantity');
        $table->decimal('unit_price', 12, 2)->nullable();
        $table->decimal('total_price', 15, 2)->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfq_items');
    }
};
