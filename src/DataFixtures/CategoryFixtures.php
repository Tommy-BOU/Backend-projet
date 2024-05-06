<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixtures extends Fixture
{
    public function __construct(private CategoryRepository $catRepo)
    {
        
    }
    public function load(ObjectManager $manager): void
    {
        $categs = ['action', 'aventure', 'comédie', 'thriller', 'science fiction', 'romance', 'fantastique', 'historique', 'drame', 'jeunesse', 'horreur', 'guerre', 'suspens'];

        for ($i = 0; $i < 12; $i++) {
        
            $category = new Category();
            $category->setLabel($categs[$i]);
            $manager->persist($category);
        
        }

        $manager->flush();
    }
}
