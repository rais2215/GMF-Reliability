<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PilotController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


// Route Test Database Connection
Route::get('/test-db', function () {
    try {
        // Cek apakah tabel ada
        if(Schema::hasTable('tbl_monthlyfhfc')) {
            // Hitung jumlah record
            $count = DB::table('tbl_monthlyfhfc')->count();
            
            if($count > 0) {
                $results = DB::table('tbl_monthlyfhfc')->first();
                return "Koneksi database berhasil! Jumlah data: " . $count . " Sample data: " . json_encode($results);
            } else {
                return "Tabel exists tapi tidak ada data (kosong)";
            }
        } else {
            return "Tabel 'tbl_monthlyfhfc' tidak ditemukan";
        }
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



/* Route User Setting for Admin */
Route::get('/user-setting', [UserController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('user-setting');

Route::get('/users/create', [UserController::class, 'create'])
    ->middleware(['auth', 'verified']);

Route::post('/users', [UserController::class, 'store'])
    ->middleware(['auth', 'verified']);

Route::get('/users/{user:id}', [UserController::class, 'show'])
    ->middleware(['auth', 'verified']);

Route::get('/users/{user:id}/edit', [UserController::class, 'edit'])
    ->middleware(['auth', 'verified']);

Route::put('/users/{user:id}', [UserController::class, 'update'])
    ->middleware(['auth', 'verified']);

Route::delete('/users/{user:id}', [UserController::class, 'destroy'])
    ->middleware(['auth', 'verified']);



/* Route Authentication User */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';



/* Routes Modul pada Report */
Route::get('/report', function () {
    return view('report');})
    ->middleware(['auth', 'verified'])->name('report');

Route::get('/get-aircraft-types', [ReportController::class, 'getAircraftTypes'])->name('get.aircraft.types');

Route::get('/report/aos', [ReportController::class, 'aosIndex']) // Aircraft Operation Summary - Button Filter
    ->name('report.aos.index')->middleware(['auth', 'verified']);

Route::post('/report/aos', [ReportController::class, 'aosStore']) // Aircraft Operation Summary - Display Datas
    ->name('report.aos.store')->middleware(['auth', 'verified']);

Route::post('/report/aos/pdf', [ReportController::class, 'aosPdf']) // AOS Export PDF
    ->name('report.aos.export.pdf')->middleware(['auth', 'verified']);
    
Route::get('/report/pilot', [PilotController::class, 'pilotIndex']) //Pilot Report And Technical Delay - button filter
    ->name('report.pilot.index')->middleware(['auth', 'verified']);
    
Route::post('/report/pilot', [PilotController::class, 'pilotStore']) //Pilot Report And Technical Delay - button filter
    ->name('report.pilot.store')->middleware(['auth', 'verified']);

Route::post('/report/pilot/pdf', [PilotController::class, 'pilotPdf']) // AOS Export PDF
    ->name('report.pilot.export.pdf')->middleware(['auth', 'verified']);

Route::get('/report/cumulative', [ReportController::class, 'cumulativeContent'])
    ->name('report.cumulative')
    ->middleware(['auth', 'verified']);

/* Routes Modul Export PDF */


