<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\FarmerProductionController;
use App\Http\Controllers\FertilizerController;
use App\Http\Controllers\FertilizerRequestController;
use App\Http\Controllers\FertilizerDistributionController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\ImportOrderController;
use App\Http\Controllers\ExportOrderController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\NotificationController;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Farmer & Farm Management Routes
    Route::resource('farmers', FarmerController::class);
    Route::resource('farms', FarmController::class)->only(['store', 'update', 'destroy']);
    Route::resource('farmer-production', FarmerProductionController::class)->only(['store', 'update', 'destroy']);

    // Fertilizer Automation Module Routes
    Route::resource('fertilizers', FertilizerController::class);
    Route::resource('fertilizer-requests', FertilizerRequestController::class);
    Route::patch('/fertilizer-requests/{fertilizerRequest}/status', [FertilizerRequestController::class, 'updateStatus'])->name('fertilizer-requests.update-status');
    Route::resource('fertilizer-distributions', FertilizerDistributionController::class)->only(['index', 'store']);

    // Inventory & Supplier Management Routes
    Route::resource('suppliers', SupplierController::class);
    Route::resource('inventory', InventoryController::class);
    Route::post('/inventory/{inventory}/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
    Route::post('/inventory/{inventory}/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out');
    Route::post('/inventory/{inventory}/adjust', [InventoryController::class, 'adjustStock'])->name('inventory.adjust');
    Route::get('/inventory-transactions', [InventoryController::class, 'transactions'])->name('inventory-transactions.index');

    // HR & Payroll Module Routes
    Route::resource('departments', DepartmentController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('payroll', PayrollController::class);
    Route::get('/payslips/{payroll}', [PayrollController::class, 'payslip'])->name('payroll.payslip');

    // Sales, Imports & Exports Routes
    Route::resource('customers', CustomerController::class);
    Route::resource('sales-orders', SalesOrderController::class);
    Route::patch('/sales-orders/{salesOrder}/status', [SalesOrderController::class, 'updateStatus'])->name('sales-orders.update-status');
    Route::resource('import-orders', ImportOrderController::class);
    Route::patch('/import-orders/{importOrder}/status', [ImportOrderController::class, 'updateStatus'])->name('import-orders.update-status');
    Route::resource('export-orders', ExportOrderController::class);
    Route::patch('/export-orders/{exportOrder}/status', [ExportOrderController::class, 'updateStatus'])->name('export-orders.update-status');

    // Sugarcane Farmers & ERP Analytics Routes
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // System Reports, Audit Logs & Notifications Routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Admin & Management Only: User Management
    Route::middleware(['role:admin,management,System Administrator'])->group(function () {
        Route::resource('users', UserController::class);
    });
});
