<?php

use Illuminate\Support\Facades\Route;

// Public Controller Imports
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\EducationController;
use App\Http\Controllers\Buyer\MarketplaceController;

// Auth Controller Imports
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Chat Controller Imports
use App\Http\Controllers\Chat\ConversationController;
use App\Http\Controllers\Chat\MessageController;

// Seller Controller Imports
use App\Http\Controllers\Seller\SellerDashboardController;
use App\Http\Controllers\Seller\SellerProfileController;
use App\Http\Controllers\Seller\SellerWasteListingController;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\Seller\SellerWalletController;
use App\Http\Controllers\Seller\SellerWithdrawalController;

// Buyer Controller Imports
use App\Http\Controllers\Buyer\BuyerDashboardController;
use App\Http\Controllers\Buyer\BuyerProfileController;
use App\Http\Controllers\Buyer\BuyerOrderController;
use App\Http\Controllers\Buyer\BuyerPaymentController;
use App\Http\Controllers\Buyer\BuyerFavoriteController;
use App\Http\Controllers\Buyer\BuyerReviewController;

// Admin Controller Imports
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminListingVerificationController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\AdminComplaintController;
use App\Http\Controllers\Admin\AdminEducationContentController;
use App\Http\Controllers\Admin\AdminReportController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/education', [EducationController::class, 'index'])->name('education.index');
Route::get('/education/{educationContent:slug}', [EducationController::class, 'show'])->name('education.show');
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/marketplace/{wasteListing}', [MarketplaceController::class, 'show'])->name('marketplace.show');
Route::get('/tentang', [HomeController::class, 'tentang'])->name('tentang');

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::middleware(['auth'])->group(function () {
    Route::get('/user/verifikasi/pending', function () {
        $user = auth()->user();
        if ($user->status === \App\Models\User::STATUS_INACTIVE || $user->status === \App\Models\User::STATUS_SUSPENDED) {
            return redirect()->route('verification.rejected');
        }
        
        // If active but already acknowledged (email_verified_at is not null), go to dashboard
        if ($user->isActive() && $user->email_verified_at !== null) {
            return redirect()->route('choose.role');
        }
        
        return view('auth.verification-pending', compact('user'));
    })->name('verification.pending');

    Route::post('/user/verifikasi/acknowledge', function () {
        $user = auth()->user();
        if ($user->isActive()) {
            $user->update(['email_verified_at' => now()]);
            return redirect()->route('choose.role')->with('success', 'Selamat datang di Recyclink!');
        }
        return redirect()->route('verification.pending');
    })->name('verification.acknowledge');

    Route::get('/user/verifikasi/rejected', function () {
        $user = auth()->user();
        if ($user->isActive()) {
            return redirect()->route('choose.role');
        }
        if ($user->status === \App\Models\User::STATUS_PENDING) {
            return redirect()->route('verification.pending');
        }
        return view('auth.verification-rejected', compact('user'));
    })->name('verification.rejected');

    Route::post('/user/verifikasi/resubmit', [App\Http\Controllers\Auth\RegisterController::class, 'resubmitVerification'])->name('verification.resubmit');

    Route::get('/user/choose-role', [RegisterController::class, 'showChooseRoleForm'])->name('choose.role');
    Route::post('/user/choose-role', [RegisterController::class, 'storeRole'])->name('choose.role.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (General)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {
    // Chat & Conversations (Shared / Peer-based)
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::post('/listings/{wasteListing}/conversation', [ConversationController::class, 'start'])->name('conversations.start');
    
    // Protected by conversation participant check
    Route::middleware('conversation.participant')->group(function () {
        Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
        Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])->name('conversations.messages.store');
    });
});

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => 'dashboard/user/seller',
    'as' => 'seller.',
    'middleware' => ['auth', 'active', 'role:seller']
], function () {
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
    
    // Seller Profile (Accessible with incomplete profile for edit/update)
    Route::get('/profile', [SellerProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [SellerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [SellerProfileController::class, 'update'])->name('profile.update');

    // Routes requiring completed profile
    Route::middleware(['profile.completed'])->group(function () {
        // Listings index & detail
        Route::get('/listings', [SellerWasteListingController::class, 'index'])->name('listings.index');
        Route::get('/listings/{wasteListing}', [SellerWasteListingController::class, 'show'])->name('listings.show');

        // Orders List & Show
        Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
        
        Route::middleware('order.participant')->group(function () {
            Route::get('/orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
            Route::patch('/orders/{order}/reject', [SellerOrderController::class, 'reject'])->name('orders.reject');
            Route::patch('/orders/{order}/processing', [SellerOrderController::class, 'processing'])->name('orders.processing');
        });

        // Wallet & Finance
        Route::get('/wallet', [SellerWalletController::class, 'index'])->name('wallet.index');
        Route::get('/wallet/transactions', [SellerWalletController::class, 'transactions'])->name('wallet.transactions');

        // Withdrawals
        Route::get('/withdrawals', [SellerWithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::get('/withdrawals/create', [SellerWithdrawalController::class, 'create'])->name('withdrawals.create');
        Route::post('/withdrawals', [SellerWithdrawalController::class, 'store'])->name('withdrawals.store');

        // Routes requiring BOTH completed profile AND seller verification
        Route::middleware(['seller.verified'])->group(function () {
            // Waste Listings management (resource except index/show already defined above)
            Route::get('/listings/create', [SellerWasteListingController::class, 'create'])->name('listings.create');
            Route::post('/listings', [SellerWasteListingController::class, 'store'])->name('listings.store');
            Route::get('/listings/{wasteListing}/edit', [SellerWasteListingController::class, 'edit'])->name('listings.edit');
            Route::put('/listings/{wasteListing}', [SellerWasteListingController::class, 'update'])->name('listings.update');
            Route::delete('/listings/{wasteListing}', [SellerWasteListingController::class, 'destroy'])->name('listings.destroy');
            
            Route::patch('/listings/{wasteListing}/availability', [SellerWasteListingController::class, 'changeAvailability'])->name('listings.availability');

            // Accept orders
            Route::patch('/orders/{order}/accept', [SellerOrderController::class, 'accept'])->name('orders.accept')->middleware('order.participant');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Buyer Routes
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => 'dashboard/user/buyer',
    'as' => 'buyer.',
    'middleware' => ['auth', 'active', 'role:buyer']
], function () {
    Route::get('/dashboard', [BuyerDashboardController::class, 'index'])->name('dashboard');
    
    // Buyer Profile (Accessible with incomplete profile for edit/update)
    Route::get('/profile', [BuyerProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [BuyerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [BuyerProfileController::class, 'update'])->name('profile.update');

    // Routes requiring completed profile
    Route::middleware(['profile.completed'])->group(function () {
        // Orders
        Route::get('/orders', [BuyerOrderController::class, 'index'])->name('orders.index');
        Route::post('/listings/{wasteListing}/orders', [BuyerOrderController::class, 'store'])->name('orders.store');
        
        Route::middleware('order.participant')->group(function () {
            Route::get('/orders/{order}', [BuyerOrderController::class, 'show'])->name('orders.show');
            Route::patch('/orders/{order}/cancel', [BuyerOrderController::class, 'cancel'])->name('orders.cancel');
            Route::patch('/orders/{order}/complete', [BuyerOrderController::class, 'complete'])->name('orders.complete');

            // Payments
            Route::get('/orders/{order}/payment/create', [BuyerPaymentController::class, 'create'])->name('orders.payment.create');
            Route::post('/orders/{order}/payment', [BuyerPaymentController::class, 'store'])->name('orders.payment.store');

            // Reviews
            Route::post('/orders/{order}/reviews', [BuyerReviewController::class, 'store'])->name('orders.reviews.store');
        });

        Route::get('/payments/{payment}', [BuyerPaymentController::class, 'show'])->name('payments.show');

        // Favorites
        Route::get('/favorites', [BuyerFavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/favorites/{wasteListing}', [BuyerFavoriteController::class, 'store'])->name('favorites.store');
        Route::delete('/favorites/{wasteListing}', [BuyerFavoriteController::class, 'destroy'])->name('favorites.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => 'dashboard/admin',
    'as' => 'admin.',
    'middleware' => ['auth', 'active', 'role:admin']
], function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Listing Verification
    Route::get('/listings/verification', [AdminListingVerificationController::class, 'index'])->name('listings.verification.index');
    Route::get('/listings/{wasteListing}/verification', [AdminListingVerificationController::class, 'show'])->name('listings.verification.show');
    Route::patch('/listings/{wasteListing}/approve', [AdminListingVerificationController::class, 'approve'])->name('listings.verification.approve');
    Route::patch('/listings/{wasteListing}/reject', [AdminListingVerificationController::class, 'reject'])->name('listings.verification.reject');
    Route::patch('/listings/{wasteListing}/deactivate', [AdminListingVerificationController::class, 'deactivate'])->name('listings.verification.deactivate');

    // Transaction Management
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{order}', [AdminTransactionController::class, 'show'])->name('transactions.show');

    // Complaint Management
    Route::get('/complaints', [AdminComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/{complaint}', [AdminComplaintController::class, 'show'])->name('complaints.show');
    Route::patch('/complaints/{complaint}/process', [AdminComplaintController::class, 'process'])->name('complaints.process');
    Route::patch('/complaints/{complaint}/resolve', [AdminComplaintController::class, 'resolve'])->name('complaints.resolve');
    Route::patch('/complaints/{complaint}/reject', [AdminComplaintController::class, 'reject'])->name('complaints.reject');

    // Education Management
    Route::resource('education-contents', AdminEducationContentController::class);
    Route::patch('/education-contents/{educationContent}/publish', [AdminEducationContentController::class, 'publish'])->name('education-contents.publish');
    Route::patch('/education-contents/{educationContent}/archive', [AdminEducationContentController::class, 'archive'])->name('education-contents.archive');

    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/transactions', [AdminReportController::class, 'transactions'])->name('reports.transactions');
    Route::get('/reports/listings', [AdminReportController::class, 'listings'])->name('reports.listings');
    Route::get('/reports/users', [AdminReportController::class, 'users'])->name('reports.users');
});
