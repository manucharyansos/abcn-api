<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_requests', function (Blueprint $table) {
            $table->string('request_type')->default('general')->index()->after('locale');
            $table->foreignId('product_id')->nullable()->after('request_type')->constrained()->nullOnDelete();
            $table->string('product_name')->nullable()->after('product_id');
            $table->string('product_sku')->nullable()->after('product_name');
            $table->unsignedInteger('quantity')->nullable()->after('product_sku');
        });
    }

    public function down(): void
    {
        Schema::table('contact_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn(['request_type', 'product_name', 'product_sku', 'quantity']);
        });
    }
};
