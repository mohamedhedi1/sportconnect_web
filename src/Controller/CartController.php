<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;


class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="app_cart")
     */
    public function index(SessionInterface $session,ProductRepository $productRepository): Response
    {
        $panier = $session->get('panier',[]);

        $panierWithData =[];

        foreach($panier as $id => $quantity){

        $panierWithData[] =[

            'product' => $productRepository->find($id),
            'quantity' => $quantity
        ];

        }

        $total = 0;
      
        foreach($panierWithData as $item){


            $totalitem =$item['product']->getPrix() * $item['quantity'];
            $total += $totalitem;

        }


        return $this->render('cart/index.html.twig', [
            'items' =>$panierWithData,
            'total' =>$total

        ]);
    }

     /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function add($id,SessionInterface $session): Response
    {
        
        $panier = $session->get('panier ',[]);

        if(!empty($panier[$id])){
            $panier[$id];

        }else {

            $panier[$id] = 1;
        }

        
        $session->set('panier',$panier);
        dd($session->get('panier'));

    }


 /**
     * @Route("/panierrr", name="app_cartte")
     */
    public function index1(Request $request,ProductRepository $productRepository)
    {

        $nombre= $productRepository->nb();

    $cart = $request->getSession()->get('cart', []);
    
    $panierWithData=[];

   

    foreach($cart as $id  => $quantity){

            $panierWithData[] =[

                'product' => $productRepository->find($id),
                'quantity' => 1
            ]; 

    }
     $total = 0;
      
    foreach($panierWithData as $item){


        $totalitem =$item['product']->getPrix();

     
        $total += $totalitem;

    }
  



    return $this->render('cart/index.html.twig', [
        'cart' => $panierWithData,
        'total' =>$total,
        'nombre' =>$nombre,
        
    ]);

    }



    /**
     * @Route("/panier/addToCart/{id}", name="addToCart")
     */
    public function addToCart(Request $request, $id,ProductRepository $productRepository)
    {
        $product = $productRepository->find($id);
        
        // Vérifiez que le produit existe
        if (!$product) {
            throw $this->createNotFoundException('Le produit n\'existe pas.');
        }
        
        // Ajoutez le produit dans le panier
        $cart = $request->getSession()->get('cart', []);
        $cart[$product->getId()] = [
            'nom' => $product->getNom(),
            'prix' => $product->getPrix(),
            'quantity' => 1,
        ];
        
        $request->getSession()->set('cart', $cart);
        
        // Redirigez l'utilisateur vers la page du panier
        return $this->redirectToRoute('app_cartte');
    }
 

   
     /**
     * @Route("/panier/supprimer/{id}", name="supprimerToCart")
     */

public function removeProductFromCart(Request $request,SessionInterface $session, $id)
{
    $cart = $session->get('cart', []);
    
    // Vérifiez que le produit est déjà dans le panier
    if (!empty($cart[$id])) {
        unset($cart[$id]);
    }
    
    // Supprimez le produit du panier

    $session->set('cart',$cart);
    
    return $this->redirectToRoute('app_cartte');
    
}

}