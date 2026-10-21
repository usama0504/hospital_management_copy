<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Prescription {{ $reference }}</title>
    {{-- dompdf flex/grid support nahi karta, isliye sab kuch tables se bana hai --}}
    <style>
        @page { margin: 28px 34px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; }
        .brand { font-size: 20px; font-weight: bold; color: #111827; }
        .sub { font-size: 10px; color: #6b7280; }
        .ref { text-align: right; }
        .ref-label { font-size: 8px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; }
        .ref-value { font-size: 14px; font-weight: bold; color: #ea580c; }
        .bar { height: 4px; background: #f97316; margin-bottom: 14px; }
        .label { font-size: 8px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .name { font-size: 13px; font-weight: bold; color: #111827; }
        .muted { color: #6b7280; font-size: 10px; }
        .accent { color: #ea580c; font-size: 10px; font-weight: bold; }
        .box { border: 1px solid #e5e7eb; background: #f9fafb; padding: 10px 12px; }
        .section { margin-top: 16px; }
        .meds th { background: #f3f4f6; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: .5px; color: #6b7280; padding: 7px 8px; border-bottom: 1px solid #e5e7eb; }
        .meds td { padding: 8px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .meds .med { font-weight: bold; color: #111827; }
        .sign { margin-top: 48px; }
        .sign-line { border-top: 1px solid #9ca3af; width: 190px; padding-top: 4px; text-align: center; font-size: 10px; color: #6b7280; }
        .footer { margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 8px; text-align: center; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="bar"></div>

    <table>
        <tr>
            <td>
                <div class="brand">Health Care</div>
                <div class="sub">Official Medical Prescription</div>
            </td>
            <td class="ref">
                <div class="ref-label">Prescription Reference</div>
                <div class="ref-value">#{{ $reference }}</div>
            </td>
        </tr>
    </table>

    <table class="section">
        <tr>
            <td class="box" style="width: 38%;">
                <div class="label">Patient</div>
                <div class="name">{{ $prescription->patient?->name ?? 'N/A' }}</div>
                <div class="muted">
                    @if($prescription->patient?->dob){{ $prescription->patient->dob->age }} yrs @endif
                    @if($prescription->patient?->gender) &middot; {{ $prescription->patient->gender }} @endif
                </div>
                <div class="muted">Phone: {{ $prescription->patient?->phone ?? 'N/A' }}</div>
            </td>
            <td style="width: 2%;"></td>
            <td class="box" style="width: 38%;">
                <div class="label">Prescribed By</div>
                <div class="name">Dr. {{ $prescription->doctor?->name ?? 'N/A' }}</div>
                <div class="accent">{{ $prescription->doctor?->specialization ?? 'Medical Specialist' }}</div>
                @if($prescription->doctor?->department)
                    <div class="muted">{{ $prescription->doctor->department->name }}</div>
                @endif
            </td>
            <td style="width: 2%;"></td>
            <td class="box" style="width: 20%;">
                <div class="label">Date</div>
                <div class="name" style="font-size: 11px;">{{ $prescription->prescribed_date?->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="label">Diagnosis</div>
        <div class="box">{{ $prescription->diagnosis }}</div>
    </div>

    <div class="section">
        <div class="label">Prescribed Medicines (Rx)</div>
        <table class="meds">
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 28%;">Medicine</th>
                    <th style="width: 14%;">Dosage</th>
                    <th style="width: 14%;">Frequency</th>
                    <th style="width: 14%;">Duration</th>
                    <th>Instructions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prescription->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="med">{{ $item->medicine_name }}</td>
                        <td>{{ $item->dosage ?: '-' }}</td>
                        <td>{{ $item->frequency ?: '-' }}</td>
                        <td>{{ $item->duration ?: '-' }}</td>
                        <td>{{ $item->instructions ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="muted">No medicines listed.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($prescription->notes)
        <div class="section">
            <div class="label">Doctor's Notes</div>
            <div class="box">{!! nl2br(e($prescription->notes)) !!}</div>
        </div>
    @endif

    <table class="sign">
        <tr>
            <td></td>
            <td style="width: 200px;">
                <div class="sign-line">Dr. {{ $prescription->doctor?->name ?? '' }}</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        This is a computer-generated prescription &middot; Generated on {{ now()->format('d M Y, h:i A') }}
    </div>
</body>
</html>
