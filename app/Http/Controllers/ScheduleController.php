<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        return view('schedules.index', [
            'schedules' => Schedule::query()->with('professional')->orderBy('scheduled_date')->get(),
            'professionals' => Professional::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'scheduled_date' => ['required', 'date'],
            'professional_id' => ['nullable', 'exists:professionals,id'],
            'unit' => ['required', 'string', 'max:255'],
            'observation' => ['nullable', 'string'],
        ]);

        Schedule::query()->create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Escala salva com sucesso.');
    }

    public function edit(Schedule $schedule)
    {
        $this->authorizeScheduleEdit($schedule);

        return view('schedules.edit', [
            'schedule' => $schedule,
            'professionals' => Professional::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $this->authorizeScheduleEdit($schedule);

        $validated = $request->validate([
            'scheduled_date' => ['required', 'date'],
            'professional_id' => ['nullable', 'exists:professionals,id'],
            'unit' => ['required', 'string', 'max:255'],
            'observation' => ['nullable', 'string'],
        ]);

        $schedule->update($validated);

        return redirect()->route('schedules.index')->with('status', 'Escala atualizada com sucesso.');
    }

    public function destroy(Request $request, Schedule $schedule)
    {
        $this->authorizeScheduleEdit($schedule);

        if ($request->user()->hasMenuAccess('cronograma')) {
            $schedule->delete();
        } else {
            $schedule->update(['professional_id' => null]);
        }

        return back()->with('status', 'Escala atualizada com sucesso.');
    }

    private function authorizeScheduleEdit(Schedule $schedule): void
    {
        $user = request()->user();

        $isAssignedProfessional = $user->professional && $schedule->professional_id === $user->professional->id;

        abort_unless($user->hasMenuAccess('cronograma') || $isAssignedProfessional, 403);
    }
}
