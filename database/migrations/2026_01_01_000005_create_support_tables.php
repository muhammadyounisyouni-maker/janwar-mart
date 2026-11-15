<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('otps', function (Blueprint $t) {
            $t->string('phone', 15);
            $t->string('purpose', 10);              // signup | reset
            $t->string('code_hash');
            $t->timestamp('expires_at');
            $t->unsignedTinyInteger('attempts')->default(0);
            $t->timestamp('sent_at');
            $t->primary(['phone', 'purpose']);
        });
        Schema::create('login_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('who', 120);
            $t->string('event', 20);   // signup login login_failed password_reset admin_login admin_login_failed
            $t->string('ip', 45)->nullable();
            $t->string('ua', 200)->nullable();
            $t->timestamp('created_at')->nullable();
            $t->index('created_at');
        });
        Schema::create('admin_notifications', function (Blueprint $t) {
            $t->id();
            $t->string('icon', 8)->default('🔔');
            $t->string('text', 300);
            $t->boolean('is_read')->default(false);
            $t->timestamp('created_at')->nullable();
        });
        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('action', 300);
            $t->timestamp('created_at')->nullable();
        });
        Schema::create('settings', function (Blueprint $t) {
            $t->string('key', 40)->primary();
            $t->json('value')->nullable();
        });
        Schema::create('contact_messages', function (Blueprint $t) {
            $t->id();
            $t->string('type', 40);
            $t->string('ad_ref', 20)->nullable();
            $t->text('message');
            $t->string('name', 80)->nullable();
            $t->string('phone', 15);
            $t->string('status', 10)->default('open');
            $t->timestamps();
        });
    }
    public function down(): void { foreach (['contact_messages', 'settings', 'audit_logs', 'admin_notifications', 'login_events', 'otps'] as $x) Schema::dropIfExists($x); }
};
