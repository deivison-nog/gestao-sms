<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Professional;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->string('month')->toString() ?: now()->format('Y-m');

        return view('attendance.index', [
            'month' => $month,
            'records' => AttendanceRecord::query()
                ->with('professional')
                ->where('month', "{$month}-01")
                ->orderBy('professional_id')
                ->get(),
            'professionals' => Professional::query()->where('is_frequency_enabled', true)->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'professional_id' => ['required', 'exists:professionals,id'],
            'month' => ['required', 'date_format:Y-m'],
            'worked_days' => ['required', 'integer', 'min:0', 'max:31'],
            'justified_absences' => ['required', 'integer', 'min:0', 'max:31'],
            'unjustified_absences' => ['required', 'integer', 'min:0', 'max:31'],
            'observations' => ['nullable', 'string'],
        ]);

        AttendanceRecord::query()->updateOrCreate(
            ['professional_id' => $validated['professional_id'], 'month' => $validated['month'].'-01'],
            [
                'worked_days' => $validated['worked_days'],
                'justified_absences' => $validated['justified_absences'],
                'unjustified_absences' => $validated['unjustified_absences'],
                'observations' => $validated['observations'] ?? null,
            ]
        );

        return back()->with('status', 'Frequência lançada com sucesso.');
    }

    public function updateProfessionalStatus(Request $request, Professional $professional)
    {
        $professional->update(['is_frequency_enabled' => $request->boolean('is_frequency_enabled')]);

        return back()->with('status', 'Visibilidade na frequência atualizada.');
    }
}
