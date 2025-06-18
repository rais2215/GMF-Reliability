<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PilotController;
use App\Http\Controllers\CombinedReportController;

/*
|--------------------------------------------------------------------------
| Utility & Test Routes
|--------------------------------------------------------------------------
*/
Route::get('/test-db', function () {
    try {
        if (Schema::hasTable('tbl_monthlyfhfc')) {
            $count = DB::table('tbl_monthlyfhfc')->count();
            $sample = DB::table('tbl_monthlyfhfc')->first();
            return "Koneksi database berhasil! Jumlah data: $count. Sample: " . json_encode($sample);
        } else {
            return "Tabel 'tbl_monthlyfhfc' tidak ditemukan.";
        }
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('welcome'));

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| User Management (Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/user-setting', [UserController::class, 'index'])->name('user-setting');
    Route::get('/users/create', [UserController::class, 'create']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user:id}', [UserController::class, 'show']);
    Route::get('/users/{user:id}/edit', [UserController::class, 'edit']);
    Route::put('/users/{user:id}', [UserController::class, 'update']);
    Route::delete('/users/{user:id}', [UserController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Profile & Photo
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/profile/photo', [UserController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [UserController::class, 'deletePhoto'])->name('profile.photo.delete');
});

/*
|--------------------------------------------------------------------------
| Report Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('report')->name('report.')->group(function () {
    Route::get('/', fn() => view('report'))->name('index');

    Route::get('/aos', [ReportController::class, 'aosIndex'])->name('aos.index');
    Route::post('/aos', [ReportController::class, 'aosStore'])->name('aos.store');
    Route::post('/aos/pdf', [ReportController::class, 'aosPdf'])->name('aos.export.pdf');
    Route::post('/aos/export-excel', [ReportController::class, 'exportExcel'])->name('aos.export.excel');

    Route::get('/pilot', [PilotController::class, 'pilotIndex'])->name('pilot.index');
    Route::post('/pilot', [PilotController::class, 'pilotStore'])->name('pilot.store');
    Route::post('/pilot/pdf', [PilotController::class, 'pilotPdf'])->name('pilot.export.pdf');
    Route::post('/pilot/excel', [PilotController::class, 'pilotExcel'])->name('pilot.export.excel');

    // Combined Report Routes
    Route::get('/combined', [CombinedReportController::class, 'index'])->name('combined.index');
    Route::post('/combined', [CombinedReportController::class, 'store'])->name('combined.store');
    Route::post('/combined/pdf', [CombinedReportController::class, 'exportPdf'])->name('combined.export.pdf');

    Route::get('/cumulative', [ReportController::class, 'cumulativeContent'])->name('cumulative');
    Route::get('/aos-pilot', [ReportController::class, 'aosPilot'])->name('aos_pilot.index');

    Route::post('/combined-report/export-pdf', [CombinedReportController::class, 'exportPdf'])->name('combined-report.export-pdf');
});

Route::get('/get-aircraft-types', [ReportController::class, 'getAircraftTypes'])->name('get.aircraft.types');

/*
|--------------------------------------------------------------------------
| Auth Routes (from Breeze/Fortify/etc)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';