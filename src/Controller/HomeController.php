<?php

namespace App\Controller;


use App\Entity\Partenaire;
use App\Repository\PartenaireRepository;
use App\Repository\CategorieRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function index(ProductRepository $productRepository,CategoryRepository $categoryRepository,Request $request): Response
    {
        
        return $this->render('home/index1.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $productRepository->findAll(),
            'category' => $categoryRepository->findall(),
            'categories' => $categoryRepository->findAll(),
          
        ]);
    }

    /**
     * @Route("/home/{id}/amine", name="amine")
     */
    public function rendrepar(ProductRepository $repository,$id): Response
    {
       
        $amine= $repository->listepartenaireparcateg($id);
        return $this->render('home/showoneprod.html.twig', [
            'produit' => $amine,
            'categories' => $amine
            
           
        ]);
    }

    
  
}
