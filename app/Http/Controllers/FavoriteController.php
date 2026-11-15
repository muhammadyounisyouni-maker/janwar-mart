<?php
namespace App\Http\Controllers;

use App\Models\Listing;

class FavoriteController extends Controller
{
    public function index()
    {
        $list = auth()->user()->favorites()->where('status', '!=', 'removed')->with(['images', 'category'])->get();
        return view('pages.favorites', ['list' => $list, 'favIds' => $list->pluck('id')->all()]);
    }
    public function toggle(string $ref)
    {
        $l = Listing::findOrFail(Listing::idFromRef($ref));
        auth()->user()->favorites()->toggle($l->id);
        return back();
    }
}
