<?php

namespace App\Http\Controllers;

use App\Models\Professional;

class NursingController extends Controller
{
    public function index()
    {
        return view('nursing.index', [
            'professionals' => Professional::query()->whereIn('position', ['Enfermeiro', 'Técnico de Enfermagem'])->orderBy('name')->get(),
        ]);
    }
}
