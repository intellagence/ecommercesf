<?php

namespace App\Controller; // App correspond à 'src'

use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\CommentType;
use App\Form\ProductFilterType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController // héritage de AbstractController
{

    /*
        AUTHENTIFICATION :

        Pour récupérer l'utilisateur connecté :
            - twig : app.user
            - controller : $this->getUser()

        Contrôler les rôles
            - twig : is_granted()
            - controller : isGranted()

        Sécuriser les routes 
            - valeur dans la route (access_control de security.yaml)
            - attribut de la route (controller IsGranted)


    */


    /*
        Une route est définie dans l'url
        en local : 127.0.0.1:8000
        en ligne : www.domaine.fr 

        la dénomination de la route se place après 
        exemple : 127.0.0.1:8000/inscription 

        On a besoin de la Class Route
        syntaxe :
        #[Route('', name:'')]
        1e argument : La route (URL)
        2e argument : Le nom de la route (Les liens)

        Uniquement des simples quotes 
        association se fait par un :

        cette syntaxe de route s'appelle ATTRIBUT (depuis SF6)

        Avant, autre syntaxe : les annotations
        /**
         * @Route("", name="")
         * /
         
        Le fonctionnement de la route :
        quand on accéde à une route (par url ou par lien)
        Cette route va être recherchée dans les controllers
        Si elle n'existe pas : error 404
        Si elle existe : elle va éxecutée par la méthode située juste en dessous

        Cette méthode va retourner une réponse HTTP, c'est-à-dire un fichier 'view' => html.twig
    */
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        //dump($this->getUser());

        
        /*
            La méthode render() provient de la class AbstractController
            la méthode render() rendre/retourner une view (html)
            Cette méthode "se place" à la racine du dossier templates
            1e argument (obligatoire), type string: nom du fichier ainsi que son emplacement dans le dossier templates
            2e argument (facultatif), type array : le tableau des données à transmettre à la view
        */

        $firstNameController = 'bart';

        //dd($firstNameController);

        //dump($firstNameController);


        $fruitsController = ['banane', 'pomme', 'orange', 'fraise'];



        //dump($fruitsController);die;
        

        return $this->render('home/index.html.twig', [
            // k => v
            // k sera le nom de la variable à utiliser sur twig
            // v sera le nom de la variable de la méthode du controller

            'firstNameTwig' => $firstNameController,
            'fruits' => $fruitsController,
            'age' => 19,
        ]);
    }


    #[Route('/catalog', name:'app_catalog')]
    // #[IsGranted('ROLE_ADMIN')]
    public function catalog(ProductRepository $productRepository, Request $request): Response
    {
        // $products = $productRepository->findBy(
        //     ['status' => true ],
        //     ['price' => 'DESC', 'title' => 'DESC'],
        //     5
        // ); 
        // SELECT * FROM product WHERE status = 1

        // findAll() => SELECT * FROM product 
        // find(int $arg) => SELECT * FROM product WHERE id = 
        // findBy() SELECT * FROM product WHERE stock = 2 AND status = 0
        // findOneBy
        // $products = $productRepository->findProductActived(true);

        $form = $this->createForm(ProductFilterType::class);
        $form->handleRequest($request);

        $search = $form->get('search')->getData();
        $order = $form->get('order')->getData();
        $categories = $form->get('categories')->getData();
        // dump($categories);
        

        

        $products = $productRepository->findProductFilter($search, $order, $categories);

        return $this->render('home/catalog.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    #[Route('/catalog/{id}', name:'app_catalog_category')]
    public function catalogCategory(Category $category, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['status' => true, 'category' => $category]); 

        return $this->render('home/catalog_category.html.twig', [
            'products' => $products,
            'category' => $category
        ]);
    }

    #[Route('/product/{id}', name:'app_catalog_product')]
    public function productFiche(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() AND $form->isValid()) {
            
            $comment->setUser($this->getUser());
            $comment->setProduct($product);
            $comment->setCreatedAt(new \DateTimeImmutable('now'));

            $em->persist($comment);
            $em->flush();
            $this->addFlash('success', 'Merci pour votre commentaire, il sera traîté dans les plus brefs délais');
            $this->redirectToRoute('app_catalog_product', ['id' => $product->getId()]);
        }


        return $this->render('home/product.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    

}
