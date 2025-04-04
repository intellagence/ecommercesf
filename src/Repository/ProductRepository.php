<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * la méthode findAll()
     *
     * @return array
     */
    public function findEverything(): array
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findProductActived($status): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :actived')
            ->setParameter('actived', $status)
            ->getQuery()
            ->getResult()
        ;
    }


    public function findProductFilter(?string $search, ?int $order, ?Collection $categories): array
    {
        $query = $this->createQueryBuilder('p')
            ->join('p.category', 'c')
        ;

        if ($search) {
            $query = $query 
                ->andWhere('p.title LIKE :search')
                ->orWhere('p.price LIKE :search')
                ->setParameter(':search', "%$search%")
            ;
        }

        if ($order) {
            switch ($order) {
                case 1:
                    $query = $query
                        ->orderBy('p.title', 'ASC')
                    ;
                    break;

                case 2:
                    $query = $query
                        ->orderBy('p.title', 'DESC')
                    ;
                    break;

                case 3:
                    $query = $query
                        ->orderBy('p.price', 'ASC')
                    ;
                    break;

                case 4:
                    $query = $query
                        ->orderBy('p.price', 'DESC')
                    ;
                    break;
                
                default:
                    $query = $query
                        ->orderBy('p.id', 'ASC')
                    ;
                    break;
            }
        }

        if ($categories) {

            $categorieIds = [];

            foreach ($categories as $category) {
                $categorieIds[] = $category->getId();
            }

            if ($categorieIds) {
               $query = $query
                    ->andWhere('c.id IN(:categories)')
                    ->setParameter('categories', $categorieIds)
               ;
            }
        }

        return $query
            ->andWhere('p.status = :actived')
            ->setParameter('actived', true)
            ->getQuery()
            ->getResult()
        ;
    }
}
