<?php

namespace App\Http\Controllers;

use App\Models\Biometric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BiometricController extends Controller
{
  
   public function index()
{
    $user = Auth::user();

    $measurements = Biometric::where('user_id', $user->id)
        ->orderByDesc('measurement_date')
        ->get();

  
    $measurementsForChart = $measurements
        ->filter(fn ($m) => !is_null($m->weight))
        ->sortBy('measurement_date');

    $weightDates = $measurementsForChart
        ->pluck('measurement_date')
        ->map(function ($date) {
            // measurement_date 如果是 Carbon 就直接 format
            return $date instanceof Carbon
                ? $date->format('Y-m-d')
                : (string) $date;
        })
        ->values()
        ->all();

    $weightValues = $measurementsForChart
        ->pluck('weight')
        ->values()
        ->all();

    return view('biometrics.index', [
        'measurements'  => $measurements,
        'weightDates'   => $weightDates,
        'weightValues'  => $weightValues,
    ]);
}

    public function create()
    {
        return view('biometrics.create', [
            'defaultDate' => now()->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'measurement_date' => ['required', 'date'],
            'weight'          => ['nullable', 'numeric', 'min:0'],
            'systolic_bp'     => ['nullable', 'integer', 'min:0'],
            'diastolic_bp'    => ['nullable', 'integer', 'min:0'],
            'bmi'             => ['nullable', 'numeric', 'min:0'],
            'notes'           => ['nullable', 'string'],
        ]);

        $data['user_id'] = Auth::id();

        Biometric::create($data);

        return redirect()
            ->route('biometrics.index')
            ->with('success', 'Measurement added.');
    }

  
    public function edit(Biometric $biometric)
    {
        if ($biometric->user_id !== Auth::id()) {
            abort(403);
        }

        return view('biometrics.edit', [
            'biometric' => $biometric,
        ]);
    }

 
    public function update(Request $request, Biometric $biometric)
    {
        if ($biometric->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'measurement_date' => ['required', 'date'],
            'weight'          => ['nullable', 'numeric', 'min:0'],
            'systolic_bp'     => ['nullable', 'integer', 'min:0'],
            'diastolic_bp'    => ['nullable', 'integer', 'min:0'],
            'bmi'             => ['nullable', 'numeric', 'min:0'],
            'notes'           => ['nullable', 'string'],
        ]);

        $biometric->update($data);

        return redirect()
            ->route('biometrics.index')
            ->with('success', 'Measurement updated.');
    }

   
    public function destroy(Biometric $biometric)
    {
        if ($biometric->user_id !== Auth::id()) {
            abort(403);
        }

        $biometric->delete();

        return redirect()
            ->route('biometrics.index')
            ->with('success', 'Measurement deleted.');
    }
}