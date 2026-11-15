<?php
namespace App\Http\Controllers;

use App\Models\Listing;
use App\Services\CartService;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}
    public function index() { return view('pages.cart', ['items' => $this->cart->listings()]); }
    public function add(string $ref)
    {
        $l = Listing::active()->findOrFail(Listing::idFromRef($ref));
        if (auth()->id() === $l->owner_id) return back()->withErrors(['cart' => __('You cannot buy your own animal')]);
        $this->cart->add($l->id);
        return back()->with('ok', __('Added to cart'));
    }
    public function buy(string $ref)
    {
        $l = Listing::active()->findOrFail(Listing::idFromRef($ref));
        if (auth()->id() === $l->owner_id) return back()->withErrors(['cart' => __('You cannot buy your own animal')]);
        $this->cart->add($l->id);
        return redirect('/cart');
    }
    public function remove(string $ref)
    {
        $this->cart->remove(Listing::idFromRef($ref));
        return back();
    }
}
