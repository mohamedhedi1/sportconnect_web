<?php

namespace App\Controller;


use App\Entity\Partenaire;
use App\Form\PartenaireType;
use MercurySeries\FlashyBundle\FlashyNotifier;
use App\Repository\PartenaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;



/**
 * @Route("/partenaire")
 */
class PartenaireController extends AbstractController
{
    private static $nb;


    /**
     * CentreController constructor.
     */
    public function __construct()
    {
        self::setNb(0);
    }

    /**
     * @return int
     */
    public static function getNb(): ?int
    {
        return self::$nb;
    }

    /**
     * @param int $nb
     */
    public static function setNb(?int $nb): void
    {
        self::$nb = $nb;
    }


    /**
     * @Route("/", name="app_partenaire", methods={"GET"})
     */
    public function index(PartenaireRepository $partenaireRepository): Response
    { 
      
        $nombre = $partenaireRepository->nb();
        return $this->render('partenaire/index.html.twig', [
            'partenaires' => $partenaireRepository->findAll(),
            'nombre' =>$nombre,
          
       
        ]);
    }

    /**
     * @Route("/new", name="app_partenaire_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get("image")->getData();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            $file->move(
            $this->getParameter('$uploads'),
            $fileName);
            $partenaire->setImage($fileName);
            //$partenaireRepository->add($partenaire);
            $em=$this->getDoctrine()->getManager();
            $em->persist($partenaire);
            $em->flush();
            self::setNb(1);
            // $s= self::getNb();
            // echo "<script type='text/javascript'>alert('$s');</script>";
           //  $flashy->success('l\'ajout d\'un centre est effectué !','http://127.0.0.1:8000/partenaire/');

            return $this->redirectToRoute('app_partenaire', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('partenaire/new.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_partenaire_show", methods={"GET"})
     */
    public function show(Partenaire $partenaire): Response
    {
        return $this->render('home/showp.html.twig', [
            'partenaire' => $partenaire,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_partenaire_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Partenaire $partenaire, PartenaireRepository $partenaireRepository,$id): Response
    {

        $partenaire=$partenaireRepository->find($id);
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);
        $file = $request->files->get('image');
        if ($form->isSubmitted() && $form->isValid()) {
         //   $partenaireRepository->add($partenaire);
         if (!empty($file)){
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('$uploads'),
                $fileName
            );
            $partenaire->setimage($fileName);
        }
            $em=$this->getDoctrine()->getManager();
            $em->flush();

         
            return $this->redirectToRoute('app_partenaire', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('partenaire/edit.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_partenaire_delete", methods={"POST"})
     */
    public function delete(Request $request, Partenaire $partenaire, PartenaireRepository $partenaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$partenaire->getId(), $request->request->get('_token'))) {
            $partenaireRepository->remove($partenaire);
        }

        return $this->redirectToRoute('app_partenaire', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

   /**
     * @Route("partenaire/TrierParDateDESC", name="Trier")
     */
    public function TrierParNom(PartenaireRepository $repository ): Response
    {
        $partenaire = $repository->tri();
        $nombre = $repository->nb();

        return $this->render('partenaire/index.html.twig', [
            'partenaires' => $partenaire,
            'nombre' => $nombre,
        ]);
    }

    /**
     * @Route("/{nom}/search", name="search")
     */
    public function ser(PartenaireRepository $repository ,$nom): Response
    {
        $partenaire = $repository->search($nom);

        return $this->render('partenaire/index.html.twig', [
            'search' => $partenaire,
        ]);
    }


   /**
     * @Route("partenaire/count", name="count")
     */
    public function count(PartenaireRepository $repository ): Response
    {
        $nombre = $repository->nb();

        return $this->render('home/index.html.twig', [
            'nombre' => $nombre,
        ]);
    }




   /**
     * @Route("partenaire/stat", name="stat")
     */
    
    public function s(PartenaireRepository $repository ): Response
    {
        $n=$repository->stat();

        return $this->render('partenaire/index.html.twig', [
            'no' => $n,
        ]);
    }

    /**
     * @Route("/map", name="map")
     */

    public function mapaa(PartenaireRepository $repository) : Response
    {  
        $all = $repository->findAll();
        return $this->render('partenaire/mapa.html.twig', [
            'partenaires' => $all,
        ]);

    }


}
