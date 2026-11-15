<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AuditLog extends Model
{
    public $timestamps = false; protected $guarded = [];
    protected function casts(): array { return ['created_at' => 'datetime']; }
    public static function log(string $action): void { static::create(['admin_id' => auth()->id(), 'action' => mb_substr($action, 0, 300), 'created_at' => now()]); }
}
