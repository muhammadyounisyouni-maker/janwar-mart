<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Consultation extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['replied_at' => 'datetime']; }
    public function getRefAttribute(): string { return 'CN-'.(4000 + $this->id); }
    public function doctor() { return $this->belongsTo(Doctor::class); }
    public function user() { return $this->belongsTo(User::class); }
}
