<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConsoleController;
use App\Http\Controllers\ConsoleTypeController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\RentalSessionController;
use App\Http\Controllers\FoodCategoryController;
use App\Http\Controllers\FoodItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Console Types
    Route::resource('console-types', ConsoleTypeController::class);

    // Consoles
    Route::resource('consoles', ConsoleController::class);

    // Packages
    Route::resource('packages', PackageController::class);

    // Rental Sessions
    Route::resource('rental-sessions', RentalSessionController::class);
    Route::post('rental-sessions/{rentalSession}/pause', [RentalSessionController::class, 'pause'])->name('rental-sessions.pause');
    Route::post('rental-sessions/{rentalSession}/resume', [RentalSessionController::class, 'resume'])->name('rental-sessions.resume');
    Route::post('rental-sessions/{rentalSession}/extend', [RentalSessionController::class, 'extend'])->name('rental-sessions.extend');
    Route::post('rental-sessions/{rentalSession}/end', [RentalSessionController::class, 'end'])->name('rental-sessions.end');
    Route::post('rental-sessions/{rentalSession}/mark-paid', [RentalSessionController::class, 'markAsPaid'])->name('rental-sessions.mark-paid');
    Route::get('rental-sessions/{rentalSession}/print-receipt', [RentalSessionController::class, 'printReceipt'])->name('rental-sessions.print-receipt');

    // Food Categories
    Route::resource('food-categories', FoodCategoryController::class);

    // Food Items
    Route::resource('food-items', FoodItemController::class);

    // Orders
    Route::resource('orders', OrderController::class);
    Route::post('orders/{order}/mark-paid', [OrderController::class, 'markAsPaid'])->name('orders.mark-paid');
    Route::get('orders/{order}/print-receipt', [OrderController::class, 'printReceipt'])->name('orders.print-receipt');

    // Invoices
    Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');

    // Customers
    Route::resource('customers', CustomerController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/usage', [ReportController::class, 'usage'])->name('reports.usage');
    Route::get('/reports/top-items', [ReportController::class, 'topItems'])->name('reports.top-items');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});

require __DIR__.'/auth.php';
