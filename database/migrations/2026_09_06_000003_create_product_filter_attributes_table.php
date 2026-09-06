<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_filter_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('key', 80);
            $table->string('option', 120);
            $table->json('label');
            $table->json('value');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'key']);
            $table->index(['key', 'option']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_filter_attributes');
    }
};
