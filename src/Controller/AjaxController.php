<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/ajax')]
final class AjaxController extends AbstractController
{
    #[Route('/check/status', name: 'app_ajax_check_status', methods: ['POST'])]
    public function index(Request $request, ProductRepository $productRepository, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);


        $product = $productRepository->find($data['id']);


        if ($product->isStatus()) {
            $product->setStatus(0);
        } else {
            $product->setStatus(1);
        }

        $em->flush();

        $productJson = [
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'prix' => $product->getPrice()
        ];
        
        return new JsonResponse($productJson);

    }


}
