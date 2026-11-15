<?php
namespace App\Http\Controllers;

use App\Models\{Category, Doctor, Listing};

class HomeController extends Controller
{
    public function index()
    {
        $active = Listing::active();
        return view('pages.home', [
            'recent' => Listing::active()->with(['images', 'category'])->orderByDesc('approved_at')->limit(8)->get(),
            'verified' => Listing::active()->where('verified', true)->with(['images', 'category'])->orderByDesc('approved_at')->limit(4)->get(),
            'cats' => Category::withCount(['listings as ads' => fn ($q) => $q->where('status', 'active')])->get(),
            'nActive' => $active->count(),
            'nVerified' => Listing::active()->where('verified', true)->count(),
            'nDoctors' => Doctor::public()->count(),
        ]);
    }
    public function lang(string $l)
    {
        if (in_array($l, ['ur', 'en'], true)) session(['lang' => $l]);
        return redirect()->back();
    }
}
