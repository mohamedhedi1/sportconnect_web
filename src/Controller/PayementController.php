<?php

namespace App\Controller;


use Stripe\Checkout\Session;
use App\Services\cart\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

class PayementController extends AbstractController
{
   /**
     * @Route("/payment", name="payment")
     */
    public function index(): Response
    {
        return $this->render('payement/index.html.twig', []);
    }

    /**
     * @Route("/checkout", name="checkout")
     */
    public function chekout(CartService $cartService): Response
    {

        \Stripe\Stripe::setApiKey('sk_test_51MhwylE5PS2f81Pv7xz0ERxhXyt6LzGe41ptB3nowDxWAKcvIQF6n4AJJQwjs07GPxTNOAPPZCLp8tFyQ2ZMszEK00tHj8EjjL');

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Total Commande',
                    ],
                    'unit_amount' => $cartService->getTotal(),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('error', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        return $this->redirect($session->url);


    }


    /**
     * @Route("/error", name="error")
     */
    public function error()
    {
        return $this->render('payement/Error.html.twig');
    }

    /**
     * @Route("/success", name="success")
     */
    public function success()
    {
        return $this->render('payement/Success.html.twig');
    }

}
