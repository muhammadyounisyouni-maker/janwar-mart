<?php
namespace App\Http\Controllers;

use App\Models\{AdminNotification, AdReport, Category, Listing};
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    public function index(Request $r)
    {
        $q = Listing::active()->with(['images', 'category']);
        if ($s = trim((string) $r->query('q'))) {
            $cityKeys = collect($this->cities())->filter(fn ($n) => mb_stripos($n['en'], $s) !== false || str_contains($n['ur'], $s))->keys()->all();
            $q->where(function ($w) use ($s, $cityKeys) {
                $w->where('title', 'like', "%$s%")->orWhere('breed', 'like', "%$s%")->orWhere('descr', 'like', "%$s%")
                  ->orWhereHas('category', fn ($c) => $c->where('name_ur', 'like', "%$s%")->orWhere('name_en', 'like', "%$s%")->orWhere('key', 'like', "%$s%"))
                  ->orWhereIn('city', $cityKeys);
            });
        }
        if (($c = $r->query('cat')) && $c !== 'all') $q->whereHas('category', fn ($x) => $x->where('key', $c));
        if (($city = $r->query('city')) && $city !== 'all') $q->where('city', $city);
        if ($min = (int) $r->query('min')) $q->where('price', '>=', $min);
        if ($max = (int) $r->query('max')) $q->where('price', '<=', $max);
        match ($r->query('sort')) {
            'low' => $q->orderBy('price'), 'high' => $q->orderByDesc('price'),
            default => $q->orderByDesc('approved_at')->orderByDesc('id'),
        };
        $list = $q->paginate(12)->withQueryString();
        $favIds = auth()->check() ? auth()->user()->favorites()->pluck('listings.id')->all() : [];
        return view('pages.browse', ['list' => $list, 'cats' => Category::all(), 'favIds' => $favIds]);
    }

    public function show(string $ref)
    {
        $l = Listing::with(['images', 'category', 'owner'])->findOrFail(Listing::idFromRef($ref));
        $mine = auth()->check() && auth()->id() === $l->owner_id;
        abort_unless($l->status === 'active' || $mine || auth()->user()?->isAdmin(), 404);
        $fav = auth()->check() && auth()->user()->favorites()->where('listings.id', $l->id)->exists();
        return view('pages.animal', ['l' => $l, 'fav' => $fav, 'mine' => $mine]);
    }

    public function report(Request $r, string $ref)
    {
        $l = Listing::findOrFail(Listing::idFromRef($ref));
        $d = $r->validate(['reason' => 'nullable|string|max:300']);
        AdReport::create(['listing_id' => $l->id, 'user_id' => auth()->id(), 'reason' => $d['reason'] ?? null]);
        AdminNotification::push('🚩', "Ad report on {$l->ref}");
        return back()->with('ok', __('Report received'));
    }
}
