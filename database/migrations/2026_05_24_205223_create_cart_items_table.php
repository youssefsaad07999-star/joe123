<?php

use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('session_id', 500)->nullable()->index();
            $table->foreignIdFor(ProductVariant::class)->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);

            // Prevents duplicate rows for the same user and variant.
            // If a user adds the same variant again, you just increment quantity.
            $table->unique(['user_id', 'product_variant_id']);
            $table->unique(['session_id', 'product_variant_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
