<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    private const SUBJECTS = [
        'Patient' => Patient::class,
        'Appointment' => Appointment::class,
        'Bill' => Bill::class,
        'Prescription' => Prescription::class,
    ];

    public function index(Request $request)
    {
        $subject = $request->input('subject');
        $event = $request->input('event');

        $logs = Activity::query()
            ->with('causer')
            ->when($subject && isset(self::SUBJECTS[$subject]), fn ($q) => $q->where('subject_type', self::SUBJECTS[$subject]))
            ->when($event, fn ($q) => $q->where('event', $event))
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(function ($activity) {
                $changes = $activity->attribute_changes ?? $activity->properties;
                $changes = $changes instanceof Arrayable ? $changes->toArray() : (array) $changes;

                return [
                    'id' => $activity->id,
                    'event' => $activity->event ?? $activity->description,
                    'subject' => class_basename((string) $activity->subject_type),
                    'subject_id' => $activity->subject_id,
                    'user' => $activity->causer?->name ?? 'System',
                    'changes' => $changes,
                    'created_at' => $activity->created_at,
                ];
            });

        return Inertia::render('ActivityLog/Index', [
            'logs' => $logs,
            'filters' => ['subject' => $subject, 'event' => $event],
            'subjects' => array_keys(self::SUBJECTS),
        ]);
    }
}
