<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB, Storage};

/**
 * Copies the data of the ORIGINAL Python/SQLite Janwar Mart into this Laravel/MySQL database.
 *  - the SQLite file is opened READ-ONLY and is never modified or deleted
 *  - numeric ids are kept, so public references (JM-1101, RQ-2001 ...) stay the same
 *  - existing rows in MySQL are never overwritten (insertOrIgnore)
 *  - old scrypt passwords are stored in users.legacy_pw and upgraded to bcrypt on the user's first login
 * usage: php artisan janwar:import-legacy /path/to/janwar.db --uploads=/path/to/data
 *        (--uploads = the Python "data" folder that contains uploads/ and private/)
 */
class ImportLegacy extends Command
{
    protected $signature = 'janwar:import-legacy {sqlite : path to the old SQLite database} {--uploads= : path to the old data folder (uploads/ and private/)}';
    protected $description = 'Import users, ads, orders, doctors and files from the original Python version (read-only, non-destructive)';

    private \PDO $src;
    private ?string $up = null;

    public function handle(): int
    {
        $file = $this->argument('sqlite');
        if (!is_file($file)) { $this->error("File not found: $file"); return self::FAILURE; }
        $this->src = new \PDO('sqlite:file:'.realpath($file).'?mode=ro', null, null, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]);
        $this->up = $this->option('uploads') ? rtrim($this->option('uploads'), '/\\') : null;
        $this->call('db:seed', ['--class' => 'Database\\Seeders\\CategorySeeder', '--force' => true]);
        $cats = DB::table('categories')->pluck('id', 'key');
        $counts = [];

