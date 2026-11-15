<?php
namespace App\Http\Controllers;

use App\Models\{AdminNotification, ContactMessage};
use App\Services\OtpService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function help() { return view('pages.help'); }
    public function plans() { return view('pages.plans'); }
    public function contact() { return view('pages.contact'); }

    public function sendContact(Request $r)
    {
        $r->merge(['phone' => OtpService::normalize($r->phone)]);
        $d = $r->validate([
            'type' => 'required|in:ad_issue,seller_complaint,account_login,other', 'ad_ref' => 'nullable|string|max:20',
            'message' => 'required|string|max:1000', 'name' => 'nullable|string|max:80', 'phone' => ['required', 'regex:'.config('janwar.phone_regex')],
        ]);
        ContactMessage::create($d);
        AdminNotification::push('✉', "Contact message ({$d['type']}) from {$d['phone']}");
        return back()->with('ok', __('Message sent to admin'));
    }

    public function profile() { return view('pages.profile', ['u' => auth()->user()]); }

    public function requestPlan(Request $r)
    {
        $d = $r->validate(['plan' => 'required|in:silver,gold']);
        $u = $r->user();
        AdminNotification::push('💎', "Plan request: {$u->name} ({$u->phone}) wants {$d['plan']}");
        return back()->with('ok', __('Plan request sent. The admin will contact you.'));
    }
}
