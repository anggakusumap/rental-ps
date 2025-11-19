<?php

use App\Http\Controllers\{
    DashboardController,
    ConsoleController,
    ConsoleTypeController,
    PackageController,
    RentalSessionController,
    FoodItemController,
    FoodCategoryController,
    OrderController,
    InvoiceController,
    ReportController
};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Console Management
    Route::resource('consoles', ConsoleController::class);
    Route::resource('console-types', ConsoleTypeController::class);
    Route::resource('packages', PackageController::class);

    // Rental Sessions
    Route::get('rental-sessions', [RentalSessionController::class, 'index'])->name('rental-sessions.index');
    Route::get('rental-sessions/create', [RentalSessionController::class, 'create'])->name('rental-sessions.create');
    Route::post('rental-sessions', [RentalSessionController::class, 'store'])->name('rental-sessions.store');
    Route::get('rental-sessions/{rentalSession}', [RentalSessionController::class, 'show'])->name('rental-sessions.show');
    Route::post('rental-sessions/{rentalSession}/pause', [RentalSessionController::class, 'pause'])->name('rental-sessions.pause');
    Route::post('rental-sessions/{rentalSession}/resume', [RentalSessionController::class, 'resume'])->name('rental-sessions.resume');
    Route::post('rental-sessions/{rentalSession}/extend', [RentalSessionController::class, 'extend'])->name('rental-sessions.extend');
    Route::post('rental-sessions/{rentalSession}/end', [RentalSessionController::class, 'end'])->name('rental-sessions.end');

    // Food & Beverage
    Route::resource('food-categories', FoodCategoryController::class);
    Route::resource('food-items', FoodItemController::class);

    // Orders
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Invoices
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/usage', [ReportController::class, 'usage'])->name('usage');
        Route::get('/top-items', [ReportController::class, 'topItems'])->name('top-items');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });
});

require __DIR__.'/auth.php';
