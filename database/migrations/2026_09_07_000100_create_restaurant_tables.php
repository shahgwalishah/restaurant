<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('cashier')->after('email');
            $table->string('phone')->nullable()->after('role');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('color')->default('#e85d3f'); $table->boolean('active')->default(true); $table->timestamps();
        });
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('category_id')->constrained()->cascadeOnDelete(); $table->string('name'); $table->text('description')->nullable(); $table->decimal('price', 12, 2); $table->decimal('cost', 12, 2)->default(0); $table->boolean('available')->default(true); $table->boolean('featured')->default(false); $table->timestamps();
        });
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('area')->default('Main Hall'); $table->unsignedTinyInteger('capacity')->default(4); $table->string('status')->default('available'); $table->timestamps();
        });
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('phone')->unique(); $table->string('address')->nullable(); $table->unsignedInteger('loyalty_points')->default(0); $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); $table->string('order_no')->unique(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete(); $table->foreignId('restaurant_table_id')->nullable()->constrained()->nullOnDelete(); $table->string('type')->default('dine_in'); $table->string('status')->default('pending'); $table->string('payment_status')->default('unpaid'); $table->string('payment_method')->nullable(); $table->decimal('subtotal', 12, 2); $table->decimal('discount', 12, 2)->default(0); $table->decimal('tax', 12, 2)->default(0); $table->decimal('total', 12, 2); $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('order_id')->constrained()->cascadeOnDelete(); $table->foreignId('menu_item_id')->nullable()->constrained()->nullOnDelete(); $table->string('name'); $table->decimal('price', 12, 2); $table->unsignedInteger('quantity'); $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('unit'); $table->decimal('stock', 12, 2)->default(0); $table->decimal('minimum_stock', 12, 2)->default(0); $table->decimal('unit_cost', 12, 2)->default(0); $table->string('supplier')->nullable(); $table->timestamps();
        });
        Schema::create('expenses', function (Blueprint $table) {
            $table->id(); $table->string('title'); $table->string('category'); $table->decimal('amount', 12, 2); $table->date('expense_date'); $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('settings', function (Blueprint $table) { $table->id(); $table->string('key')->unique(); $table->text('value')->nullable(); $table->timestamps(); });
    }
    public function down(): void
    {
        Schema::dropIfExists('settings'); Schema::dropIfExists('expenses'); Schema::dropIfExists('inventory_items'); Schema::dropIfExists('order_items'); Schema::dropIfExists('orders'); Schema::dropIfExists('customers'); Schema::dropIfExists('restaurant_tables'); Schema::dropIfExists('menu_items'); Schema::dropIfExists('categories');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['role', 'phone']));
    }
};
