<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_menu_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('navigation_menu_items')
                ->cascadeOnDelete();

            $table->string('label', 100);
            $table->string('route', 255);
            $table->string('icon', 100)->nullable();

            $table->integer('order_index')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Performance indexes
            $table->index(['parent_id', 'order_index']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_menu_items');
    }
};
