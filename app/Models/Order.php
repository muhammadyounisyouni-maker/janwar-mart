<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['admin_present' => 'boolean', 'completed_at' => 'datetime']; }
    public function getRefAttribute(): string { return 'RQ-'.(2000 + $this->id); }
    public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function listings() { return $this->belongsToMany(Listing::class, 'order_items')->withPivot('price'); }
    public function meeting() { return $this->hasOne(OrderMeeting::class); }
    public function dispute() { return $this->hasOne(Dispute::class); }
}
