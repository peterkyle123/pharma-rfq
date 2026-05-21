<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\RfqTracker;
use App\Http\Controllers\RfqController;
use App\Http\Controllers\AgencyController;
Route::get('/', function () {
    return redirect()->route('rfqs.index');
});

Route::get('/rfqs',            RfqTracker::class)->name('rfqs.index');
Route::get('/rfqs/create',     [RfqController::class, 'create'])->name('rfqs.create');
Route::post('/rfqs',           [RfqController::class, 'store'])->name('rfqs.store');
Route::get('/rfqs/{rfq}',      [RfqController::class, 'show'])->name('rfqs.show');
Route::get('/rfqs/{rfq}/edit', [RfqController::class, 'edit'])->name('rfqs.edit');
Route::put('/rfqs/{rfq}',      [RfqController::class, 'update'])->name('rfqs.update');
Route::delete('/rfqs/{rfq}',   [RfqController::class, 'destroy'])->name('rfqs.destroy');


Route::get('/agencies',             [AgencyController::class, 'index'])->name('agencies.index');
Route::get('/agencies/create',      [AgencyController::class, 'create'])->name('agencies.create');
Route::get('/agencies/{agency}/edit',[AgencyController::class, 'edit'])->name('agencies.edit');