<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{AdminNotification, AdReport, AuditLog, Category, ContactMessage, Consultation, Dispute, Doctor, Listing, LoginEvent, Order, OrderMeeting, Setting, User};
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage};

class DashboardController extends Controller
{
    public const TABS = [
        'overview' => 'Overview', 'users' => 'Users', 'logins' => 'Login activity', 'ads' => 'Ads', 'orders' => 'Purchase requests',
        'doctors' => 'Vet doctors', 'consultations' => 'Consultations', 'payments' => 'Payments & COD', 'sold' => 'Sold / Bought report',
        'messages' => 'Contact messages', 'reports' => 'Ad reports', 'disputes' => 'Disputes & meetings', 'notifications' => 'Notifications', 'audit' => 'Admin log',
    ];

    public function show(string $tab = 'overview')
    {
        abort_unless(isset(self::TABS[$tab]), 404);
        $data = ['tab' => $tab, 'tabs' => self::TABS, 'badges' => [
            'ads' => Listing::where('status', 'pending')->count(), 'doctors' => Doctor::where('status', 'pending')->count(),
            'notifications' => AdminNotification::where('is_read', false)->count(), 'messages' => ContactMessage::where('status', 'open')->count(),
            'reports' => AdReport::where('status', 'open')->count(), 'disputes' => Dispute::where('status', 'open')->count(),
        ]];
        return view('admin.'.$tab, $data + $this->{'data'.ucfirst($tab)}());
    }

    private function dataOverview(): array
    {
        $c = fn ($s) => Listing::where('status', $s)->count();
        $stats = [
            'users' => User::count(), 'buyers' => User::where('role', 'buyer')->count(), 'sellers' => User::where('role', 'seller')->count(), 'doctors' => User::where('role', 'doctor')->count(),
            'logins_today' => LoginEvent::whereIn('event', ['login', 'signup', 'admin_login'])->whereDate('created_at', today())->count(),
            'failed_today' => LoginEvent::where('event', 'like', '%failed')->whereDate('created_at', today())->count(),
            'pending' => $c('pending'), 'active' => $c('active'), 'reserved' => $c('reserved'), 'sold' => $c('sold'), 'listed' => Listing::where('status', '!=', 'removed')->count(),
            'open_orders' => Order::whereIn('status', ['requested', 'contacted'])->count(), 'revenue' => (int) Listing::where('status', 'sold')->sum('sold_price'),
            'doctors_pending' => Doctor::where('status', 'pending')->count(), 'doctors_verified' => Doctor::where('status', 'verified')->count(),
            'cod' => Order::where('pay_method', 'cod')->count(), 'favs' => DB::table('favorites')->count(),
        ];
        $byCat = Category::all()->map(fn ($k) => ['name' => $k->label(), 'listed' => $k->listings()->where('status', '!=', 'removed')->count(), 'sold' => $k->listings()->where('status', 'sold')->count()]);
        $days = collect(range(6, 0))->map(fn ($i) => today()->subDays($i));
        $perDay = fn ($q, $col) => $days->map(fn ($d) => (clone $q)->whereDate($col, $d)->count())->all();
        $chart = [
            'labels' => $days->map(fn ($d) => $d->format('D'))->all(),
            'logins' => $perDay(LoginEvent::whereIn('event', ['login', 'signup', 'admin_login']), 'created_at'),
            'orders' => $perDay(Order::query(), 'created_at'),
            'ads' => $perDay(Listing::query(), 'created_at'),
            'status' => [$stats['pending'], $stats['active'], $stats['reserved'], $stats['sold']],
            'cats' => ['labels' => $byCat->pluck('name')->all(), 'listed' => $byCat->pluck('listed')->all(), 'sold' => $byCat->pluck('sold')->all()],
        ];
        return [
            'stats' => $stats, 'byCat' => $byCat, 'chart' => $chart,
            'pendingAds' => Listing::where('status', 'pending')->with(['images', 'category'])->latest('id')->limit(5)->get(),
            'pendingDocs' => Doctor::whereIn('status', ['pending', 'review'])->latest('id')->limit(4)->get(),
            'openOrders' => Order::whereIn('status', ['requested', 'contacted'])->latest('id')->limit(5)->get(),
            'feed' => AdminNotification::latest('id')->limit(8)->get(),
            'recentLogins' => LoginEvent::with('user')->latest('id')->limit(6)->get(),
        ];
    }
    private function dataUsers(): array { return ['users' => User::latest('id')->get()]; }
    private function dataLogins(): array { return ['events' => LoginEvent::with('user')->latest('id')->limit(300)->get()]; }
    private function dataAds(): array { return ['ads' => Listing::where('status', '!=', 'removed')->with(['images', 'category', 'owner'])->latest('id')->get()]; }
    private function dataOrders(): array { return ['orders' => Order::with(['listings', 'buyer'])->latest('id')->get()]; }
    private function dataDoctors(): array { return ['docs' => Doctor::with('histories')->latest('id')->get()]; }
    private function dataConsultations(): array { return ['cons' => Consultation::with('doctor')->latest('id')->get()]; }
    private function dataPayments(): array { return ['acc' => Setting::payAccounts(), 'orders' => Order::latest('id')->limit(100)->get()]; }
    private function dataSold(): array { return ['sold' => Listing::where('status', 'sold')->with(['owner', 'category'])->latest('sold_at')->get(), 'listed' => Listing::where('status', '!=', 'removed')->count()]; }
    private function dataMessages(): array { return ['items' => ContactMessage::latest('id')->get()]; }
    private function dataReports(): array { return ['items' => AdReport::with(['listing', 'listing.category'])->latest('id')->get()]; }
    private function dataDisputes(): array { return ['orders' => Order::with(['dispute', 'meeting'])->where(fn ($q) => $q->has('dispute')->orHas('meeting')->orWhere('admin_present', true))->latest('id')->get()]; }
    private function dataNotifications(): array { return ['items' => AdminNotification::latest('id')->limit(200)->get()]; }
    private function dataAudit(): array { return ['items' => AuditLog::latest('id')->limit(200)->get()]; }

