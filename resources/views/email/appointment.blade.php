<x-mail::message>
# Hello {{ $patientName }},

@if ($event === 'received')
We have received your appointment request. Our team will contact you shortly to confirm it.
@elseif ($event === 'confirmed')
Your appointment is confirmed.
@elseif ($event === 'rescheduled')
Your appointment time has been changed. Please note the new time below.
@elseif ($event === 'cancelled')
Your appointment has been cancelled. If this is a mistake, please contact us to book a new time.
@endif

<x-mail::panel>
**Date & time:** {{ $when }}
@if ($doctorName)

**Doctor:** Dr. {{ $doctorName }}
@endif
</x-mail::panel>

@if ($event !== 'cancelled')
Please arrive a few minutes early. If you can't make it, let us know so we can offer the slot to someone else.
@endif

Thanks,<br>
{{ $clinic }}
</x-mail::message>
