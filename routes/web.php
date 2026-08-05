<?php

use App\Http\Controllers\AssetInterestController;
use App\Http\Controllers\AssetMatchingAuthController;
use App\Http\Controllers\AssetMatchingController;
use App\Http\Controllers\OwnerAssetController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectContractController;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Project contract preview
// Project contract preview
Route::get('/admin/projects/{project}/contract', [ProjectContractController::class, 'show'])
    ->name('projects.contract')
    ->middleware('auth');

// Project report preview
Route::get('/admin/projects/{project}/report', [\App\Http\Controllers\ProjectReportController::class, 'show'])
    ->name('projects.report')
    ->middleware('auth');

// Homepage
Route::get('/', [PageController::class, 'home'])->name('home');

// About page
Route::get('/about', [PageController::class, 'about'])->name('about');

// Timeline page
Route::get('/timeline', [PageController::class, 'timeline'])->name('timeline');

// Services page
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/services/{slug}', [PageController::class, 'serviceDetail'])->name('services.show');

// Portfolio page
Route::get('/portfolio', [PageController::class, 'portfolio'])->name('portfolio');

// Blog page
Route::get('/blog', [PageController::class, 'blog'])->name('blog');

// Contact page
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Contact form submission
Route::post('/contact', function (Request $request) {
    $validated = $request->validate([
        'full_name' => 'required|string|max:100',
        'email' => 'required|email|max:100',
        'phone' => 'nullable|string|max:20',
        'subject' => 'nullable|string|max:150',
        'message' => 'required|string',
    ]);

    Inquiry::create($validated);

    return back()->with('success', 'Thank you for your message! We will get back to you soon.');
})->name('inquiries.store');

// Sitemap routes
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-pages.xml', [\App\Http\Controllers\SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-blog.xml', [\App\Http\Controllers\SitemapController::class, 'blog'])->name('sitemap.blog');
Route::get('/sitemap-services.xml', [\App\Http\Controllers\SitemapController::class, 'services'])->name('sitemap.services');
Route::get('/sitemap-assets.xml', [\App\Http\Controllers\SitemapController::class, 'assets'])->name('sitemap.assets');

// Robots.txt
Route::get('/robots.txt', function () {
    $content = "User-agent: *\n";
    $content .= "Allow: /\n";
    $content .= "Disallow: /gp-strategix\n";
    $content .= "Disallow: /gp-strategix/*\n\n";
    $content .= 'Sitemap: '.url('/sitemap.xml')."\n";

    return response($content, 200)->header('Content-Type', 'text/plain');
})->name('robots');

// Newsletter subscription
Route::post('/newsletter/subscribe', [\App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::post('/newsletter/unsubscribe', [\App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Grapadi Capital Connect (Asset Matching)
Route::prefix('capital-connect')->name('matching.')->group(function () {
    Route::get('/', [AssetMatchingController::class, 'index'])->name('index');
    Route::get('/aset/{asset}', [AssetMatchingController::class, 'show'])->name('show');

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AssetMatchingAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AssetMatchingAuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
        Route::get('/daftar', [AssetMatchingAuthController::class, 'showRegister'])->name('register');
        Route::post('/daftar', [AssetMatchingAuthController::class, 'register'])->middleware('throttle:6,1')->name('register.store');
        Route::get('/lupa-password', [AssetMatchingAuthController::class, 'showForgotPassword'])->name('password.request');
        Route::post('/lupa-password', [AssetMatchingAuthController::class, 'sendResetLink'])->middleware('throttle:6,1')->name('password.email');
        Route::get('/reset-password/{token}', [AssetMatchingAuthController::class, 'showResetPassword'])->name('password.reset');
        Route::post('/reset-password', [AssetMatchingAuthController::class, 'resetPassword'])->name('password.update');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AssetMatchingAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AssetMatchingController::class, 'dashboard'])->name('dashboard');
        Route::get('/aset-baru', [OwnerAssetController::class, 'create'])->name('assets.create');
        Route::post('/aset-baru', [OwnerAssetController::class, 'store'])->name('assets.store');
        Route::get('/aset-saya/{asset}/edit', [OwnerAssetController::class, 'edit'])->name('assets.edit');
        Route::put('/aset-saya/{asset}', [OwnerAssetController::class, 'update'])->name('assets.update');
        Route::post('/aset-saya/{asset}/submit', [OwnerAssetController::class, 'submit'])->name('assets.submit');
        Route::post('/aset-saya/{asset}/arsipkan', [OwnerAssetController::class, 'archive'])->name('assets.archive');
        Route::post('/aset/{asset}/minat', [AssetInterestController::class, 'store'])->name('interests.store');
    });
});

// Legacy 301 redirects for /asset-matching and /capital
Route::get('/asset-matching', function () {
    return redirect()->route('matching.index', [], 301);
});
Route::get('/asset-matching/{path}', function ($path) {
    return redirect('/capital-connect/'.$path, 301);
})->where('path', '.*');

Route::get('/capital', function () {
    return redirect()->route('matching.index', [], 301);
});
Route::get('/capital/{path}', function ($path) {
    return redirect('/capital-connect/'.$path, 301);
})->where('path', '.*');

// Redirect /login to Filament admin login
Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

// Blog article detail (catch-all, must be LAST route)
Route::get('/{slug}', [PageController::class, 'articleDetail'])->name('blog.show');
