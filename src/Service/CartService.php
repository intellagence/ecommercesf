<?php 

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class CartService
{

    private Session $session;

    public function __construct(private Security $security, private RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }



    public function getCart(): array
    {
        $user = $this->security->getUser();

        if ($user) {
            return [];
        } else {
            return $this->session->get('cart', []);;
        }
    }

    /**
     * Undocumented function
     *
     * @param integer $id
     * @param integer $quantity
     * @return void
     */
    public function addProduct(int $id, int $quantity): void
    {
        $user = $this->security->getUser();

        if ($user) {
            # panier dans la BDD
        } else {
            # panier dans la session
            $cart = $this->getCart();

            // la fonction array_key_exists permet de savoir si une key existe dans un tableau
            $searchKey = array_key_exists($id, $cart);

            if ($searchKey) {
                $cart[$id] += $quantity;
            } else {
                $cart[$id] = $quantity;
            }

            // Enregistrer le tableau $cart dans la key 'cart' de la session
            $this->session->set('cart', $cart);
            
        }
        
    }
}