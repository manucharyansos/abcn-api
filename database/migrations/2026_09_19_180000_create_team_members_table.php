<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('status')->default('draft')->index();
            $table->boolean('show_on_homepage')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('translations');
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
