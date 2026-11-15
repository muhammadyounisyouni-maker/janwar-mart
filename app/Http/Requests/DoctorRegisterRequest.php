<?php
namespace App\Http\Requests;

use App\Services\OtpService;
use Illuminate\Foundation\Http\FormRequest;

class DoctorRegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    protected function prepareForValidation(): void
    {
        $this->merge(['phone' => OtpService::normalize($this->phone), 'wa' => $this->wa ? OtpService::normalize($this->wa) : null]);
    }
    public function rules(): array
    {
        $ph = ['regex:'.config('janwar.phone_regex')];
        return [
            'name' => 'required|string|max:80', 'phone' => ['required', ...$ph], 'wa' => ['nullable', ...$ph], 'email' => 'nullable|email|max:120',
            'qual' => 'required|string|max:80', 'uni' => 'required|string|max:80', 'reg' => 'required|string|max:40', 'exp' => 'required|integer|min:0|max:60',
            'clinic' => 'required|string|max:100', 'city' => 'required|in:'.implode(',', array_keys(config('janwar.cities'))), 'addr' => 'nullable|string|max:200',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:3072',
            'cert' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:4096', 'lic' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',
            'consent' => 'accepted',
        ];
    }
}
