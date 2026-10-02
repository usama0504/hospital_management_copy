<?php

use App\Models\Patient;
use Spatie\Activitylog\Models\Activity;

test('creating and deleting a patient is written to the activity log with the user', function () {
    $admin = userWithRole('admin');

    $this->actingAs($admin)->post('/patients', [
        'name' => 'Log Test',
        'email' => 'log@example.com',
        'phone' => '03001112222',
        'gender' => 'Male',
    ]);

    $patient = Patient::where('email', 'log@example.com')->firstOrFail();

    $this->actingAs($admin)->delete("/patients/{$patient->id}");

    $events = Activity::where('subject_type', Patient::class)
        ->where('subject_id', $patient->id)
        ->pluck('event');

    expect($events)->toContain('created')->toContain('deleted');
    expect(Activity::where('event', 'created')->latest('id')->first()->causer_id)->toBe($admin->id);
});

test('only admin can open the activity log and the trash', function () {
    $this->actingAs(userWithRole('admin'))->get(route('activity-log.index'))->assertOk();
    $this->actingAs(userWithRole('admin'))->get(route('trash.index'))->assertOk();

    $this->actingAs(userWithRole('receptionist'))->get(route('activity-log.index'))->assertForbidden();
    $this->actingAs(userWithRole('receptionist'))->get(route('trash.index'))->assertForbidden();
});

test('an unknown trash type returns 404', function () {
    $this->actingAs(userWithRole('admin'))->get('/trash?type=users')->assertNotFound();
});
