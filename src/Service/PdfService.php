<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private $domPdf;

    public function __construct()
    {
        $this->domPdf = new Dompdf();
        $pdfOptions = new Options();

        // Configuration de la police par défaut
        $pdfOptions->set('defaultFont', 'Arial');

        // Configuration de la taille et des marges de la page
        $pdfOptions->set('pageWidth', '210mm');
        $pdfOptions->set('pageHeight', '297mm');
        $pdfOptions->set('marginTop', '20mm');
        $pdfOptions->set('marginBottom', '20mm');
        $pdfOptions->set('marginLeft', '20mm');
        $pdfOptions->set('marginRight', '20mm');

        $this->domPdf->setOptions($pdfOptions);
    }

    /*  public function showPdfFile($html)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream("details.pdf", ['Attachment' => false]);
    }*/
    public function showPdfFile(string $html)
    {
        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->render();

        $response = new Response();
        $response->setContent($pdf->output());
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    public function generateBinaryPDF($html)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }
}
