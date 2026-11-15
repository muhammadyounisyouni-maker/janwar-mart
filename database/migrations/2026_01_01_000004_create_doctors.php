<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained();
            $t->string('name', 80);
            $t->string('phone', 15);
            $t->string('wa', 15)->nullable();
            $t->string('email', 120)->nullable();
            $t->string('qual', 80);
            $t->string('uni', 80);
            $t->string('reg', 40);
            $t->unsignedTinyInteger('exp')->default(0);
            $t->string('clinic', 100);
            $t->string('city', 5);
            $t->string('addr', 200)->nullable();
            $t->string('photo')->nullable();
            $t->string('cert_name')->nullable();
            $t->string('cert_path')->nullable();   // private disk
            $t->string('lic_name')->nullable();
            $t->string('lic_path')->nullable();    // private disk
            $t->boolean('consent')->default(false);
            $t->string('status', 10)->default('pending'); // pending review verified rejected suspended
            $t->string('note', 300)->nullable();
            $t->timestamps();
        });
        Schema::create('doctor_status_histories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $t->string('status', 10);
            $t->string('note', 300)->nullable();
            $t->timestamp('created_at')->nullable();
        });
        Schema::create('consultations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('doctor_id')->constrained();
            $t->foreignId('user_id')->constrained();
            $t->string('animal', 80)->nullable();
            $t->text('text');
            $t->string('photo1')->nullable();
            $t->string('photo2')->nullable();
            $t->string('name', 80);
            $t->string('phone', 15);
            $t->string('status', 10)->default('new');   // new answered
            $t->text('reply')->nullable();
            $t->timestamp('replied_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { foreach (['consultations', 'doctor_status_histories', 'doctors'] as $x) Schema::dropIfExists($x); }
};