    /* ---------------- actions ---------------- */
    public function userAct(Request $r, User $user, string $act)
    {
        if ($user->isAdmin()) return back()->withErrors(['admin' => 'Admin accounts cannot be changed here']);
        if ($act === 'plan') { $d = $r->validate(['plan' => 'required|in:basic,silver,gold']); $user->update(['plan' => $d['plan']]); }
        elseif ($act === 'block') $user->update(['status' => 'blocked']);
        elseif ($act === 'unblock') $user->update(['status' => 'active']);
        else abort(404);
        AuditLog::log("User {$user->phone}: $act");
        return back()->with('ok', 'Saved');
    }

    public function listingAct(Listing $listing, string $act)
    {
        match ($act) {
            'approve' => $listing->update(['status' => 'active', 'verified' => true, 'approved_at' => now()]),
            'reject' => $listing->update(['status' => 'rejected']),
            'verify' => $listing->update(['verified' => true]),
            'unverify' => $listing->update(['verified' => false]),
            'hide' => $listing->update(['status' => 'pending']),
            'remove' => $listing->update(['status' => 'removed']),
            default => abort(404),
        };
        AuditLog::log("Ad {$listing->ref}: $act");
        return back()->with('ok', 'Saved');
    }

    public function orderAct(OrderService $svc, Order $order, string $act)
    {
        try { $svc->act($order, $act); } catch (\DomainException $e) { return back()->withErrors(['order' => $e->getMessage()]); }
        AuditLog::log("Order {$order->ref}: $act");
        return back()->with('ok', 'Saved');
    }

    public function meeting(Request $r, Order $order)
    {
        $d = $r->validate(['meet_at' => 'required|string|max:60', 'place' => 'required|string|max:120', 'admin_name' => 'required|string|max:60']);
        OrderMeeting::updateOrCreate(['order_id' => $order->id], $d);
        AuditLog::log("Order {$order->ref}: meeting set");
        return back()->with('ok', 'Saved');
    }

    public function disputeAct(Request $r, Dispute $dispute, string $act)
    {
        if ($act === 'refund') { $dispute->update(['status' => 'refund', 'resolved_at' => now()]); $dispute->order->update(['pay_status' => 'refund']); }
        elseif ($act === 'reject') { $d = $r->validate(['resolution' => 'required|string|max:300']); $dispute->update(['status' => 'rejected', 'resolution' => $d['resolution'], 'resolved_at' => now()]); }
        else abort(404);
        AuditLog::log("Dispute on {$dispute->order->ref}: $act");
        return back()->with('ok', 'Saved');
    }

    public function doctorAct(Request $r, Doctor $doctor, string $act)
    {
        $map = ['review' => 'review', 'approve' => 'verified', 'reject' => 'rejected', 'suspend' => 'suspended', 'moreinfo' => 'review', 'reinstate' => 'verified'];
        abort_unless(isset($map[$act]), 404);
        $note = trim((string) $r->input('note'));
        if (in_array($act, ['reject', 'suspend', 'moreinfo'], true) && $note === '') return back()->withErrors(['note' => 'A reason is required']);
        if ($act === 'reinstate' && $doctor->status !== 'suspended') return back()->withErrors(['note' => 'Only suspended doctors can be reinstated']);
        $doctor->setStatus($map[$act], $note ?: null);
        AuditLog::log("Doctor {$doctor->ref}: $act");
        return back()->with('ok', 'Saved');
    }

    /** Private documents: only admins can open them (never a public URL). */
    public function doctorFile(Doctor $doctor, string $which)
    {
        $path = $which === 'cert' ? $doctor->cert_path : ($which === 'lic' ? $doctor->lic_path : null);
        abort_unless($path && Storage::disk('private')->exists($path), 404);
        AuditLog::log("Opened {$which} of {$doctor->ref}");
        return Storage::disk('private')->response($path);
    }

    public function savePayments(Request $r)
    {
        $d = $r->validate([
            'jazz_title' => 'nullable|string|max:80', 'jazz_no' => 'nullable|string|max:20', 'easy_title' => 'nullable|string|max:80', 'easy_no' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:80', 'bank_title' => 'nullable|string|max:80', 'bank_iban' => 'nullable|string|max:40', 'cod' => 'nullable|boolean',
        ]);
        Setting::put('pay_accounts', [
            'jazz' => ['title' => $d['jazz_title'] ?? '', 'no' => $d['jazz_no'] ?? ''], 'easy' => ['title' => $d['easy_title'] ?? '', 'no' => $d['easy_no'] ?? ''],
            'bank' => ['bank' => $d['bank_name'] ?? '', 'title' => $d['bank_title'] ?? '', 'iban' => strtoupper(str_replace(' ', '', $d['bank_iban'] ?? ''))], 'cod' => $r->boolean('cod'),
        ]);
        AuditLog::log('Payment accounts updated');
        return back()->with('ok', 'Saved');
    }

    public function readNotifications() { AdminNotification::where('is_read', false)->update(['is_read' => true]); return back(); }
    public function resolveMessage(ContactMessage $message) { $message->update(['status' => 'resolved']); return back(); }
    public function resolveReport(AdReport $report) { $report->update(['status' => 'resolved']); return back(); }
}
