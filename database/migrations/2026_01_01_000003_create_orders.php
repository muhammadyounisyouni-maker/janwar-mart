<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('buyer_id')->constrained('users');
            $t->string('name', 80);
            $t->string('phone', 15);
            $t->string('city', 60)->nullable();
            $t->string('note', 300)->nullable();
            $t->boolean('admin_present')->default(false);
            $t->unsignedBigInteger('total')->default(0);
            $t->string('status', 12)->default('requested');   // requested contacted completed cancelled
            $t->string('pay_method', 6)->default('bank');      // jazz easy bank cod
            $t->string('pay_tid', 60)->nullable();
            $t->string('pay_status', 10)->default('unpaid');   // unpaid submitted cod confirmed rejected refund
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();
        });
        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('listing_id')->constrained();
            $t->unsignedBigInteger('price');
        });
        Schema::create('order_meetings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('meet_at', 60);
            $t->string('place', 120);
            $t->string('admin_name', 60);
            $t->timestamps();
        });
        Schema::create('disputes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained();
            $t->string('reason', 500);
            $t->string('status', 10)->default('open');         // open refund rejected
            $t->string('resolution', 300)->nullable();
            $t->timestamp('resolved_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { foreach (['disputes', 'order_meetings', 'order_items', 'orders'] as $x) Schema::dropIfExists($x); }
};
