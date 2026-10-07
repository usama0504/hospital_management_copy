<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrashController extends Controller
{
    private const TYPES = [
        'patients' => Patient::class,
        'appointments' => Appointment::class,
        'bills' => Bill::class,
        'prescriptions' => Prescription::class,
    ];

    public function index(Request $request)
    {
        $type = $request->input('type', 'patients');
        abort_unless(isset(self::TYPES[$type]), 404);

        $model = self::TYPES[$type];

        $items = $model::onlyTrashed()
            ->when($type !== 'patients', fn ($q) => $q->with(['patient', 'doctor']))
            ->latest('deleted_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($record) => $this->present($type, $record));

        $counts = [];
        foreach (self::TYPES as $key => $class) {
            $counts[$key] = $class::onlyTrashed()->count();
        }

        return Inertia::render('Trash/Index', [
            'type' => $type,
            'items' => $items,
            'counts' => $counts,
        ]);
    }

    public function restore(string $type, int $id)
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        $record = self::TYPES[$type]::onlyTrashed()->findOrFail($id);
        $record->restore();

        return back()->with('success', 'Record restored successfully.');
    }

    private function present(string $type, $record): array
    {
        $patient = $record->patient?->name ?? 'Unknown patient';
        $doctor = $record->doctor?->name ? 'Dr. ' . $record->doctor->name : null;

        [$label, $sub] = match ($type) {
            'patients' => [$record->name, trim($record->email . ' · ' . $record->phone, ' ·')],
            'appointments' => [$patient, collect([$doctor, (string) $record->appointment_date, $record->status])->filter()->implode(' · ')],
            'bills' => [$patient, collect([$record->amount ? 'Amount ' . $record->amount : null, $record->status, $record->bill_date?->format('d M Y')])->filter()->implode(' · ')],
            'prescriptions' => [$patient, collect([$doctor, $record->prescribed_date?->format('d M Y')])->filter()->implode(' · ')],
        };

        return [
            'id' => $record->id,
            'label' => $label,
            'sub' => $sub,
            'deleted_at' => $record->deleted_at,
        ];
    }
}
