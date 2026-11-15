<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LoginEvent extends Model
{
    public $timestamps = false; protected $guarded = [];
    protected function casts(): array { return ['created_at' => 'datetime']; }
    public function user() { return $this->belongsTo(User::class); }
    public static function record(?User $u, string $who, string $event): void
    {
        static::create(['user_id' => $u?->id, 'who' => mb_substr($who, 0, 120), 'event' => $event, 'ip' => request()->ip(), 'ua' => mb_substr((string) request()->userAgent(), 0, 200), 'created_at' => now()]);
    }
}
