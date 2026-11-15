<?php
namespace App\Services;

use App\Models\Listing;

/** The cart lives in the visitor's session: a list of listing ids. */
class CartService
{
    public function ids(): array { return array_values(array_unique(session('cart', []))); }
    public function add(int $id): void { session(['cart' => array_values(array_unique([...$this->ids(), $id]))]); }
    public function remove(int $id): void { session(['cart' => array_values(array_diff($this->ids(), [$id]))]); }
    public function clear(): void { session()->forget('cart'); }
    public function count(): int { return count($this->ids()); }
    public function listings()
    {
        // only animals that are still on sale can stay in the cart
        return Listing::with(['images', 'category'])->whereIn('id', $this->ids())->where('status', 'active')->get();
    }
}
