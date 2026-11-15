<?php
namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\{AdminNotification, Dispute, Order, Setting};
use App\Services\{CartService, OrderService};
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private CartService $cart, private OrderService $orders) {}

    public function checkout()
    {
        return view('pages.checkout', ['items' => $this->cart->listings(), 'acc' => Setting::payAccounts()]);
    }

    public function store(CheckoutRequest $r)
    {
        try {
            $o = $this->orders->place($r->user(), $r->validated(), $this->cart->listings()->pluck('id')->all());
        } catch (\DomainException $e) {
            return back()->withInput()->withErrors(['order' => $e->getMessage()]);
        }
        $this->cart->clear();
        return redirect('/orders')->with('ok', __('Request sent. Reference').': '.$o->ref);
    }

    public function index()
    {
        $orders = auth()->user()->orders()->with(['listings.images', 'meeting', 'dispute'])->latest('id')->get();
        return view('pages.orders', ['orders' => $orders, 'acc' => Setting::payAccounts()]);
    }

    public function dispute(Request $r, int $id)
    {
        $o = auth()->user()->orders()->findOrFail($id);
        $d = $r->validate(['reason' => 'required|string|min:10|max:500']);
        if ($o->status !== 'completed' || $o->dispute) return back()->withErrors(['dispute' => __('You cannot open a dispute on this order')]);
        Dispute::create(['order_id' => $o->id, 'user_id' => auth()->id(), 'reason' => $d['reason']]);
        AdminNotification::push('⚖', "Dispute on {$o->ref}");
        return back()->with('ok', __('Dispute sent to admin'));
    }
}
