<?php
namespace App\Http\Requests;

use App\Services\OtpService;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    protected function prepareForValidation(): void { $this->merge(['phone' => OtpService::normalize($this->phone)]); }
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:80', 'phone' => ['required', 'regex:'.config('janwar.phone_regex')],
            'city' => 'nullable|string|max:60', 'note' => 'nullable|string|max:300', 'admin_present' => 'nullable|boolean',
            'pay_method' => 'required|in:jazz,easy,bank,cod', 'pay_tid' => 'nullable|string|max:60',
        ];
    }
}
