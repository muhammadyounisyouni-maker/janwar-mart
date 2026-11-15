<?php
namespace App\Http\Controllers;

use App\Http\Requests\DoctorRegisterRequest;
use App\Models\{AdminNotification, Consultation, Doctor};
use App\Services\{ImageService, OtpService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class DoctorController extends Controller
{
    public function index(Request $r)
    {
        $q = Doctor::public();
        if (($c = $r->query('city')) && $c !== 'all') $q->where('city', $c);
        if ($s = trim((string) $r->query('q'))) $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('clinic', 'like', "%$s%")->orWhere('qual', 'like', "%$s%"));
        return view('pages.doctors', ['docs' => $q->orderBy('name')->get()]);
    }

    public function registerForm() { return view('pages.docreg', ['doc' => auth()->user()->doctor]); }

    public function register(DoctorRegisterRequest $r, ImageService $img)
    {
        $u = $r->user();
        $doc = $u->doctor;
        if ($doc && !in_array($doc->status, ['rejected', 'review'], true)) return back()->withErrors(['doctor' => __('You are already registered')]);
        $cert = $img->storePrivate($r->file('cert'));
        $lic = $img->storePrivate($r->file('lic'));
        if (!$cert || !$lic) return back()->withInput()->withErrors(['cert' => __('Certificate and licence must be an image or PDF, max 4 MB')]);
        $d = $r->validated();
        $data = collect($d)->only(['name', 'phone', 'wa', 'email', 'qual', 'uni', 'reg', 'exp', 'clinic', 'city', 'addr'])->all();
        $data += ['cert_name' => $r->file('cert')->getClientOriginalName(), 'cert_path' => $cert, 'lic_name' => $r->file('lic')->getClientOriginalName(), 'lic_path' => $lic, 'consent' => true];
        if ($r->hasFile('photo') && ($p = $img->storePublic($r->file('photo'), 'doctors'))) $data['photo'] = $p;
        $doc = $doc ? tap($doc)->update($data) : Doctor::create($data + ['user_id' => $u->id]);
        $doc->setStatus('pending', null);
        if ($u->role !== 'admin') $u->update(['role' => 'doctor']);
        AdminNotification::push('🩺', "New doctor registration: {$doc->name} ({$doc->ref}) waiting for verification");
        return redirect('/doctor/dashboard')->with('ok', __('Registration sent. The admin will verify your documents.'));
    }

    public function dashboard()
    {
        $doc = auth()->user()->doctor;
        $cons = $doc ? $doc->consultations()->with('user')->latest('id')->get() : collect();
        return view('pages.docdash', ['doc' => $doc, 'cons' => $cons]);
    }

    public function reply(Request $r, int $id)
    {
        $doc = auth()->user()->doctor;
        abort_unless($doc && $doc->status === 'verified', 403);
        $c = $doc->consultations()->findOrFail($id);
        $d = $r->validate(['reply' => 'required|string|min:5|max:2000']);
        $c->update(['reply' => $d['reply'], 'status' => 'answered', 'replied_at' => now()]);
        return back()->with('ok', __('Reply sent'));
    }

    public function consultForm(int $id)
    {
        $doc = Doctor::public()->findOrFail($id);
        return view('pages.consult', ['doc' => $doc]);
    }

    public function consult(Request $r, ImageService $img, int $id)
    {
        $doc = Doctor::public()->findOrFail($id);
        $u = $r->user();
        if ($doc->user_id === $u->id) return back()->withErrors(['text' => __('You cannot consult yourself')]);
        $r->merge(['phone' => OtpService::normalize($r->phone)]);
        $d = $r->validate([
            'animal' => 'nullable|string|max:80', 'text' => 'required|string|min:10|max:2000', 'name' => 'required|string|max:80',
            'phone' => ['required', 'regex:'.config('janwar.phone_regex')], 'photos' => 'nullable|array|max:2', 'photos.*' => 'file|mimes:jpg,jpeg,png,webp|max:3072',
        ]);
        $key = 'consult:'.$u->id;
        if (RateLimiter::tooManyAttempts($key, 10)) return back()->withErrors(['text' => __('Too many consultations, try later')]);
        RateLimiter::hit($key, 3600);
        $ph = [];
        foreach ($r->file('photos', []) as $f) if ($p = $img->storePublic($f, 'consults')) $ph[] = $p;
        $c = Consultation::create(['doctor_id' => $doc->id, 'user_id' => $u->id, 'animal' => $d['animal'] ?? null, 'text' => $d['text'], 'photo1' => $ph[0] ?? null, 'photo2' => $ph[1] ?? null, 'name' => $d['name'], 'phone' => $d['phone']]);
        AdminNotification::push('💬', "New consultation {$c->ref} for {$doc->name}");
        return redirect('/consultations')->with('ok', __('Question sent to the doctor'));
    }

    public function mine()
    {
        return view('pages.consultations', ['cons' => auth()->user()->hasMany(Consultation::class)->with('doctor')->latest('id')->get()]);
    }
}
