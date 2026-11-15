<?php
/* Business settings of Janwar Mart (same values as the original Python server.py) */
return [
    'plans' => ['basic' => 3, 'silver' => 10, 'gold' => 999],
    'plan_prices' => ['silver' => 1500, 'gold' => 3000],
    'cities' => ['lhr' => ['ur' => 'لاہور', 'en' => 'Lahore'], 'mul' => ['ur' => 'ملتان', 'en' => 'Multan'], 'fsd' => ['ur' => 'فیصل آباد', 'en' => 'Faisalabad'], 'khi' => ['ur' => 'کراچی', 'en' => 'Karachi'], 'isb' => ['ur' => 'اسلام آباد', 'en' => 'Islamabad']],
    'phone_regex' => '/^(03\d{9}|\+923\d{9})$/',
    'sms_provider' => env('SMS_PROVIDER', 'console'),
    'twilio' => ['sid' => env('TWILIO_SID'), 'token' => env('TWILIO_TOKEN'), 'from' => env('TWILIO_FROM')],
    'webhook' => ['url' => env('SMS_WEBHOOK_URL'), 'token' => env('SMS_WEBHOOK_TOKEN')],
    'contact_number' => env('CONTACT_NUMBER', '923001234567'),
    'admin' => ['name' => env('ADMIN_NAME', 'Janwar Mart Admin'), 'phone' => env('ADMIN_PHONE'), 'email' => env('ADMIN_EMAIL'), 'password' => env('ADMIN_PASSWORD')],
];
