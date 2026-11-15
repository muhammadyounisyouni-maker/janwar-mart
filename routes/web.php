<?php

use App\Http\Controllers\{AnimalController, AuthController, TwoFactorController, CartController, DoctorController, FavoriteController, HomeController, OrderController, PageController, SellController};
use App\Http\Controllers\Admin\DashboardController as Admin;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lang/{l}', [HomeController::class, 'lang']);

Route::get('/animals', [AnimalController::class, 'index'])->name('animals');
Route::get('/animals/{ref}', [AnimalController::class, 'show'])->name('animal');
Route::post('/animals/{ref}/report', [AnimalController::class, 'report'])->middleware('throttle:10,1');

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/{ref}', [CartController::class, 'add']);
Route::delete('/cart/{ref}', [CartController::class, 'remove']);
Route::post('/buy/{ref}', [CartController::class, 'buy']);

Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors');
Route::get('/help', [PageController::class, 'help'])->name('help');
Route::get('/plans', [PageController::class, 'plans'])->name('plans');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->middleware('throttle:5,1');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'form'])->name('login');
    Route::get('/register', [AuthController::class, 'form'])->name('register');
    Route::get('/password/reset', [AuthController::class, 'form'])->name('password.reset');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:20,1');
    Route::post('/register', [AuthController::class, 'signup'])->middleware('throttle:10,1');
    Route::post('/password/reset', [AuthController::class, 'reset'])->middleware('throttle:10,1');
    Route::get('/two-factor', [TwoFactorController::class, 'challenge'])->name('two-factor');
    Route::post('/two-factor', [TwoFactorController::class, 'verify'])->middleware('throttle:10,1');
});
Route::post('/otp/send', [AuthController::class, 'sendOtp'])->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/sell', [SellController::class, 'form'])->name('sell');
    Route::post('/sell', [SellController::class, 'store']);
    Route::get('/my-ads', [SellController::class, 'mine'])->name('my-ads');
    Route::delete('/my-ads/{ref}', [SellController::class, 'destroy']);

    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::post('/orders/{id}/dispute', [OrderController::class, 'dispute']);

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
    Route::post('/favorites/{ref}', [FavoriteController::class, 'toggle']);

    Route::get('/two-factor/setup', [TwoFactorController::class, 'setup'])->name('two-factor.setup');
    Route::post('/two-factor/enable', [TwoFactorController::class, 'enable']);
    Route::post('/two-factor/disable', [TwoFactorController::class, 'disable']);

    Route::get('/profile', [PageController::class, 'profile'])->name('profile');
    Route::post('/profile/plan', [PageController::class, 'requestPlan']);

    Route::get('/doctor/register', [DoctorController::class, 'registerForm'])->name('doctor.register');
    Route::post('/doctor/register', [DoctorController::class, 'register']);
    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
    Route::post('/doctor/consultations/{id}/reply', [DoctorController::class, 'reply']);
    Route::get('/doctors/{id}/consult', [DoctorController::class, 'consultForm'])->name('consult');
    Route::post('/doctors/{id}/consult', [DoctorController::class, 'consult']);
    Route::get('/consultations', [DoctorController::class, 'mine'])->name('consultations');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [Admin::class, 'show']);
    Route::get('/tab/{tab}', [Admin::class, 'show'])->name('admin.tab');
    Route::post('/users/{user}/{act}', [Admin::class, 'userAct']);
    Route::post('/listings/{listing}/{act}', [Admin::class, 'listingAct']);
    Route::post('/orders/{order}/meeting', [Admin::class, 'meeting']);
    Route::post('/orders/{order}/{act}', [Admin::class, 'orderAct']);
    Route::post('/disputes/{dispute}/{act}', [Admin::class, 'disputeAct']);
    Route::post('/doctors/{doctor}/{act}', [Admin::class, 'doctorAct']);
    Route::get('/doctors/{doctor}/file/{which}', [Admin::class, 'doctorFile']);
    Route::post('/payments', [Admin::class, 'savePayments']);
    Route::post('/notifications/read', [Admin::class, 'readNotifications']);
    Route::post('/messages/{message}/resolve', [Admin::class, 'resolveMessage']);
    Route::post('/reports/{report}/resolve', [Admin::class, 'resolveReport']);
});