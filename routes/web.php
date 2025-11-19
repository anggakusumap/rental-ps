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
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Console Management
    Route::resource('consoles', ConsoleController::class);
    Route::resource('console-types', ConsoleTypeController::class);
    Route::resource('packages', PackageController::class);

    // Rental Sessions
    Route::resource('rental-sessions', RentalSessionController::class)
        ->except(['edit', 'update', 'destroy']);
    Route::post('rental-sessions/{rentalSession}/pause', [RentalSessionController::class, 'pause'])
        ->name('rental-sessions.pause');
    Route::post('rental-sessions/{rentalSession}/resume', [RentalSessionController::class, 'resume'])
        ->name('rental-sessions.resume');
    Route::post('rental-sessions/{rentalSession}/extend', [RentalSessionController::class, 'extend'])
        ->name('rental-sessions.extend');
    Route::post('rental-sessions/{rentalSession}/end', [RentalSessionController::class, 'end'])
        ->name('rental-sessions.end');

    // Food & Beverage
    Route::resource('food-categories', FoodCategoryController::class);
    Route::resource('food-items', FoodItemController::class);
    Route::resource('orders', OrderController::class)->except(['edit', 'update', 'destroy']);

    // Invoices
    Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])
        ->name('invoices.mark-paid');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])
        ->name('invoices.pdf');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/usage', [ReportController::class, 'usage'])->name('usage');
        Route::get('/top-items', [ReportController::class, 'topItems'])->name('top-items');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });
});
