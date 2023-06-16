<?php

namespace App\Controller;


use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Symfony\Component\Serializer\SerializerInterface;
use App\Form\EntityType;
use App\Form\EquipementFormType;
use App\Entity\Equipement;
use App\Repository\EquipementRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

class EquipementController extends AbstractController
{


    /*  #[Route('/listEquipementp', name: 'listEquipementp')]
    public function listEquipementp(Request $request, EquipementRepository $rep, PaginatorInterface $paginator): Response
    {
        $c = $rep->findAll();

        $paginator = $paginator->paginate(
            $c,
            $request->query->getInt('page', 1),
            2
        );
        return $this->render('equipement/listEquipement.html.twig', [
            'equipements' => $c,
        ]);
    }*/


    #[Route('/listEquipement', name: 'listEquipement')]
    public function listEquipement(EquipementRepository $rep): Response
    {
        $c = $rep->findAll();
        return $this->render('equipement/listEquipement.html.twig', [
            'equipements' => $c,
        ]);
    }
    #[Route('/equipements/{page?1}/{nbre?10}', name: 'listEquipement.all')]
    public function listEquipements($nbre, $page, EquipementRepository $rep): Response
    {
        $c = $rep->findBy([], [], $nbre, ($page - 1) * $nbre);
        return $this->render('equipement/listEquipement.html.twig', [
            'equipements' => $c,
        ]);
    }




    #[Route('/addEquipement', name: 'addEquipement')]
    public function addEquipement(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $equipement = new Equipement();
        $form = $this->createForm(EquipementFormType::class, $equipement);
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
                        $this->getParameter('equipement_image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $equipement->setImageEquipement($newFilename);
            }

            $em = $doctrine->getManager();
            $em->persist($equipement);
            $em->flush();
            return $this->redirectToRoute("listEquipement");
        }
        return $this->renderForm("equipement/addEquipement.html.twig", array("f" => $form));
    }




    #[Route('/deleteEquipement/{id}', name: 'deleteEquipement')]
    public function deleteEquipement($id, EquipementRepository $repository, ManagerRegistry $doctrine): Response
    {
        $equipement = $repository->find($id);

        $em = $doctrine->getManager();
        $em->remove($equipement);
        $em->flush();
        return $this->redirectToRoute("listEquipement");
    }


    #[Route('/updateEquipement/{id}', name: 'updateEquipement')]
    public function updateEquipement($id, EquipementRepository $repository, ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $equipement = $repository->find($id);
        $form = $this->createForm(EquipementFormType::class, $equipement);
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
                        $this->getParameter('equipement_image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $equipement->setImageEquipement($newFilename);
            }
            $em = $doctrine->getManager();
            $em->flush();
            $this->redirectToRoute("listEquipement");
        }

        return $this->renderForm("equipement/updateEquipement.html.twig", array("f" => $form));
    }







    #[Route('/api/equipements', name: 'api_equipement_list', methods: ['GET'])]
    public function listeqapi(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $equipements = $this->getDoctrine()->getRepository(Equipement::class)->findAll();

        if (empty($equipements)) {
            return new JsonResponse(['message' => 'No found.'], Response::HTTP_OK);
        }

        $data = [];

        foreach ($equipements as $eq) {
            $data[] = [
                'id' => $eq->getId(),
                'nomEquipement' => $eq->getNomEquipement(),
                'imageEquipement' => $eq->getImageEquipement(),


            ];
        }

        $json = $serializer->serialize($data, 'json', ['groups' => 'read', 'max_depth' => 1]);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }


    /* api ajoute equipement */

    #[Route('/addEq/{nomEquipement}/{imageEquipement}', name: 'createl', methods: ['GET'])]
    public function newCategoryPath($nomEquipement, $imageEquipement)
    {
        // create a new produit object with the provided data
        $equipement = new Equipement();
        $equipement->setNomEquipement($nomEquipement);
        $equipement->setImageEquipement($imageEquipement);

        // save the new produit to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($equipement);
        $entityManager->flush();
        // return a response to indicate success
        return new Response('Equipement added with ID: ' . $equipement->getId());
    }

    #[Route('/supprimApiEq/{id}', name: 'suppApi', methods: ['GET'])]
    public function SupprimerEqApi($id, Request $request, EquipementRepository $repository, ManagerRegistry $doctrine): JsonResponse
    {

        $equipement = $repository->find($id);
        $em = $doctrine->getManager();

        /* $idE = $request->get($id);
        $em = $this->getDoctrine()->getManager();
        $equipement = $em->getRepository(Equipement::class)->find($idE);*/
        if ($equipement != null) {
            $em->remove($equipement);
            $em->flush();
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formated = $serializer->normalize("Equipement a ete supprimé avec succées ");
            return new JsonResponse($formated);
        }
    }


    #[Route('/updateEqApi/{id}/{nomEquipement}/{imageEquipement}', name: 'updateE', methods: ['GET'])]
    public function updateMatch($id, $nomEquipement, $imageEquipement, EquipementRepository $repository)
    {

        $equipement = $repository->find($id);
        $equipement->setNomEquipement($nomEquipement);
        $equipement->setImageEquipement($imageEquipement);



        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($equipement);
        $entityManager->flush();

        return new Response('Equipement modified with ID: ' . $equipement->getId());
    }


    #[Route('/listEquipementF', name: 'listEquipementF')]
    public function listSerief(EquipementRepository $rep): Response
    {
        $c = $rep->findAll();
        return $this->render('equipement/listEquipementF.html.twig', [
            'equipements' => $c
        ]);
    }
}
