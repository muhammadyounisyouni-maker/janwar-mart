<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Setting extends Model
{
    public $timestamps = false; public $incrementing = false; protected $primaryKey = 'key'; protected $keyType = 'string'; protected $guarded = [];
    protected function casts(): array { return ['value' => 'array']; }
    public static function get(string $key, $default = null) { return static::find($key)?->value ?? $default; }
    public static function put(string $key, $value): void { static::updateOrCreate(['key' => $key], ['value' => $value]); }
    public static function payAccounts(): array
    {
        return static::get('pay_accounts', ['jazz' => ['title' => '', 'no' => ''], 'easy' => ['title' => '', 'no' => ''], 'bank' => ['bank' => '', 'title' => '', 'iban' => ''], 'cod' => true]);
    }
}
