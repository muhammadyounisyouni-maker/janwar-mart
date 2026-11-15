<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListingRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:120', 'category' => 'required|exists:categories,key',
            'price' => 'required|integer|min:1|max:100000000', 'city' => 'required|in:'.implode(',', array_keys(config('janwar.cities'))),
            'age' => 'nullable|string|max:10', 'breed' => 'nullable|string|max:60', 'weight' => 'nullable|string|max:10', 'descr' => 'nullable|string|max:1000',
            'contact' => ['required', 'regex:'.config('janwar.phone_regex')], 'consent' => 'accepted',
            'photos' => 'required|array|min:1|max:4', 'photos.*' => 'file|mimes:jpg,jpeg,png,webp|max:3072',
        ];
    }
}
