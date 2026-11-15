<?php
namespace App\Services;

use App\Models\{AdminNotification, Listing, Order, Setting, User};
use Illuminate\Support\Facades\DB;

class OrderService
{
    /** @throws \DomainException */
    public function place(User $buyer, array $d, array $ids): Order
    {
        if (!$ids) throw new \DomainException(__('Cart is empty'));
        $method = $d['pay_method'];
        $acc = Setting::payAccounts();
        $ok = match ($method) {
            'jazz' => !empty($acc['jazz']['no']), 'easy' => !empty($acc['easy']['no']),
            'bank' => !empty($acc['bank']['iban']), 'cod' => !empty($acc['cod']), default => false,
        };
        if (!$ok) throw new \DomainException(__('This payment method is not available'));

        return DB::transaction(function () use ($buyer, $d, $ids, $method) {
            $ls = Listing::whereIn('id', $ids)->lockForUpdate()->get();
            if ($ls->count() !== count($ids) || $ls->contains(fn ($l) => $l->status !== 'active')) throw new \DomainException(__('An animal in your cart is no longer available'));
            if ($ls->contains(fn ($l) => $l->owner_id === $buyer->id)) throw new \DomainException(__('You cannot buy your own animal'));
            $tid = $method === 'cod' ? null : trim((string) ($d['pay_tid'] ?? ''));
            $o = Order::create([
                'buyer_id' => $buyer->id, 'name' => $d['name'], 'phone' => $d['phone'], 'city' => $d['city'] ?? null, 'note' => $d['note'] ?? null,
                'admin_present' => !empty($d['admin_present']), 'total' => $ls->sum('price'), 'status' => 'requested',
                'pay_method' => $method, 'pay_tid' => $tid ?: null,
                'pay_status' => $method === 'cod' ? 'cod' : ($tid ? 'submitted' : 'unpaid'),
            ]);
            foreach ($ls as $l) { $o->items()->create(['listing_id' => $l->id, 'price' => $l->price]); $l->update(['status' => 'reserved']); }
            AdminNotification::push('🧾', "New purchase request {$o->ref} from {$o->name} ({$o->phone}) — Rs ".number_format($o->total));
            return $o;
        });
    }

    /** Admin actions on an order; same rules as the original server.py */
    public function act(Order $o, string $act): void
    {
        $ls = $o->listings;
        match ($act) {
            'contact' => $o->update(['status' => 'contacted']),
            'paid' => $o->update(['pay_status' => 'confirmed']),
            'payreject' => $o->update(['pay_status' => 'rejected']),
            'complete' => $this->complete($o, $ls),
            'cancel' => $this->cancel($o, $ls),
            default => throw new \DomainException('bad action'),
        };
    }
    private function complete(Order $o, $ls): void
    {
        if ($o->pay_status !== 'confirmed') throw new \DomainException(__('Confirm the payment first'));
        foreach ($ls as $l) $l->update(['status' => 'sold', 'sold_at' => now(), 'buyer_name' => $o->name, 'buyer_phone' => $o->phone, 'sold_price' => $l->price]);
        $o->update(['status' => 'completed', 'completed_at' => now()]);
    }
    private function cancel(Order $o, $ls): void
    {
        foreach ($ls as $l) if ($l->status === 'reserved') $l->update(['status' => 'active']);
        $o->update(['status' => 'cancelled', 'pay_status' => in_array($o->pay_status, ['confirmed', 'submitted']) ? 'refund' : $o->pay_status]);
    }
}
