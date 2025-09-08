<?php
require_once __DIR__ . '/../../../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

function generate_pdf($html, $filename = 'document.pdf') {
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4');
    $dompdf->render();
    
    $dompdf->stream($filename, ['Attachment' => false]);
}
