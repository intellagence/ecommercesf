<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/produit')]
/*
    Préfixe des routes,
    Toutes les routes de ce controller commenceront par /produit
*/
final class ProductController extends AbstractController
{

    #[Route('/afficher', name:'app_product_index')]
    public function index(ProductRepository $productRepository): Response
    {
        /*
            Lorsqu'une entity est créée, son repository est généré automatique
            Le Repository d'une Entity permet d'éffectuer des requêtes SELECT

            Cette route va dépendre du ProductRepository

            Dans  les parenthèses de la méthode de la route, on y place des dépendances

            Syntaxe pour appeler des objets en dépendance :
            Class $objet
        */

        $products = $productRepository->findAll(); 
        /*
            findAll() ---> SELECT * FROM product
            La méthode findAll() retourne un tableau
        */
        
        /*
            $product = $productRepository->find(10);
            find(int $id) ----> SELECT * FROM product WHERE id = $id
            La méthode find() retourne un objet Product (ou null)
        */
        //dd($products);
        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/ajouter', name:'app_product_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // Création d'un objet issu de la class Product (entity)
        $product = new Product();

        
        // dump($product);

        /*
            Création du formulaire par le biais de la méthode createForm() provenant de la class AbstractController

            1e argument : nom de la class Type contenant l'objet $builder (le plan de construction du formulaire)
            2e argument : objet issu de la class (Entity)

            Cette méthode retourne un objet de la class FormInterface

        */
        $form = $this->createForm(ProductType::class, $product);

       
        // traitement du formulaire
        $form->handleRequest($request);

        // Si le formulaire a été soumis (click sur le bouton submit)
        // Si le formulaire respecte les conditions/constraints
        if ($form->isSubmitted() && $form->isValid()) {
            // INSERTION EN BDD
            // dump($product);

            /*
                EntityManagerInterface permet d'effectuer les requètes :
                    - INSERT INTO
                    - UPDATE
                    - DELETE
                
                Pour rappel le Modèle
                    - Entity
                    - Repository 
                    - EntityManagerInterface
            */
            $em->persist($product); // INSERT INTO 
            $em->flush();
            // dd($product);

            // notification
            $this->addFlash('success', 'Le produit a bien été ajouté');
            // redirection
            return $this->redirectToRoute('app_product_index');
        }


        return $this->render('product/new.html.twig', [
            'formProduct' => $form->createView(), // création de la vue html du formulaire
        ]);
    }

    #[Route('/fiche/{id}', name:'app_product_show')]
    public function show(Product $product): Response
    {
        /*
            pour récupérer un paramètre de l'url, dans la route, la syntaxe est avec des accolades {}
            la valeur placée est le nom du paramètre 
            si celui-ci est une valeur d'une propriété d'un objet, il faut respecter le nom de cette propriété

            En dépendance, on génère un objet issu de la class Entity, le paramètre de l'url va se positionner sur la propriété qui porte la même dénomination.

            Avec Doctrine, les informations du produit provenant de la BDD seront récupérées dans l'objet

        */
        return $this->render('product/show.html.twig',[
            'product' => $product
        ]);
    }

    #[Route('/modifier/{id}', name:'app_product_edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        /*
            On observe qu'il y a le même 'code' entre ajouter et modifier
            la SEULE différence est que lorsqu'on veut ajouter un produit, on génère un nouvel objet $product (new) alors que lorsqu'on veut modifier un produit on a un objet en dépendance qui est créé via le paramètre dans l'URL
        */
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success', 'Le produit a bien été modifié');
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formProduct' => $form->createView()
        ]);
    }

    #[Route('/supprimer/{id}', name:'app_product_delete')]
    public function delete(Product $product, EntityManagerInterface $em): Response
    {
        $em->remove($product);
        $em->flush();
        $this->addFlash('success', 'Le produit a bien été supprimé');
        return $this->redirectToRoute('app_product_index');
    }
    


}
