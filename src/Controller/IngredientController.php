<?php

namespace App\Controller;

use App\Entity\IngredientEntity;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Form\IngredientFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\IngredientEntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient')]
    public function index(): Response
    {
        return $this->render('ingredient/index.html.twig', [
            'controller_name' => 'IngredientController',
        ]);
    }

    #[Route('/addingredient', name: 'add_ingredient')]
    public function addIngredient(Request $request): Response
    {
        $ingredient = new IngredientEntity();
        $form = $this->createForm(IngredientFormType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ingredient);
            $entityManager->flush();

            return $this->redirectToRoute('show_ingredient');
        }

        return $this->render('ingredient/addIng.html.twig', [
            'form' => $form->createView(),


        ]);
    }

    #[Route('/showingredient', name: 'show_ingredient')]
    public function showIng(): Response
    {
        $ingredient = $this->getDoctrine()->getRepository(IngredientEntity::class)->findAll();
        return $this->render('ingredient/showIng.html.twig', [
            'ingredient' => $ingredient,

        ]);
    }


    #[Route("/ingredient/edit/{id}", name: "ingredient_edit")]

    public function edit(
        ManagerRegistry $doctrine,
        Request $request,
        $id,
        IngredientEntityRepository $repository
    ) {
        //récupérer l'ingredient à modifier
        $ingredients = $repository->find($id);
        $form = $this->createForm(IngredientFormType::class, $ingredients);
        $form->submit($request->request->get($form->getName()));
        //Action de MAJ
        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->flush();
            /*$this->addFlash('success', 'ingredient updated successfully!');*/
            return $this->redirectToRoute("show_ingredient");
        }
        return $this->renderForm(
            "ingredient/editIng.html.twig",
            ["form" => $form]
        );
    }

    #[Route("/delete/{id}", name: "ingredient_delete")]

    public function delete(ManagerRegistry $doctrine, $id, IngredientEntityRepository $repository)
    {
        $ingredient = $repository->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($ingredient);
        $entityManager->flush();

        return $this->redirectToRoute('show_ingredient');
    }


    #[Route('/Ingredientshow', name: 'show_ingredient')]
public function showNewIngredient(): JsonResponse
{
    $ingredient = $this->getDoctrine()->getRepository(IngredientEntity::class)->findAll();

    if (!$ingredient) {
        $data = [
            'success' => false,
            'message' => 'Ingredients not found',
        ];
        return new JsonResponse($data, 404);
    }

    $data = [
        'success' => true,
        'ingredients' => [],
    ];
    foreach ($ingredient as $i) {
        $data['ingredients'][] = [
            'id' => $i->getId(),
            'name' => $i->getName(),
            'quantite' => $i->getQuantite(),
            'calories' => $i->getCalories(),
        ];
    }
    return new JsonResponse($data);
}



    #[Route('/Ingredient/{name}/{quantite}/{calories}', name: 'new_ingredient', methods: ['GET'])]
    public function addNewIngredient($name, $quantite, $calories): JsonResponse
    {
        // create a new ingredient object with the provided data
        $ingredient = new IngredientEntity();
        $ingredient->setName($name);
        $ingredient->setQuantite($quantite);
        $ingredient->setCalories($calories);

        // save the new ingredient to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($ingredient);
        $entityManager->flush();

        // return a response to indicate success
        return new JsonResponse([
            'success' => true,
            'message' => sprintf('Ingredient %s added successfully', $name)
        ]);
    }

    #[Route("/Ingredient/edit/{id}/{name}/{quantite}/{calories}", name: "update_ingredient")]
    public function editNewIngredient(
        ManagerRegistry $doctrine,
        Request $request,
        $id,
        IngredientEntityRepository $repository
    ): JsonResponse {
        // Get the ingredient to be modified
        $ingredient = $repository->find($id);
        $form = $this->createForm(IngredientFormType::class, $ingredient);
        $form->submit($request->request->get($form->getName()));
        // Updating
        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->flush();

            $data = [
                'success' => true,
                'message' => 'Ingredient updated successfully',
            ];
            return new JsonResponse($data);
        }

        $data = [
            'success' => false,
            'message' => 'Failed to edit ingredient. Invalid data provided.',
        ];
        return new JsonResponse($data, 400);
    }

}
