<?php

namespace App\Services;

use App\Models\Prescription;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;

/**
 * Prescription ka PDF banata hai (dompdf se).
 * Install: composer require dompdf/dompdf
 */
class PrescriptionPdf
{
    public static function reference(Prescription $prescription): string
    {
        return 'RX-' . str_pad((string) $prescription->id, 5, '0', STR_PAD_LEFT);
    }

    /** PDF ka raw content (string). */
    public function output(Prescription $prescription): string
    {
        $prescription->loadMissing('patient', 'doctor.department', 'items');

        $html = view('pdf.prescription', [
            'prescription' => $prescription,
            'reference' => self::reference($prescription),
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false); // bahar se images/CSS load nahi hongi
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    /** Browser ko download response. */
    public function download(Prescription $prescription): Response
    {
        $filename = 'prescription-' . strtolower(self::reference($prescription)) . '.pdf';

        return response($this->output($prescription), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
