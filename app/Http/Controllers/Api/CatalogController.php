<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\{DoctorResource, ListingResource};
use App\Models\{Category, Doctor, Listing};
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function listings(Request $r)
    {
        $q = Listing::active()->with(['images', 'category'])->orderByDesc('approved_at');
        if ($c = $r->query('category')) $q->whereHas('category', fn ($x) => $x->where('key', $c));
        if ($c = $r->query('city')) $q->where('city', $c);
        return ListingResource::collection($q->paginate(20));
    }
    public function listing(string $ref) { return new ListingResource(Listing::active()->with(['images', 'category'])->findOrFail(Listing::idFromRef($ref))); }
    public function doctors() { return DoctorResource::collection(Doctor::public()->orderBy('name')->get()); }
    public function categories() { return Category::all(['key', 'name_ur', 'name_en']); }
}
