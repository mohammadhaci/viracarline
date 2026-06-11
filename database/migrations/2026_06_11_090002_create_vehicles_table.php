<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vin', 17)->nullable()->unique();
            $table->string('brand');
            $table->string('model');
            $table->string('variant')->nullable();
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('mileage_km');
            $table->string('fuel');
            $table->string('transmission');
            $table->string('color')->nullable();
            $table->decimal('purchase_price', 12, 2);
            $table->date('purchase_date');
            $table->string('purchase_source')->nullable();
            $table->decimal('asking_price', 12, 2)->nullable();
            $table->decimal('sold_price', 12, 2)->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->string('status')->default('purchased')->index();
            $table->foreignId('partner_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('show_price')->default(true);
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
