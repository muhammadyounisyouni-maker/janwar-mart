<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];
    public function listings() { return $this->hasMany(Listing::class); }
    public function label(): string { return app()->getLocale() === 'ur' ? $this->name_ur : $this->name_en; }
}
