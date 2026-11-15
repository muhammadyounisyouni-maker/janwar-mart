<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/** Creates the first admin from .env. Never overwrites an existing account. */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $c = config('janwar.admin');
        if (!$c['phone'] || !$c['password']) { $this->command?->warn('ADMIN_PHONE / ADMIN_PASSWORD not set in .env: no admin created.'); return; }
        $p = $c['password'];
        if (strlen($p) < 12 || !preg_match('/[A-Z]/', $p) || !preg_match('/[a-z]/', $p) || !preg_match('/\d/', $p) || !preg_match('/[^A-Za-z0-9]/', $p)) {
            $this->command?->error('ADMIN_PASSWORD is too weak (need 12+ characters with upper, lower, digit and symbol). Admin not created.'); return;
        }
        if (User::where('phone', $c['phone'])->exists()) { $this->command?->info('Admin phone already exists: left unchanged.'); return; }
        User::create(['name' => $c['name'], 'phone' => $c['phone'], 'email' => $c['email'] ?: null, 'password' => $p, 'role' => 'admin']);
        $this->command?->info('Admin created.');
    }
}
