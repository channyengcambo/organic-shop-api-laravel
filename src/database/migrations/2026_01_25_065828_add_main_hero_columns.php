<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('main_heroes', function (Blueprint $table) {
            $table->string('title');
            $table->boolean('has_discount');
            $table->string('discount_label')->nullable();
            $table->string('discount_content')->nullable();
            $table->double('discount_value')->nullable();
            $table->string('discount_subtitle')->nullable();
            $table->string('order_index')->nullable();
            $table->boolean('is_active')->default(1);
            $table->string('description');
            $table->string('target')->default('_self');
            $table->string('image')->nullable();
            $table->string('button_action')->nullable();
            $table->integer('total_items')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('main_heroes', function (Blueprint $table) {
            Schema::table('main_heroes', function (Blueprint $table) {
                $table->dropColumn([
                    'title',
                    'has_discount',
                    'discount_label',
                    'discount_content',
                    'discount_value',
                    'discount_subtitle',
                    'order_index',
                    'is_active',
                    'description',
                    'target',
                    'image',
                    'button_action',
                    'total_items'
                ]);
            });
        });
    }
};
