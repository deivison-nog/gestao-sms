<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NursingController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\SupportTicketResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('menu:dashboard')->name('dashboard.index');

    Route::resource('roles', RolePermissionController::class)->only(['index', 'update'])->middleware('menu:admin_permissoes');

    Route::resource('professionals', ProfessionalController::class)->except(['show'])->middleware('menu:profissionais');

    Route::get('/nursing', [NursingController::class, 'index'])->middleware('menu:enfermagem')->name('nursing.index');

    Route::get('/schedules', [ScheduleController::class, 'index'])->middleware('menu:cronograma')->name('schedules.index');
    Route::post('/schedules', [ScheduleController::class, 'store'])->middleware('menu:cronograma')->name('schedules.store');
    Route::get('/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

    Route::get('/attendance', [AttendanceController::class, 'index'])->middleware('menu:frequencia')->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->middleware('menu:frequencia')->name('attendance.store');
    Route::patch('/attendance/professionals/{professional}', [AttendanceController::class, 'updateProfessionalStatus'])->middleware('menu:frequencia')->name('attendance.professionals.update');

    Route::resource('support-tickets', SupportTicketController::class)->except(['show', 'create', 'edit'])->middleware('menu:suporte');
    Route::post('/support-tickets/{supportTicket}/responses', [SupportTicketResponseController::class, 'store'])->middleware('menu:suporte')->name('support-tickets.responses.store');
});
