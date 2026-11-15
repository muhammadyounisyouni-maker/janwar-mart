<?php
namespace Tests\Feature;

use App\Models\{Category, Doctor, Listing, Order, Setting, User};
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{DB, Storage};
use Tests\TestCase;

class JanwarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        Setting::put('pay_accounts', ['jazz' => ['title' => 'JM', 'no' => '03000000000'], 'easy' => ['title' => '', 'no' => ''], 'bank' => ['bank' => '', 'title' => '', 'iban' => ''], 'cod' => true]);
    }

    private function user(string $role = 'buyer', string $phone = '03001111111', string $plan = 'basic'): User
    {
        return User::create(['name' => 'T '.$role, 'phone' => $phone, 'password' => 'secret1', 'role' => $role, 'plan' => $plan]);
    }
    private function ad(User $owner, string $status = 'active', int $price = 100000): Listing
    {
        return Listing::create(['category_id' => Category::first()->id, 'price' => $price, 'city' => 'lhr', 'status' => $status, 'verified' => true, 'owner_id' => $owner->id, 'contact' => $owner->phone, 'approved_at' => now()]);
    }

    public function test_public_pages_open(): void
    {
        foreach (['/', '/animals', '/doctors', '/help', '/plans', '/contact', '/login', '/register', '/cart', '/password/reset'] as $u) $this->get($u)->assertOk();
        $this->get('/manifest.webmanifest')->assertOk();
    }

    public function test_only_active_ads_are_public(): void
    {
        $s = $this->user('seller', '03002222222');
        $a = $this->ad($s); $p = $this->ad($s, 'pending');
        $this->get('/animals')->assertSee($a->ref)->assertDontSee($p->ref);
        $this->get('/animals/'.$p->ref)->assertNotFound();
        $this->getJson('/api/v1/listings')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_signup_needs_a_valid_otp_then_login_works(): void
    {
        $this->postJson('/otp/send', ['phone' => '03005550001', 'purpose' => 'signup'])->assertOk();
        $row = DB::table('otps')->first();
        $this->post('/register', ['name' => 'Ali', 'phone' => '03005550001', 'password' => 'secret1', 'role' => 'buyer', 'otp' => '000000'])->assertSessionHasErrors('otp');
        DB::table('otps')->update(['code_hash' => hash_hmac('sha256', '03005550001|123456', config('app.key'))]);
        $this->post('/register', ['name' => 'Ali', 'phone' => '03005550001', 'password' => 'secret1', 'role' => 'buyer', 'otp' => '123456'])->assertRedirect('/');
        $this->assertDatabaseHas('users', ['phone' => '03005550001']);
        $this->assertDatabaseHas('login_events', ['event' => 'signup']);
        $this->post('/logout');
        $this->post('/login', ['login' => '03005550001', 'password' => 'wrong'])->assertSessionHasErrors('login');
        $this->post('/login', ['login' => '0300 5550001', 'password' => 'secret1'])->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_old_python_password_works_and_is_upgraded(): void
    {
        $u = User::create(['name' => 'Old', 'phone' => '03009990000', 'password' => 'x-unused', 'legacy_pw' => trim(file_get_contents(__DIR__.'/legacy_hash.txt'))]);
        $this->post('/login', ['login' => '03009990000', 'password' => 'bad'])->assertSessionHasErrors('login');
        $this->post('/login', ['login' => '03009990000', 'password' => 'demo123'])->assertRedirect('/');
        $this->assertNull($u->fresh()->legacy_pw);
        $this->post('/logout');
        $this->post('/login', ['login' => '03009990000', 'password' => 'demo123'])->assertRedirect('/');
    }

    public function test_blocked_user_cannot_log_in(): void
    {
        $u = $this->user(); $u->update(['status' => 'blocked']);
        $this->post('/login', ['login' => $u->phone, 'password' => 'secret1'])->assertSessionHasErrors('login');
    }

    public function test_sell_creates_pending_ad_and_respects_plan(): void
    {
        Storage::fake('public');
        $s = $this->user('seller', '03002222222');
        $data = fn () => ['category' => 'cow', 'price' => 150000, 'city' => 'lhr', 'contact' => '03002222222', 'consent' => 1, 'photos' => [UploadedFile::fake()->image('a.jpg')]];
        for ($i = 0; $i < 3; $i++) $this->actingAs($s)->post('/sell', $data())->assertRedirect('/my-ads');
        $this->assertSame(3, Listing::where('status', 'pending')->count());
        $this->actingAs($s)->post('/sell', $data())->assertSessionHasErrors('plan');
        $this->actingAs($s)->post('/sell', ['category' => 'cow', 'price' => 1, 'city' => 'lhr', 'contact' => '03002222222', 'consent' => 1])->assertSessionHasErrors('photos');
    }

    public function test_full_cod_order_flow(): void
    {
        $seller = $this->user('seller', '03002222222'); $buyer = $this->user('buyer', '03003333333');
        $admin = $this->user('admin', '03000000000');
        $ad = $this->ad($seller);
        $this->actingAs($buyer)->post('/cart/'.$ad->ref)->assertSessionHas('ok');
        $this->actingAs($buyer)->post('/checkout', ['name' => 'B', 'phone' => '03003333333', 'pay_method' => 'cod'])->assertRedirect('/orders');
        $o = Order::first();
        $this->assertSame('cod', $o->pay_status); $this->assertSame('reserved', $ad->fresh()->status);
        $this->actingAs($admin)->post("/admin/orders/{$o->id}/complete")->assertSessionHasErrors('order');   // payment not confirmed yet
        $this->actingAs($admin)->post("/admin/orders/{$o->id}/paid");
        $this->actingAs($admin)->post("/admin/orders/{$o->id}/complete");
        $this->assertSame('sold', $ad->fresh()->status); $this->assertSame('completed', $o->fresh()->status);
        $this->actingAs($buyer)->post("/orders/{$o->id}/dispute", ['reason' => 'too short'])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('disputes', 1);
    }

    public function test_cannot_buy_own_animal_or_use_unavailable_method(): void
    {
        $seller = $this->user('seller', '03002222222'); $ad = $this->ad($seller);
        $this->actingAs($seller)->post('/cart/'.$ad->ref)->assertSessionHasErrors('cart');
        $buyer = $this->user('buyer', '03003333333');
        $this->actingAs($buyer)->post('/cart/'.$ad->ref);
        $this->actingAs($buyer)->post('/checkout', ['name' => 'B', 'phone' => '03003333333', 'pay_method' => 'easy'])->assertSessionHasErrors('order');
    }

    public function test_cancel_returns_animal_to_sale(): void
    {
        $seller = $this->user('seller', '03002222222'); $buyer = $this->user('buyer', '03003333333'); $admin = $this->user('admin', '03000000000');
        $ad = $this->ad($seller);
        $this->actingAs($buyer)->post('/cart/'.$ad->ref);
        $this->actingAs($buyer)->post('/checkout', ['name' => 'B', 'phone' => '03003333333', 'pay_method' => 'jazz', 'pay_tid' => 'TX1']);
        $o = Order::first(); $this->assertSame('submitted', $o->pay_status);
        $this->actingAs($admin)->post("/admin/orders/{$o->id}/cancel");
        $this->assertSame('active', $ad->fresh()->status); $this->assertSame('refund', $o->fresh()->pay_status);
    }

    public function test_admin_area_is_admins_only(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $u = $this->user();
        foreach (['/admin', '/admin/tab/users', '/admin/tab/ads'] as $url) $this->actingAs($u)->get($url)->assertForbidden();
        $this->actingAs($u)->post('/admin/payments')->assertForbidden();
        $admin = $this->user('admin', '03000000000');
        foreach (array_keys(\App\Http\Controllers\Admin\DashboardController::TABS) as $t) $this->actingAs($admin)->get($t === 'overview' ? '/admin' : "/admin/tab/$t")->assertOk();
    }

    public function test_admin_approves_ad_which_then_appears_publicly(): void
    {
        $s = $this->user('seller', '03002222222'); $admin = $this->user('admin', '03000000000');
        $ad = $this->ad($s, 'pending');
        $this->actingAs($admin)->post("/admin/listings/{$ad->id}/approve");
        $this->assertSame('active', $ad->fresh()->status);
        $this->get('/')->assertSee($ad->ref);
    }

    public function test_doctor_flow_and_private_documents(): void
    {
        Storage::fake('public'); Storage::fake('private');
        $d = $this->user('doctor', '03004444444'); $admin = $this->user('admin', '03000000000'); $owner = $this->user('buyer', '03005555555');
        $payload = ['name' => 'Dr X', 'phone' => '03004444444', 'qual' => 'DVM', 'uni' => 'UVAS', 'reg' => 'R-1', 'exp' => 5, 'clinic' => 'C', 'city' => 'lhr', 'consent' => 1,
            'cert' => UploadedFile::fake()->create('c.pdf', 100, 'application/pdf'), 'lic' => UploadedFile::fake()->create('l.pdf', 100, 'application/pdf')];
        $this->actingAs($d)->post('/doctor/register', $payload)->assertRedirect('/doctor/dashboard');
        $doc = Doctor::first(); $this->assertSame('pending', $doc->status);
        $this->get('/doctors')->assertDontSee('Dr X');
        $this->actingAs($d)->post("/admin/doctors/{$doc->id}/approve")->assertForbidden();          // a doctor can never approve themselves
        $this->actingAs($admin)->post("/admin/doctors/{$doc->id}/reject", [])->assertSessionHasErrors('note');   // reason required
        $this->actingAs($admin)->post("/admin/doctors/{$doc->id}/approve");
        $this->get('/doctors')->assertSee('Dr X');
        $this->getJson('/api/v1/doctors')->assertOk()->assertJsonMissing(['reg' => 'R-1']);
        $this->actingAs($owner)->get("/admin/doctors/{$doc->id}/file/cert")->assertForbidden();
        $this->actingAs($admin)->get("/admin/doctors/{$doc->id}/file/cert")->assertOk();
        $this->actingAs($d)->post("/doctors/{$doc->id}/consult", ['text' => 'my cow is sick for days', 'name' => 'D', 'phone' => '03004444444'])->assertSessionHasErrors('text'); // not yourself
        $this->actingAs($owner)->post("/doctors/{$doc->id}/consult", ['text' => 'my cow is sick for days', 'name' => 'O', 'phone' => '03005555555'])->assertRedirect('/consultations');
        $this->actingAs($owner)->post('/doctor/consultations/1/reply', ['reply' => 'hello there'])->assertForbidden();
        $this->actingAs($d)->post('/doctor/consultations/1/reply', ['reply' => 'give water and see a vet'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('consultations', ['status' => 'answered']);
    }

    public function test_contact_report_favorite_and_xss_escaping(): void
    {
        $s = $this->user('seller', '03002222222'); $ad = $this->ad($s); $ad->update(['title' => '<script>alert(1)</script>']);
        $this->get('/animals')->assertDontSee('<script>alert(1)</script>', false);
        $this->post('/contact', ['type' => 'other', 'message' => 'hello', 'phone' => '03001234567'])->assertSessionHas('ok');
        $this->post("/animals/{$ad->ref}/report", ['reason' => 'Fake or fraud'])->assertSessionHas('ok');
        $this->assertDatabaseCount('contact_messages', 1); $this->assertDatabaseCount('ad_reports', 1);
        $b = $this->user('buyer', '03003333333');
        $this->actingAs($b)->post("/favorites/{$ad->ref}"); $this->assertDatabaseCount('favorites', 1);
        $this->actingAs($b)->post("/favorites/{$ad->ref}"); $this->assertDatabaseCount('favorites', 0);
    }

    public function test_forms_carry_a_csrf_token(): void
    {
        foreach (['/login', '/register', '/contact'] as $u) $this->get($u)->assertSee('name="_token"', false);
    }

    public function test_language_switch(): void
    {
        $this->get('/lang/en')->assertRedirect(); $this->get('/')->assertSee('Buy or sell your animal');
        $this->get('/lang/ur'); $this->get('/')->assertSee('جانور مارٹ');
    }
}
