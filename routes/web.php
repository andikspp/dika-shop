<?php

use App\Http\Controllers\BuyerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Http\Controllers\ProductController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('back.pages.buyer.login-buyer');
});

// BUYER
// halaman login

Route::get('/login', [BuyerController::class, 'login'])->name('buyer.login');
// halaman beranda
Route::get('/home', [BuyerController::class, 'tampilkanHome'])->middleware(['auth', 'verified'])->name('buyer.home');
// halaman register
Route::get('/register', [BuyerController::class, 'registerPage'])->name('buyer.register');
// proses register
Route::post('/register', [UserController::class, 'registerBuyer'])->name('register-buyer');
// proses login
Route::post('/login', [UserController::class, 'loginBuyer'])->name('login-buyer');
// proses logout
Route::post('/logout', [UserController::class, 'logoutBuyer'])->name('logout.buyer');
// halaman verifikasi email berhasil
Route::get('/verification-success', [BuyerController::class, 'success'])->name('verification-success');
// verifikasi email
Route::get('/email/verify', function () {
    return view('back.pages.buyer.verify-notice');
})->middleware('auth')->name('verification.notice');
// Handler verifikasi email dengan pesan success
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    // Tambahkan pesan success ke dalam sesi
    return redirect('/home')->with('success', 'Email verification successful!');
})->middleware(['auth', 'signed'])->name('verification.verify');
// pengiriman ulang email verifikasi
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
// Forgot Password
// Route untuk menampilkan form forgot password
Route::get('/forgot-password', function () {
    return view('back.pages.buyer.forgot-password');
})->middleware('guest')->name('password.request');
Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');
// Password reset form
Route::get('/reset-password/{token}', function (string $token) {
    return view('back.pages.buyer.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');
// Handler reset password
Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('buyer.login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');

// PRODUK
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::middleware('auth')->group(function () {
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
});
//Menampilkan kategori obat herbal
Route::get('/products/obat-herbal', [ProductController::class, 'showObat'])->name('product.obat');
// menampilkan kategori sabun cuci
Route::get('/products/sabun-cuci', [ProductController::class, 'showSabun'])->name('product.sabun');
// menampilkan kategori makanan
Route::get('/products/makanan', [ProductController::class, 'showFood'])->name('product.food');
// menampilkan halaman detail product
// menampilkan halaman detail product by ID
Route::middleware('auth')->group(function () {
    Route::get('/products/{id}', [ProductController::class, 'showDetail'])->name('products.show');
});
// FITUR SEARCH PRODUK
Route::middleware('auth')->group(function () {
    Route::get('/search', [ProductController::class, 'searchByName'])->name('searchByName');
});
// CART
Route::middleware('auth')->group(function () {
    Route::get('/cart', [ProductController::class, 'cartPage'])->name('cart');
    Route::post('/add-to-cart', [CartController::class, 'addCart'])->name('cart.add');
    Route::delete('/cart/{id}', [CartController::class, 'removeCart'])->name('cart.remove');
});
