<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Film;
use App\Repository\FilmRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FilmFixtures extends Fixture
{
    public function __construct(private FilmRepository $filmRepo, private CategoryRepository $catRepo)
    {
        
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $categories = $this->catRepo->findAll();

        for ($i = 0; $i < 100; $i++) {
            $film = new Film();
            $film->setTitle($faker->words(2, true));
            $film->setSummary($faker->realText);
            $film->setReleaseYear($faker->numberBetween(1900, 2024));
            $film->setRealisator($faker->name);

            $manager->persist($film);
        }


        $manager->flush();
    }
}
