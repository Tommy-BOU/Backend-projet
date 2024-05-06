<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Film;
use App\Entity\FilmCategory;
use App\Repository\FilmRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FilmCategFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private FilmRepository $filmRepo, private CategoryRepository $catRepo)
    {
        
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $categories = $this->catRepo->findAll();
        $films = $this->filmRepo->findAll();

        for ($i = 0; $i < 100; $i++) {
            $filmCateg = new FilmCategory();
            $filmCateg->setFilm($films[$i]);
            $filmCateg->setCategory($faker->randomElement($categories));

            $manager->persist($filmCateg);

        }

        for ($i = 0; $i < 100; $i++) {
            $filmCateg = new FilmCategory();
            $filmCateg->setFilm($films[$i]);
            $filmCateg->setCategory($faker->randomElement($categories));

            $manager->persist($filmCateg);

        }


        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            FilmFixtures::class,
        ];
    }
}
