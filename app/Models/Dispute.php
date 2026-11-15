<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Dispute extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['resolved_at' => 'datetime']; }
    public function order() { return $this->belongsTo(Order::class); }
}
