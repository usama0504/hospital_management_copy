<x-mail::message>
# Hello {{ $name }},

{!! nl2br(e($replyBody)) !!}

<x-mail::panel>
**Your original message:**<br>
{!! nl2br(e($originalMessage)) !!}
</x-mail::panel>

Thanks,<br>
{{ $clinic }}
</x-mail::message>
