<?php 

namespace App\Service;

use App\Repository\CategoryRepository;

class TabService 
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
        
    }

    public function getCategories(): array
    {
        return $this->categoryRepository->findAll();
    }
}