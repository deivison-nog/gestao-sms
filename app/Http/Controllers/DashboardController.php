<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Professional;
use App\Models\Schedule;
use App\Models\SupportTicket;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'totalProfessionals' => Professional::count(),
            'activeProfessionals' => Professional::query()->where('is_active', true)->count(),
            'ticketsByStatus' => SupportTicket::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'nextSchedules' => Schedule::query()->with('professional')->whereDate('scheduled_date', '>=', now()->toDateString())->orderBy('scheduled_date')->limit(5)->get(),
            'attendanceSummary' => AttendanceRecord::query()->where('month', now()->startOfMonth()->toDateString())->selectRaw('sum(worked_days) as worked_days, sum(justified_absences) as justified_absences, sum(unjustified_absences) as unjustified_absences')->first(),
        ]);
    }
}
