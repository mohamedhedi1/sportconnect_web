<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\EquipementRepository;

class SearchController extends AbstractController
{
    public function search(Request $request, EquipementRepository $equipementRepository)
    {
        $searchTerm = $request->query->get('q');

        $results = $equipementRepository->searchByName($searchTerm);

        $formattedResults = [];
        foreach ($results as $result) {
            $formattedResults[] = [
                'id' => $result->getId(),
                'name' => $result->getNomEquipement(),
            ];
        }

        return new JsonResponse($formattedResults);
    }
}
