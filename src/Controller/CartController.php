<?php

namespace App\Controller;

use App\Service\CartService;
use App\Repository\ProductRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/panier')]
final class CartController extends AbstractController
{

    public function __construct(
        private CartService $cartService, 
        private ProductRepository $productRepository
    )
    {
    }
    
    #[Route('/', name: 'app_cart_index')]
    public function index(): Response
    {
        $cart = $this->cartService->getCart();

        return $this->render('cart/index.html.twig', [
            'cart' => $cart
        ]);
    }

    #[Route('/ajouter', name: 'app_cart_add', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $quantity = $request->request->get('quantity');
        $productId = $request->request->get('product');

        $this->cartService->addProduct($productId, $quantity);

        return $this->redirectToRoute('app_cart_index');
    }
}
