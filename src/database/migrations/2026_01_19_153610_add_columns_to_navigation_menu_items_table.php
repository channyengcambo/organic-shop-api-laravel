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
        Schema::table('navigation_menu_items', function (Blueprint $table) {
            Schema::table('navigation_menu_items', function (Blueprint $table) {
                $table->string('description')->nullable()->after('icon');
                $table->string('image')->nullable()->after('description');
                $table->string('image_action')->nullable()->after('image');
                $table->string('total_items')->nullable()->after('image_action');
                $table->string('target')->default('_self')->after('route');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('navigation_menu_items', function (Blueprint $table) {
            Schema::table('navigation_menu_items', function (Blueprint $table) {
                $table->dropColumn(['description', 'target', 'image', 'image_action', 'total_items']);
            });
        });
    }
};
