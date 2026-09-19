<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('show_on_homepage')->default(false)->after('featured')->index();
        });

        DB::table('products')
            ->where('featured', true)
            ->update(['show_on_homepage' => true]);

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('status')->default('draft')->index();
            $table->boolean('show_on_homepage')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('translations');
            $table->json('images')->nullable();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('status')->default('draft')->index();
            $table->boolean('show_on_homepage')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->date('completed_at')->nullable()->index();
            $table->json('translations');
            $table->json('images')->nullable();
            $table->timestamps();
        });

        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('status')->default('draft')->index();
            $table->boolean('show_on_homepage')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable()->index();
            $table->json('translations');
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_articles');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('services');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('show_on_homepage');
        });
    }
};
