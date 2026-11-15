<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $guarded = [];
    protected function casts(): array
    {
        return ['verified' => 'boolean', 'approved_at' => 'datetime', 'sold_at' => 'datetime'];
    }

    // public reference, e.g. JM-1101
    public function getRefAttribute(): string { return 'JM-'.(1100 + $this->id); }
    public static function idFromRef(string $ref): int
    {
        $n = (int) preg_replace('/\D/', '', $ref);
        return $n > 1100 ? $n - 1100 : $n;
    }
    public function scopeActive(Builder $q): Builder { return $q->where('status', 'active'); }
    public function category() { return $this->belongsTo(Category::class); }
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function images() { return $this->hasMany(ListingImage::class)->orderBy('position'); }
    public function reports() { return $this->hasMany(AdReport::class); }
    public function cover(): ?string
    {
        $i = $this->images->first();
        return $i ? asset('storage/'.$i->path) : null;
    }
    public function displayTitle(): string
    {
        return $this->title ?: ($this->category?->label().' — '.$this->breed);
    }
}
