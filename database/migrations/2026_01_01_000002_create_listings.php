<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $t) {
            $t->id();
            $t->string('title', 120)->nullable();
            $t->foreignId('category_id')->constrained('categories');
            $t->unsignedBigInteger('price');
            $t->string('city', 5);
            $t->string('age', 10)->nullable();
            $t->string('breed', 60)->nullable();
            $t->string('weight', 10)->nullable();
            $t->text('descr')->nullable();
            $t->string('status', 10)->default('pending')->index(); // pending active reserved sold rejected removed
            $t->boolean('verified')->default(false);
            $t->foreignId('owner_id')->constrained('users');
            $t->string('contact', 15);
            $t->timestamp('approved_at')->nullable();
            $t->timestamp('sold_at')->nullable();
            $t->string('buyer_name', 80)->nullable();
            $t->string('buyer_phone', 15)->nullable();
            $t->unsignedBigInteger('sold_price')->nullable();
            $t->timestamps();
        });
        Schema::create('listing_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $t->string('path');
            $t->unsignedTinyInteger('position')->default(0);
        });
        Schema::create('favorites', function (Blueprint $t) {
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
            $t->primary(['user_id', 'listing_id']);
        });
        Schema::create('ad_reports', function (Blueprint $t) {
            $t->id();
            $t->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('reason', 300)->nullable();
            $t->string('status', 10)->default('open');
            $t->timestamps();
        });
    }
    public function down(): void { foreach (['ad_reports', 'favorites', 'listing_images', 'listings'] as $x) Schema::dropIfExists($x); }
};
