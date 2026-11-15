<?php
namespace App\Http\Requests;

use App\Services\OtpService;
use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    protected function prepareForValidation(): void { $this->merge(['phone' => OtpService::normalize($this->phone), 'email' => $this->email ? strtolower(trim($this->email)) : null]); }
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:80', 'phone' => ['required', 'regex:'.config('janwar.phone_regex'), 'unique:users,phone'],
            'email' => 'nullable|email|max:120|unique:users,email', 'password' => 'required|string|min:6|max:100',
            'role' => 'required|in:buyer,seller,doctor', 'otp' => 'required|digits:6',
        ];
    }
}