        DB::transaction(function () use ($cats, &$counts) {
            $counts['users'] = $this->copy('users', fn ($r) => [
                'id' => $r['id'], 'name' => $r['name'], 'phone' => $r['phone'], 'email' => $r['email'] ?: null,
                'password' => bcrypt(bin2hex(random_bytes(16))), 'legacy_pw' => $r['pw'], 'role' => $r['role'] ?: 'buyer', 'city' => $r['city'] ?: 'lhr',
                'plan' => $r['plan'] ?: 'basic', 'status' => $r['status'] ?: 'active', 'last_login_at' => $this->dt($r['last_login'] ?? null),
                'login_count' => (int) ($r['login_count'] ?? 0), 'pw_changed_at' => !empty($r['pw_changed']) ? date('Y-m-d H:i:s', (int) $r['pw_changed']) : null,
                'created_at' => $this->dt($r['created_at']), 'updated_at' => $this->dt($r['created_at']),
            ]);
            $counts['listings'] = $this->copy('listings', function ($r) use ($cats) {
                $imgs = json_decode($r['images'] ?: '[]', true) ?: [];
                $row = [
                    'id' => $r['id'], 'title' => $r['title'], 'category_id' => $cats[$r['cat']] ?? $cats['cow'], 'price' => (int) $r['price'], 'city' => $r['city'] ?: 'lhr',
                    'age' => $r['age'], 'breed' => $r['breed'], 'weight' => $r['weight'], 'descr' => $r['descr'], 'status' => $r['status'] ?: 'pending', 'verified' => (int) $r['verified'],
                    'owner_id' => $r['owner_id'], 'contact' => $r['contact'] ?: '', 'approved_at' => $this->dt($r['approved_at']), 'sold_at' => $this->dt($r['sold_at']),
                    'buyer_name' => $r['buyer_name'], 'buyer_phone' => $r['buyer_phone'], 'sold_price' => $r['sold_price'], 'created_at' => $this->dt($r['created_at']), 'updated_at' => $this->dt($r['created_at']),
                ];
                $this->pendingImages[$r['id']] = $imgs;
                return $row;
            });
            foreach ($this->pendingImages as $lid => $imgs) {
                if (DB::table('listing_images')->where('listing_id', $lid)->exists()) continue;
                foreach ($imgs as $i => $url) {
                    $path = $this->moveFile($url, 'uploads', 'public', 'listings');
                    if ($path) DB::table('listing_images')->insert(['listing_id' => $lid, 'path' => $path, 'position' => $i]);
                }
            }
            $counts['orders'] = $this->copy('orders', function ($r) {
                foreach (json_decode($r['items'] ?: '[]', true) ?: [] as $lid) {
                    $price = DB::table('listings')->where('id', $lid)->value('price') ?? 0;
                    if (DB::table('listings')->where('id', $lid)->exists()) DB::table('order_items')->insertOrIgnore(['order_id' => $r['id'], 'listing_id' => $lid, 'price' => $price]);
                }
                return [
                    'id' => $r['id'], 'buyer_id' => $r['buyer_id'], 'name' => $r['name'], 'phone' => $r['phone'], 'city' => $r['city'], 'note' => $r['note'],
                    'admin_present' => (int) $r['admin_present'], 'total' => (int) $r['total'], 'status' => $r['status'] ?: 'requested', 'pay_method' => $r['pay_method'] ?: 'bank',
                    'pay_tid' => $r['pay_tid'], 'pay_status' => $r['pay_status'] ?: 'unpaid', 'completed_at' => $this->dt($r['completed_at']), 'created_at' => $this->dt($r['created_at']), 'updated_at' => $this->dt($r['created_at']),
                ];
            });
            $counts['favorites'] = $this->copy('favorites', fn ($r) => ['user_id' => $r['user_id'], 'listing_id' => $r['listing_id'], 'created_at' => $this->dt($r['ts']), 'updated_at' => $this->dt($r['ts'])], ['user_id', 'listing_id']);
            $counts['doctors'] = $this->copy('doctors', function ($r) {
                $cert = json_decode($r['cert'] ?: '{}', true) ?: []; $lic = json_decode($r['lic'] ?: '{}', true) ?: [];
                foreach (json_decode($r['hist'] ?: '[]', true) ?: [] as $h) {
                    DB::table('doctor_status_histories')->insert(['doctor_id' => $r['id'], 'status' => $h['s'] ?? 'pending', 'note' => $h['n'] ?? null, 'created_at' => $this->dt($h['d'] ?? null)]);
                }
                return [
                    'id' => $r['id'], 'user_id' => $r['user_id'], 'name' => $r['name'], 'phone' => $r['phone'], 'wa' => $r['wa'], 'email' => $r['email'], 'qual' => $r['qual'] ?: '', 'uni' => $r['uni'] ?: '',
                    'reg' => $r['reg'] ?: '', 'exp' => (int) $r['exp'], 'clinic' => $r['clinic'] ?: '', 'city' => $r['city'] ?: 'lhr', 'addr' => $r['addr'],
                    'photo' => $this->moveFile($r['photo'] ?? null, 'uploads', 'public', 'doctors'),
                    'cert_name' => $cert['name'] ?? null, 'cert_path' => $this->moveFile($cert['file'] ?? null, 'private', 'private', 'docs'),
                    'lic_name' => $lic['name'] ?? null, 'lic_path' => $this->moveFile($lic['file'] ?? null, 'private', 'private', 'docs'),
                    'consent' => (int) $r['consent'], 'status' => $r['status'] ?: 'pending', 'note' => $r['note'] ?: null, 'created_at' => $this->dt($r['created_at']), 'updated_at' => $this->dt($r['created_at']),
                ];
            });
            $counts['consultations'] = $this->copy('consultations', function ($r) {
                $ph = json_decode($r['photos'] ?: '[]', true) ?: [];
                return [
                    'id' => $r['id'], 'doctor_id' => $r['doctor_id'], 'user_id' => $r['user_id'], 'animal' => $r['animal'], 'text' => $r['text'] ?: '',
                    'photo1' => $this->moveFile($ph[0] ?? null, 'uploads', 'public', 'consults'), 'photo2' => $this->moveFile($ph[1] ?? null, 'uploads', 'public', 'consults'),
                    'name' => $r['name'] ?: '', 'phone' => $r['phone'] ?: '', 'status' => $r['status'] ?: 'new', 'reply' => $r['reply'] ?: null,
                    'replied_at' => $this->dt($r['replied_at']), 'created_at' => $this->dt($r['created_at']), 'updated_at' => $this->dt($r['created_at']),
                ];
            });
            $counts['login_events'] = $this->copy('login_events', fn ($r) => ['id' => $r['id'], 'user_id' => $r['user_id'], 'who' => $r['who'] ?: '', 'event' => $r['event'] ?: 'login', 'ip' => $r['ip'], 'ua' => $r['ua'], 'created_at' => $this->dt($r['ts'])]);
            $counts['notifications'] = $this->copy('notifs', fn ($r) => ['id' => $r['id'], 'icon' => $r['icon'] ?: '🔔', 'text' => $r['text'] ?: '', 'is_read' => (int) $r['read'], 'created_at' => $this->dt($r['ts'])], null, 'admin_notifications');
            // payment accounts: copy the old admin-entered account details (the old admin password hash is deliberately NOT imported)
            $pa = $this->src->query("SELECT v FROM settings WHERE k='pay_accounts'")->fetchColumn();
            if ($pa && !DB::table('settings')->where('key', 'pay_accounts')->exists()) DB::table('settings')->insert(['key' => 'pay_accounts', 'value' => $pa]);
        });

        foreach ($counts as $k => $n) $this->line(str_pad($k, 15).$n.' imported');
        $this->info('Done. Nothing was deleted or overwritten. Old users sign in with their old password (it is upgraded on first login).');
        $this->warn('Create the new admin account with: php artisan db:seed (reads ADMIN_* from .env).');
        return self::SUCCESS;
    }

    private array $pendingImages = [];

    private function copy(string $table, \Closure $map, ?array $keys = null, ?string $to = null): int
    {
        $n = 0;
        foreach ($this->src->query("SELECT * FROM $table") as $r) {
            $row = $map($r);
            if (DB::table($to ?? $table)->insertOrIgnore($row)) $n++;
        }
        return $n;
    }
    private function dt($v): ?string
    {
        if (!$v) return null;
        $t = strtotime((string) $v);
        return $t ? date('Y-m-d H:i:s', $t) : null;
    }
    /** old "/uploads/name.jpg" or "name.pdf" -> copied into storage; returns the new relative path or null */
    private function moveFile(?string $name, string $oldDir, string $disk, string $dir): ?string
    {
        if (!$name || !$this->up) return null;
        $base = basename($name);
        $from = $this->up.DIRECTORY_SEPARATOR.$oldDir.DIRECTORY_SEPARATOR.$base;
        if (!is_file($from)) return null;
        $to = $dir.'/'.$base;
        Storage::disk($disk)->put($to, file_get_contents($from));   // copy, the original stays where it is
        return $to;
    }
}
