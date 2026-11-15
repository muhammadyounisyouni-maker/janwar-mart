<?php
namespace App\Http\Controllers;

use App\Http\Requests\ListingRequest;
use App\Models\{AdminNotification, Category, Listing};
use App\Services\ImageService;

class SellController extends Controller
{
    public function form() { return view('pages.sell', ['cats' => Category::all()]); }

    public function store(ListingRequest $r, ImageService $img)
    {
        $u = $r->user();
        $used = $u->listings()->whereIn('status', ['active', 'pending'])->count();
        if ($used >= $u->adLimit()) return back()->withInput()->withErrors(['plan' => __('Plan limit reached. Upgrade your plan to post more ads.')]);
        $paths = [];
        foreach ($r->file('photos', []) as $f) if ($p = $img->storePublic($f)) $paths[] = $p;
        if (!$paths) return back()->withInput()->withErrors(['photos' => __('Add at least one valid photo (JPG, PNG or WebP, max 3 MB)')]);
        $d = $r->validated();
        $l = Listing::create([
            'title' => $d['title'] ?? null, 'category_id' => Category::where('key', $d['category'])->value('id'), 'price' => $d['price'], 'city' => $d['city'],
            'age' => $d['age'] ?? null, 'breed' => $d['breed'] ?? null, 'weight' => $d['weight'] ?? null, 'descr' => $d['descr'] ?? null,
            'status' => 'pending', 'verified' => false, 'owner_id' => $u->id, 'contact' => \App\Services\OtpService::normalize($d['contact']),
        ]);
        foreach ($paths as $i => $p) $l->images()->create(['path' => $p, 'position' => $i]);
        AdminNotification::push('🐄', "New ad {$l->ref} from {$u->name} waiting for approval");
        return redirect('/my-ads')->with('ok', __('Your ad was submitted. It will be visible after admin approval.').' '.$l->ref);
    }

    public function mine()
    {
        $list = auth()->user()->listings()->where('status', '!=', 'removed')->with(['images', 'category'])->latest('id')->get();
        return view('pages.mine', ['list' => $list]);
    }

    public function destroy(string $ref)
    {
        $l = auth()->user()->listings()->whereIn('status', ['pending', 'active', 'rejected'])->findOrFail(Listing::idFromRef($ref));
        $l->update(['status' => 'removed']);
        return back()->with('ok', __('Ad removed'));
    }
}
