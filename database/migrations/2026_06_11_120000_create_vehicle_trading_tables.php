<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('seller_name')->nullable();
            $table->decimal('price', 12, 2);
            $table->date('purchased_at');
            $table->foreignId('inspection_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('vehicle_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->decimal('price', 12, 2);
            $table->string('vat_mode')->default('margin');
            $table->string('contract_pdf_path')->nullable();
            $table->timestamp('sold_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_sales');
        Schema::dropIfExists('vehicle_purchases');
    }
};
