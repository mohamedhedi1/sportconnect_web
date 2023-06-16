<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Entity\Exercice;
use App\Entity\Serie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;


class PdfController extends AbstractController
{

    #[Route("/pdf/equipements", name: "pdf_equipements")]
    public function pdf_equipements()
    {
        // Récupérer la liste des utilisateurs depuis la base de données
        $equipements = $this->getDoctrine()->getRepository(Equipement::class)->findAll();

        // Générer le contenu du PDF avec la liste des utilisateurs
        $html = $this->renderView('pdf/equipements.html.twig', [
            'equipements' => $equipements,
        ]);


        // Récupérer l'heure actuelle
        $date = new \DateTime();
        $heure = $date->format('d/m/Y H:i:s');

        // Créer une nouvelle instance de TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Définir les propriétés du document PDF
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Mon application');
        $pdf->SetTitle('Liste des equipements');
        $pdf->SetSubject('Liste des equipements');
        $pdf->SetKeywords('Liste, equipements');



        // Ajouter une page au document PDF
        $pdf->AddPage();



        // Écrire le contenu HTML dans le document PDF
        $pdf->writeHTML($html, true, false, true, false, '');



        // Ajouter l'heure en bas de la dernière page
        $pdf->SetY(260);
        $pdf->SetFont('helvetica', 'I', 12);
        $pdf->Cell(0, 10, 'Date et heure de création : ' . $heure, 0, false, 'C', 0, '', 0, false, 'T', 'M');
        // Générer le fichier PDF et l'envoyer au navigateur
        return new Response($pdf->Output('Liste des equipements.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Liste des equipements.pdf"',
        ]);
    }

    #[Route("/pdf/exercices", name: "pdf_exercices")]
    public function pdf_exercices()
    {
        // Récupérer la liste des utilisateurs depuis la base de données
        $exercices = $this->getDoctrine()->getRepository(Exercice::class)->findAll();

        // Générer le contenu du PDF avec la liste des utilisateurs
        $html = $this->renderView('pdf/exercices.html.twig', [
            'exercices' => $exercices,
        ]);


        // Récupérer l'heure actuelle
        $date = new \DateTime();
        $heure = $date->format('d/m/Y H:i:s');

        // Créer une nouvelle instance de TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Définir les propriétés du document PDF
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Mon application');
        $pdf->SetTitle('Liste des exercices');
        $pdf->SetSubject('Liste des exercices');
        $pdf->SetKeywords('Liste, exercices');



        // Ajouter une page au document PDF
        $pdf->AddPage();



        // Écrire le contenu HTML dans le document PDF
        $pdf->writeHTML($html, true, false, true, false, '');



        // Ajouter l'heure en bas de la dernière page
        $pdf->SetY(260);
        $pdf->SetFont('helvetica', 'I', 12);
        $pdf->Cell(0, 10, 'Date et heure de création : ' . $heure, 0, false, 'C', 0, '', 0, false, 'T', 'M');
        // Générer le fichier PDF et l'envoyer au navigateur
        return new Response($pdf->Output('Liste des exercices.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Liste des exercices.pdf"',
        ]);
    }

    #[Route("/pdf/series", name: "pdf_series")]
    public function pdf_series()
    {

        $series = $this->getDoctrine()->getRepository(Serie::class)->findAll();


        $html = $this->renderView('pdf/series.html.twig', [
            'series' => $series,
        ]);


        // Récupérer l'heure actuelle
        $date = new \DateTime();
        $heure = $date->format('d/m/Y H:i:s');

        // Créer une nouvelle instance de TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Définir les propriétés du document PDF
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Mon application');
        $pdf->SetTitle('Liste series');
        $pdf->SetSubject('Liste series');
        $pdf->SetKeywords('Liste, series');



        // Ajouter une page au document PDF
        $pdf->AddPage();



        // Écrire le contenu HTML dans le document PDF
        $pdf->writeHTML($html, true, false, true, false, '');



        // Ajouter l'heure en bas de la dernière page
        $pdf->SetY(260);
        $pdf->SetFont('helvetica', 'I', 12);
        $pdf->Cell(0, 10, 'Date et heure de création : ' . $heure, 0, false, 'C', 0, '', 0, false, 'T', 'M');
        // Générer le fichier PDF et l'envoyer au navigateur
        return new Response($pdf->Output('Liste des series.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Liste des series.pdf"',
        ]);
    }
}
