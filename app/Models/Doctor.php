<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['consent' => 'boolean']; }
    public function getRefAttribute(): string { return 'DR-'.(3000 + $this->id); }
    public function user() { return $this->belongsTo(User::class); }
    public function histories() { return $this->hasMany(DoctorStatusHistory::class)->orderBy('id'); }
    public function consultations() { return $this->hasMany(Consultation::class); }
    public function scopePublic($q) { return $q->where('status', 'verified')->where('consent', true); }
    public function setStatus(string $status, ?string $note = null): void
    {
        $this->update(['status' => $status, 'note' => $note]);
        $this->histories()->create(['status' => $status, 'note' => $note, 'created_at' => now()]);
    }
}
