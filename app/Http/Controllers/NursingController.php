<?php

namespace App\Http\Controllers;

use App\Models\Professional;

class NursingController extends Controller
{
    public function index()
    {
        return view('nursing.index', [
            'professionals' => Professional::query()
                ->with('position')
                ->whereHas('position', function ($q) {
                    $q->whereIn('name', ['Enfermeiro', 'Técnico de Enfermagem']);
                })
                ->orWhere(function ($q) {
                    // backwards compat: also match old text `position` column while it still exists
                    $q->whereNull('position_id')
                      ->whereIn('position', ['Enfermeiro', 'Técnico de Enfermagem']);
                })
                ->orderBy('name')
                ->get(),
        ]);
    }
}
