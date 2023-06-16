<?php

namespace App\Controller;



use App\Form\SerieFormType;
use App\Repository\SerieRepository;
use App\Entity\Serie;
use App\Service\MailerService;
use App\Service\PdfService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;


class SerieController extends AbstractController
{
    #[Route('/listSerie', name: 'listSerie')]
    public function listSerie(SerieRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        $donnees = $repository->findAll();
        $c = $paginator->paginate($donnees, $request->query->getInt('page', 1), 5);



        return $this->render('serie/listSerie.html.twig', [
            'series' => $c,
        ]);
    }

    #[Route('/serie/{id}', name: 'serie')]
    public function serie($id, SerieRepository $repository): Response
    {
        $serie = $repository->find($id);
        return $this->render('serie/serie.html.twig', [
            'serie' => $serie,
        ]);
    }

    #[Route('/listSerieF', name: 'listSerieF')]
    public function listSerief(SerieRepository $rep): Response
    {
        $c = $rep->findAll();
        return $this->render('serie/listSerieF.html.twig', [
            'series' => $c
        ]);
    }

    /* formulaire stat */
    #[Route('/contact_submit', name: 'contact_submit')]
    public function contactSubmit(Request $request, SerieRepository $rep, ManagerRegistry $doctrine)
    {
        $c = $rep->findAll();
        $name = $request->request->get('name');
        $idSerie = $request->request->get('idSerie');
        $serie = $rep->find($idSerie);
        $valeur = $serie->getValeur();
        $v = $valeur + $name;
        $serie->setValeur($v);
        $em = $doctrine->getManager();
        $em->persist($serie);
        $em->flush();



        // Traitement de la valeur $name
        echo "le serie: " . $idSerie;

        echo "Le valur est  : " . $name;



        return $this->render('serie/listSerieF.html.twig', [
            'series' => $c
        ]);

        // ... traitement du formulaire

        return $this->render('serie/listSerie.html.twig');
    }



    #[Route('/addSerie', name: 'addSerie')]
    public function addSerie(MailerService $mailer, ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $serie = new  Serie();
        $form = $this->createForm(SerieFormType::class, $serie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('serie_image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $serie->setImageSerie($newFilename);
            }
            $serie->setValeur(0);

            $em = $doctrine->getManager();
            $em->persist($serie);
            $em->flush();
            $mailer->sendEmail();

            return $this->redirectToRoute("listSerie");
        }
        return $this->renderForm("serie/addSerie.html.twig", array("f" => $form));
    }


    #[Route('/deleteSerie/{id}', name: 'deleteSerie')]
    public function deleteSerie($id, SerieRepository $repository, ManagerRegistry $doctrine): Response
    {
        $equipement = $repository->find($id);

        $em = $doctrine->getManager();
        $em->remove($equipement);
        $em->flush();
        return $this->redirectToRoute("listSerie");
    }

    #[Route('/updateSerie/{id}', name: 'updateSerie')]
    public function updateSerie($id, SerieRepository $repository, ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $equipement = $repository->find($id);
        $form = $this->createForm(SerieFormType::class, $equipement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('serie_image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $equipement->setImageSerie($newFilename);
            }
            $em = $doctrine->getManager();
            $em->flush();
            $this->redirectToRoute("listSerie");
        }

        return $this->renderForm("serie/updateSerie.html.twig", array("f" => $form));
    }

    /*  #[Route('/pdf', name: 'serie.pdf')]
    public function generatedPdfSerie(Serie $serie = null, PdfService $pdf)
    {
        $html = $this->render('serie/serie.html.twig', array('serie' => $serie));
        $pdf->showPdfFile($html);
    } */

    #[Route('/pdf', name: 'serie.pdf')]
    public function generatedPdfSerie(SerieRepository $rep, PdfService $pdf)
    {
        $c = $rep->findAll();
        $html = $this->render('serie/seriePdf.html.twig', [
            'series' => $c,
        ]);
        $pdfContent = $pdf->showPdfFile($html);

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="filename.pdf"');

        return $response;
    }

    #[Route('/statistic', name: 'statistic')]
    public function chartAction()
    {
        $series = $this->getDoctrine()->getRepository(Serie::class)->findAll();
        $data = array();

        foreach ($series as $serie) {

            $nomSerie = $serie->getTitreSerie();
            $valeur = $serie->getValeur();
            if ($valeur >= 0)
                $data[$nomSerie] = $valeur;
        }

        return $this->render('serie/stat.html.twig', array(
            'data' => $data
        ));
    }
}
