<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name', 80);
            $t->string('phone', 15)->unique();
            $t->string('email', 120)->nullable()->unique();
            $t->string('password');
            $t->string('legacy_pw')->nullable();          // old Python scrypt hash "salt:hash", upgraded on first login
            $t->string('role', 10)->default('buyer');      // buyer | seller | doctor | admin
            $t->string('city', 5)->default('lhr');
            $t->string('plan', 10)->default('basic');      // basic | silver | gold
            $t->string('status', 10)->default('active');   // active | blocked
            $t->timestamp('last_login_at')->nullable();
            $t->unsignedInteger('login_count')->default(0);
            $t->timestamp('pw_changed_at')->nullable();
            $t->rememberToken();
            $t->timestamps();
        });
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('key', 20)->unique();   // cow buffalo goat sheep
            $t->string('name_ur', 40);
            $t->string('name_en', 40);
            $t->string('icon', 8)->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('categories'); Schema::dropIfExists('users'); }
};
