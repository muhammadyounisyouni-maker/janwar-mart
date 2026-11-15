<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AdminNotification extends Model
{
    public $timestamps = false; protected $guarded = [];
    protected function casts(): array { return ['is_read' => 'boolean', 'created_at' => 'datetime']; }
    public static function notify(string $icon, string $text): void { static::create(['icon' => $icon, 'text' => mb_substr($text, 0, 300), 'created_at' => now()]); }
}