<?php

namespace App\Controller;

use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Exercice;
use App\Form\ExerciceFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ExerciceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ExerciceController extends AbstractController
{



    #[Route('/exercice/{id}', name: 'exercice')]
    public function exercice($id, ExerciceRepository $repository): Response
    {
        $exercice = $repository->find($id);
        return $this->render('exercice/exercice.html.twig', [
            'exercice' => $exercice,
        ]);
    }
    #[Route('/addExercice', name: 'addExercice')]
    public function addExercice(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $exercice = new  Exercice();
        $form = $this->createForm(ExerciceFormType::class, $exercice);
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
                        $this->getParameter('exercice_image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $exercice->setImageExercice($newFilename);
            }

            $em = $doctrine->getManager();
            $em->persist($exercice);
            $em->flush();
            return $this->redirectToRoute("listExercice");
        }
        return $this->renderForm("exercice/addExercice.html.twig", array("f" => $form));
    }

    #[Route('/listExercice', name: 'listExercice')]
    public function listExercice(ExerciceRepository $rep): Response
    {
        $c = $rep->findAll();
        return $this->render('exercice/listExercice.html.twig', [
            'exercices' => $c,
        ]);
    }
    #[Route('/listExercicef', name: 'listExercicef')]
    public function listExerciceFront(ExerciceRepository $rep): Response
    {
        $c = $rep->findAll();
        return $this->render('exercice/listExerciceF.html.twig', [
            'exercices' => $c,
        ]);
    }

    #[Route('/listef', name: 'listef')]
    public function listExerciceF(ExerciceRepository $rep): Response
    {
        $cf = $rep->findAll();
        return $this->render('exercice/listExerciceF.html.twig', [
            'exercices' => $cf,
        ]);
    }

    #[Route('/deleteExercice/{id}', name: 'deleteExercice')]
    public function deleteExercice($id, ExerciceRepository $repository, ManagerRegistry $doctrine): Response
    {
        $equipement = $repository->find($id);

        $em = $doctrine->getManager();
        $em->remove($equipement);
        $em->flush();
        return $this->redirectToRoute("listExercice");
    }

    #[Route('/updateExercice/{id}', name: 'updateExercice')]
    public function updateEquipement($id, ExerciceRepository $repository, ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $equipement = $repository->find($id);
        $form = $this->createForm(ExerciceFormType::class, $equipement);
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
                        $this->getParameter('exercice_image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $equipement->setImageExercice($newFilename);
            }
            $em = $doctrine->getManager();
            $em->flush();
            $this->redirectToRoute("listExercice");
        }

        return $this->renderForm("exercice/updateExercice.html.twig", array("f" => $form));
    }



    /* API FOR MOBILE*/

    #[Route('/api/exercices', name: 'api_exercice_list', methods: ['GET'])]
    public function listeqapi(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $equipements = $this->getDoctrine()->getRepository(Exercice::class)->findAll();

        if (empty($equipements)) {
            return new JsonResponse(['message' => 'No found.'], Response::HTTP_OK);
        }

        $data = [];

        foreach ($equipements as $eq) {
            $data[] = [
                'id' => $eq->getId(),
                'nomExercice' => $eq->getNomExercice(),
                'duration' => $eq->getDuration(),
                'repetation' => $eq->getRepetation(),
                'instruction' => $eq->getInstruction()


            ];
        }

        $json = $serializer->serialize($data, 'json', ['groups' => 'read', 'max_depth' => 1]);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/addEx/{nomExercice}/{duration}/{repetation}/{instruction}', name: 'createl', methods: ['GET'])]
    public function newCategoryPath($nomExercice, $duration, $repetation, $instruction)
    {
        // create a new produit object with the provided data
        $equipement = new Exercice();
        $equipement->setNomExercice($nomExercice);
        $duration = intval($duration);
        $equipement->setDuration($duration);
        $repetation = intval($repetation);
        $equipement->setDuration($repetation);
        $equipement->setInstruction($instruction);

        // save the new produit to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($equipement);
        $entityManager->flush();
        // return a response to indicate success
        return new Response('Exercice added with ID: ' . $equipement->getId());
    }
}
